<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\agency;
use App\Entity\Carrier;
use App\Entity\Customer;
use App\Entity\Role;
use App\Form\AgentType;
use App\Form\AgencyType;
use App\Form\CarrierType;
use App\Form\CustomerType;
use App\Form\RoleType;
use App\Repository\AgentRepository;
use App\Repository\AgencyRepository;
use App\Repository\CarrierRepository;
use App\Repository\CustomerRepository;
use App\Repository\RoleRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/agencies',)]
class AgencyController extends AbstractController{

    private $em;
    /**
     * UserController constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/list', name: 'list_agency', methods: ["GET", "POST"])]
    public function agencyList(PaginatorInterface $paginator, Request $request, AgencyRepository $repository): Response
    {
        $searh = "";
        $agencies = $repository->getAll();
        if ($request->isMethod('POST')) {
            $searh = $request->get('search');
            if ($searh != "") {
                $agencies = $repository->findByExampleField($searh);
            }
        }
        $actions = [
            [
                "id" => 0,
                "name" => "Delete",
            ]
        ];
        $pagination = $paginator->paginate(
            $agencies,
            $request->query->getInt('page', 1),
            25,
            array('wrap-queries'=>true)
        );
        $delete_form_ajax = $this->createCustomForm('AGENCY_ID', 'DELETE', 'delete_agency');
        return $this->render('agency/index.html.twig', [
            'pagination' => $pagination, "actions" => $actions, 'searh' => $searh, 'delete_form_ajax' => $delete_form_ajax->createView()
        ]);
    }

    #[Route('/new', name: 'add_agency')]
    public function addAgency(Request $request): Response
    {
        $agency = new agency();
        $form = $this->createForm(AgencyType::class, $agency);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($agency);
            $this->em->flush();

            $this->addFlash('success', 'Agency Created!');
            return $this->redirectToRoute('list_agency');
        }

        return $this->render('agency/add.html.twig', ['form' => $form->createView(), 'action' => 'New', 'agency' => $agency]);
    }

    #[Route('/edit/{id}', name: 'edit_agency', defaults: ['id' => null])]
    public function editAgency(Request $request, $id): Response
    {
        $agency = $this->em->getRepository('App\Entity\Agency')->find($id);
        $form = $this->createForm(AgencyType::class, $agency);

        if ($agency == null) {
            return $this->redirectToRoute('list_agency');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($agency);
            $this->em->flush();

            $this->addFlash('success', 'Agency Updated!');
            return $this->redirectToRoute('list_agency');
        }

        return $this->render('agency/add.html.twig', ['form' => $form->createView(), 'action' => 'Edit', 'agency' => $agency]);
    }

    #[Route('/delete', name: 'delete_agency', methods: ["POST", "DELETE"])]
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');

        $agency = $this->em->getRepository('App\Entity\Agency')->find($id);
        $removed = 0;
        $message = "";

        if ($agency) {
            try {
                $this->em->remove($agency);
                $this->em->flush();
                $removed = 1;
                $message = "The Agency has been Successfully removed";
            } catch (Exception $ex) {
                $removed = 0;
                $message = "The Agency can't be removed";
            }
        }

        return new Response(
            json_encode(array('removed' => $removed, 'message' => $message)), 200, array('Content-Type' => 'application/json')
        );
    }

    /**
     * @Route("/delete_multiple", name="delete_multiple_agency",methods={"POST","DELETE"})
     */
    public function deleteMultipleAction(Request $request)
    {

        $ids = $request->get('ids');
        $removed = 0;
        $message = "";

        foreach ($ids as $id) {
            $agency = $this->em->getRepository('App\Entity\Agency')->find($id);

            if ($agency) {
                try {
                    $this->em->remove($agency);
                    $this->em->flush();
                    $removed = 1;
                    $message = "The Agencies has been removed Successfully";
                } catch (Exception $ex) {
                    $removed = 0;
                    $message = "The Agencies can't be removed";
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
