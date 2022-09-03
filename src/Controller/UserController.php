<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/users')]
class UserController extends AbstractController
{
    private $em;
    private $log_service;

    public function __construct(EntityManagerInterface $em, LogService $log_service)
    {
        $this->em = $em;
        $this->log_service = $log_service;
    }

    #[Route('/list', name: 'list_user', methods: ["GET","POST"])]
    public function index(UserRepository $repository, TranslatorInterface $translator, PaginatorInterface $paginator, Request $request): Response
    {
        $searh="";
        $users=$repository->getAll();
        if($request->isMethod('POST')){
            $searh=$request->get('search');
            if($searh!=""){
                $users=$repository->findByExampleField($searh);
            }
        }

        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            10
        );

        $delete_form_ajax = $this->createCustomForm('USER_ID', 'DELETE', 'delete_role');

        $actions=[
            [
                "id"=>0,
                "name"=>"delete"
            ]
        ];

        $this->log_service->add('List',null, 'User');

        return $this->render('user/index.html.twig', [
            'pagination' => $pagination,'delete_form_ajax' => $delete_form_ajax->createView(), "actions"=>$actions, 'searh'=>$searh
        ]);
    }

    #[Route('/new', name: 'add_user')]
    public function add(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user=new User();

        $form=$this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user->setRoles(['ROLE_USER']);
            $password=$form->get('password')->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );

            $user->setPassword($hashedPassword);
            $this->em->persist($user);
            $this->em->flush();

            $this->log_service->add('Add',$user->getId(), 'User');

            return $this->redirectToRoute('list_user');
        }

        return $this->render('user/add.html.twig', [
            'form'=>$form->createView(), 'action'=>'New'
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_user', defaults: ["id"=>null],  methods: ["GET", "POST"])]
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher, $id):Response {

        $user=$this->em->getRepository(User::class)->find($id);

        $form=$this->createForm(UserEditType::class,$user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($user);
            $this->em->flush();

            $this->log_service->add('Edit',$user->getId(), 'User');
            $this->addFlash('success', 'User Updated!');
            return $this->redirectToRoute('user');
        }

        return $this->render('user/add.html.twig',['action'=>'Edit','form'=>$form->createView()]);
    }

    #[Route('/view/{id}', name: 'view_user', defaults: ["id"=>null],  methods: ["GET"])]
    public function view($id):Response {

        $user=$this->em->getRepository(User::class)->find($id);

        $role=null;
        if($user->hasRole('ROLE_AGENT')){
            $role='Agent';
        }

        return $this->render('user/view.html.twig',['user'=>$user,'action'=>'View' ,'role'=>$role]);
    }

    #[Route('/delete', name: 'delete_user', methods: ["POST","DELETE"])]
    public function deleteAction(Request $request) {
        $id = $request->get('id');

        $user = $this->em->getRepository('App\Entity\User')->find($id);
        $removed = 0;
        $message = "";

        if ($user) {
            try {
                $this->em->remove($user);
                $this->em->flush();
                $removed = 1;

                $this->log_service->add('Delete',$user->getId(), 'User');

                $message = "The User has been Successfully removed";
            } catch (Exception $ex) {
                $removed = 0;
                $message = "The User can't be removed";
            }
        }

        return new Response(
            json_encode(array('removed' => $removed, 'message' => $message)), 200, array('Content-Type' => 'application/json')
        );
    }

    /**
     * @Route("/delete_multiple", name="delete_multiple_user",methods={"POST","DELETE"})
     */
    public function deleteMultipleAction(Request $request) {

        $ids = $request->get('ids');
        $removed = 0;
        $message = "";

        foreach ($ids as $id) {
            $user  = $this->em->getRepository('App\Entity\User')->find($id);

            if ($user) {
                try {
                    $this->em->remove($user);
                    $this->em->flush();
                    $removed = 1;

                    $this->log_service->add('Delete',$user->getId(), 'User');

                    $message = "The Users has been removed Successfully";
                } catch (Exception $ex) {
                    $removed = 0;
                    $message = "The Users can't be removed";
                }
            }
        }

        return new Response(
            json_encode(array('removed' => $removed, 'message' => $message)), 200, array('Content-Type' => 'application/json')
        );
    }


    private function createCustomForm($id, $method, $route) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod($method)
            ->getForm();
    }

    public function userMenu(Request $request) {

        $user=$this->getUser();
        $full_name='';
        $email='';

        if($user!=null){
            $full_name=$user->getFullName();
            $email=$user->getEmail();
        }


        $locale = $request->getLocale();

        $current_language="English";
        if($locale=='es'){
            $current_language="Español";
        }

        return $this->render('user-menu.html.twig',['full_name'=>$full_name,'email'=>$email,'current_language'=>$current_language]);
    }
}
