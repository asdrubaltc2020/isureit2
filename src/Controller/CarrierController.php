<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Carrier;
use App\Entity\Customer;
use App\Entity\Role;
use App\Form\AgentType;
use App\Form\CarrierType;
use App\Form\CustomerType;
use App\Form\RoleType;
use App\Repository\AgentRepository;
use App\Repository\CarrierRepository;
use App\Repository\CustomerRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/carriers',)]
class CarrierController extends AbstractController{

    private $em;
    /**
     * UserController constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/list', name: 'list_carrier', methods: ["GET", "POST"])]
    public function carrierList(PaginatorInterface $paginator, Request $request, CarrierRepository $repository): Response
    {
        $searh = "";
        $carriers = $repository->getAll();
        if ($request->isMethod('POST')) {
            $searh = $request->get('search');
            if ($searh != "") {
                $carriers = $repository->findByExampleField($searh);
            }
        }
        $actions = [
            [
                "id" => 0,
                "name" => "Delete",
            ]
        ];
        $pagination = $paginator->paginate(
            $carriers,
            $request->query->getInt('page', 1),
            25,
            array('wrap-queries'=>true)
        );
        $delete_form_ajax = $this->createCustomForm('CARRIER_ID', 'DELETE', 'delete_carrier');
        return $this->render('carrier/index.html.twig', [
            'pagination' => $pagination, "actions" => $actions, 'searh' => $searh, 'delete_form_ajax' => $delete_form_ajax->createView()
        ]);
    }

    #[Route('/new', name: 'add_carrier')]
    public function addCarrier(Request $request): Response
    {
        $carrier = new Carrier();
        $form = $this->createForm(CarrierType::class, $carrier);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $agents = $request->get('carrier')['agents'];
            foreach ($agents as $agent_id) {
                $agent = $this->em->getRepository(Agent::class)->find($agent_id);
                $agent->addAgentCarrier($carrier);
            }

            $this->em->persist($agent);
            $this->em->persist($carrier);
            $this->em->flush();

            $this->addFlash('success', 'Carrier Created!');
            return $this->redirectToRoute('list_carrier');
        }

        return $this->render('carrier/add.html.twig', ['form' => $form->createView(), 'action' => 'New']);
    }

    #[Route('/edit/{id}', name: 'edit_carrier', defaults: ['id' => null])]
    public function edit(Request $request, $id): Response
    {

        $carrier = $this->em->getRepository('App\Entity\Carrier')->find($id);
        $agents_old = $carrier->getAgents();
        $agents_old_id = [];
        foreach ($agents_old as $agent_old) {
            $agents_old_id[] = $agent_old;
        }

        $form = $this->createForm(CarrierType::class, $carrier);

        if ($carrier == null) {
            return $this->redirectToRoute('list_customer');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($agents_old_id as $agent_old) {
                $agent = $this->em->getRepository(Agent::class)->find($agent_old);
                $agent->removeAgentCarrier($carrier);
            }

            $agents = $request->get('carrier')['agents'];
            foreach ($agents as $agent_id) {
                $agent = $this->em->getRepository(Agent::class)->find($agent_id);
                $agent->addAgentCarrier($carrier);
            }

            $this->em->persist($agent);

            $this->em->persist($carrier);
            $this->em->flush();

            $this->addFlash('success', 'Carrier Updated!');
            return $this->redirectToRoute('list_carrier');
        }

        return $this->render('carrier/add.html.twig', ['form' => $form->createView(), 'action' => 'Edit']);
    }

    #[Route('/delete', name: 'delete_carrier', methods: ["POST", "DELETE"])]
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');

        $carrier = $this->em->getRepository('App\Entity\Carrier')->find($id);
        $removed = 0;
        $message = "";

        if ($carrier) {
            try {
                $this->em->remove($carrier);
                $this->em->flush();
                $removed = 1;
                $message = "The Carrier has been Successfully removed";
            } catch (Exception $ex) {
                $removed = 0;
                $message = "The Carrier can't be removed";
            }
        }

        return new Response(
            json_encode(array('removed' => $removed, 'message' => $message)), 200, array('Content-Type' => 'application/json')
        );
    }

    /**
     * @Route("/delete_multiple", name="delete_multiple_carrier",methods={"POST","DELETE"})
     */
    public function deleteMultipleAction(Request $request)
    {

        $ids = $request->get('ids');
        $removed = 0;
        $message = "";

        foreach ($ids as $id) {
            $carrier = $this->em->getRepository('App\Entity\Carrier')->find($id);

            if ($carrier) {
                try {
                    $this->em->remove($carrier);
                    $this->em->flush();
                    $removed = 1;
                    $message = "The Carriers has been removed Successfully";
                } catch (Exception $ex) {
                    $removed = 0;
                    $message = "The Carriers can't be removed";
                }
            }
        }

        return new Response(
            json_encode(array('removed' => $removed, 'message' => $message)), 200, array('Content-Type' => 'application/json')
        );
    }

    private function createCustomForm($id, $method, $route)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod($method)
            ->getForm();
    }
}
