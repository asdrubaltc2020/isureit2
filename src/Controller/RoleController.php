<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/roles',)]
class RoleController extends AbstractController
{

    private $em;

    /**
     * UserController constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/list', name: 'list_role', methods: ["GET","POST"])]
    public function roleList(PaginatorInterface $paginator, Request $request, RoleRepository $repository): Response {

        $searh = "";
        $roles = $repository->getAll();
        if($request->isMethod('POST')){
            $searh=$request->get('search');
            if($searh!=""){
                $roles=$repository->findByExampleField($searh);
            }
        }

        $actions=[
            [
                "id"=>0,
                "name"=>"Delete",
                "path"=>"delete_multiple_role"
            ]
        ];

        $pagination = $paginator->paginate(
            $roles,
            $request->query->getInt('page', 1),
            25
        );

        $delete_form_ajax = $this->createCustomForm('ROLE_ID', 'DELETE', 'delete_role');

        return $this->render('role/index.html.twig', [
            'pagination' => $pagination, "actions"=>$actions, 'searh'=>$searh, 'delete_form_ajax' => $delete_form_ajax->createView()
        ]);
    }

    #[Route('/new', name: 'add_role')]
    public function addRole(Request $request): Response{
        $role=new Role();
        $form=$this->createForm(RoleType::class, $role);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($role);
            $this->em->flush();

            $this->addFlash('success', 'Role Created!');
            return $this->redirectToRoute('list_role');
        }

        return $this->render('role/add.html.twig', ['form'=>$form->createView(),'action'=>'New']);
    }

    #[Route('/edit/{id}', name: 'edit_role', defaults:['id'=>null])]
    public function edit(Request $request, $id):Response {

        $role = $this->em->getRepository('App\Entity\Role')->find($id);
        $form=$this->createForm(RoleType::class, $role);

        if($role==null){
            return $this->redirectToRoute('list_role');
        }

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($role);
            $this->em->flush();

            $this->addFlash('success', 'Role Updated!');
            return $this->redirectToRoute('list_role');
        }

        return $this->render('role/add.html.twig', ['form'=>$form->createView(),'action'=>'Edit']);
    }


    /*#[Route('/delete/{id}', name: 'delete_role')]
    public function deleteAction($id) {
        $role = $this->em->getRepository('App\Entity\Role')->find($id);

        $removed = 0;
        $message = "";

        if ($role) {
            try {
                $this->em->remove($role);
                $this->em->flush();
                $removed = 1;
                $message = "The Role has been removed Successfully ";
            } catch (Exception $ex) {
                $removed = 0;
                $message = "The Role can't be removed";
            }
        }
        return $this->redirectToRoute('list_role');
    }*/

    #[Route('/delete', name: 'delete_role', methods: ["POST","DELETE"])]
    public function deleteAction(Request $request) {
        $id = $request->get('id');

        $user = $this->em->getRepository('App\Entity\Role')->find($id);
        $removed = 0;
        $message = "";

        if ($user) {
            try {
                $this->em->remove($user);
                $this->em->flush();
                $removed = 1;
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
     * @Route("/delete_multiple", name="delete_multiple_role",methods={"POST","DELETE"})
     */
    public function deleteMultipleAction(Request $request) {

        $ids = $request->get('ids');
        $removed = 0;
        $message = "";

        foreach ($ids as $id) {
            $role  = $this->em->getRepository('App\Entity\Role')->find($id);

            if ($role) {
                try {
                    $this->em->remove($role);
                    $this->em->flush();
                    $removed = 1;
                    $message = "The Roles has been removed Successfully";
                } catch (Exception $ex) {
                    $removed = 0;
                    $message = "The Roles can't be removed";
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
}
