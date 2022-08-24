<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Role;
use App\Form\AgentType;
use App\Form\RoleType;
use App\Repository\AgentRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/agents',)]
class AgentController extends AbstractController
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

    #[Route('/list', name: 'list_agent', methods: ["GET","POST"])]
    public function agentList(PaginatorInterface $paginator, Request $request, AgentRepository $repository): Response {

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

        $delete_form_ajax = $this->createCustomForm('AGENT_ID', 'DELETE', 'delete_agent');

        return $this->render('agent/index.html.twig', [
            'pagination' => $pagination, "actions"=>$actions, 'searh'=>$searh, 'delete_form_ajax' => $delete_form_ajax->createView()
        ]);
    }

    #[Route('/new', name: 'add_agent')]
    public function addAgent(Request $request): Response{
        $agent=new Agent();
        $form=$this->createForm(AgentType::class, $agent);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($agent);
            $this->em->flush();

            $this->addFlash('success', 'Agent Created!');
            return $this->redirectToRoute('list_agent');
        }

        return $this->render('agent/add.html.twig', ['form'=>$form->createView(),'action'=>'New']);
    }

    #[Route('/edit/{id}', name: 'edit_agent', defaults:['id'=>null])]
    public function edit(Request $request, $id):Response {

        $agent = $this->em->getRepository('App\Entity\Agent')->find($id);
        $form=$this->createForm(AgentType::class, $agent);

        if($agent==null){
            return $this->redirectToRoute('list_agent');
        }

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($agent);
            $this->em->flush();

            $this->addFlash('success', 'Agent Updated!');
            return $this->redirectToRoute('list_agent');
        }

        return $this->render('agent/add.html.twig', ['form'=>$form->createView(),'action'=>'Edit']);
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

    #[Route('/delete', name: 'delete_agent', methods: ["POST","DELETE"])]
    public function deleteAction(Request $request) {
        $id = $request->get('id');

        $user = $this->em->getRepository('App\Entity\Agent')->find($id);
        $removed = 0;
        $message = "";

        if ($user) {
            try {
                $this->em->remove($user);
                $this->em->flush();
                $removed = 1;
                $message = "The Agent has been Successfully removed";
            } catch (Exception $ex) {
                $removed = 0;
                $message = "The Agent can't be removed";
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
            $agent  = $this->em->getRepository('App\Entity\Agent')->find($id);

            if ($agent) {
                try {
                    $this->em->remove($agent);
                    $this->em->flush();
                    $removed = 1;
                    $message = "The Agents has been removed Successfully";
                } catch (Exception $ex) {
                    $removed = 0;
                    $message = "The Agents can't be removed";
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
