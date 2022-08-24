<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\Role;
use App\Form\AgentType;
use App\Form\CustomerType;
use App\Form\RoleType;
use App\Repository\AgentRepository;
use App\Repository\CustomerRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customers',)]
class CustomerController extends AbstractController
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

    #[Route('/list', name: 'list_customer', methods: ["GET","POST"])]
    public function customerList(PaginatorInterface $paginator, Request $request, CustomerRepository $repository): Response {

        $searh = "";
        $agents = $repository->getAll();
        if($request->isMethod('POST')){
            $searh=$request->get('search');
            if($searh!=""){
                $agents=$repository->findByExampleField($searh);
            }
        }

        $actions=[
            [
                "id"=>0,
                "name"=>"Delete",
            ]
        ];

        $pagination = $paginator->paginate(
            $agents,
            $request->query->getInt('page', 1),
            25
        );

        $delete_form_ajax = $this->createCustomForm('CUSTOMER_ID', 'DELETE', 'delete_customer');

        return $this->render('customer/index.html.twig', [
            'pagination' => $pagination, "actions"=>$actions, 'searh'=>$searh, 'delete_form_ajax' => $delete_form_ajax->createView()
        ]);
    }

    #[Route('/new', name: 'add_customer')]
    public function addCustomer(Request $request): Response{
        $customer = new Customer();
        $form=$this->createForm(CustomerType::class, $customer);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($customer);
            $this->em->flush();

            $this->addFlash('success', 'Customer Created!');
            return $this->redirectToRoute('list_customer');
        }

        return $this->render('customer/add.html.twig', ['form'=>$form->createView(),'action'=>'New']);
    }

    #[Route('/edit/{id}', name: 'edit_customer', defaults:['id'=>null])]
    public function edit(Request $request, $id):Response {

        $customer = $this->em->getRepository('App\Entity\Customer')->find($id);
        $form=$this->createForm(CustomerType::class, $customer);

        if($customer==null){
            return $this->redirectToRoute('list_customer');
        }

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($customer);
            $this->em->flush();

            $this->addFlash('success', 'Customer Updated!');
            return $this->redirectToRoute('list_customer');
        }

        return $this->render('customer/add.html.twig', ['form'=>$form->createView(),'action'=>'Edit']);
    }

    #[Route('/delete', name: 'delete_customer', methods: ["POST","DELETE"])]
    public function deleteAction(Request $request) {
        $id = $request->get('id');

        $user = $this->em->getRepository('App\Entity\Customer')->find($id);
        $removed = 0;
        $message = "";

        if ($user) {
            try {
                $this->em->remove($user);
                $this->em->flush();
                $removed = 1;
                $message = "The Customer has been Successfully removed";
            } catch (Exception $ex) {
                $removed = 0;
                $message = "The Customer can't be removed";
            }
        }

        return new Response(
            json_encode(array('removed' => $removed, 'message' => $message)), 200, array('Content-Type' => 'application/json')
        );
    }

    /**
     * @Route("/delete_multiple", name="delete_multiple_agent",methods={"POST","DELETE"})
     */
    public function deleteMultipleAction(Request $request) {

        $ids = $request->get('ids');
        $removed = 0;
        $message = "";

        foreach ($ids as $id) {
            $agent  = $this->em->getRepository('App\Entity\Customer')->find($id);

            if ($agent) {
                try {
                    $this->em->remove($agent);
                    $this->em->flush();
                    $removed = 1;
                    $message = "The Customers has been removed Successfully";
                } catch (Exception $ex) {
                    $removed = 0;
                    $message = "The Customers can't be removed";
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
