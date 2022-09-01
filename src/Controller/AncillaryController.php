<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Ancillary;
use App\Entity\Carrier;
use App\Entity\Customer;
use App\Entity\Role;
use App\Form\AgentType;
use App\Form\AncillaryType;
use App\Form\CarrierType;
use App\Form\CustomerType;
use App\Form\RoleType;
use App\Repository\AgentRepository;
use App\Repository\AncillaryRepository;
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

#[Route('/ancillaries',)]
class AncillaryController extends AbstractController{

    private $em;
    /**
     * UserController constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/list', name: 'list_ancillary', methods: ["GET", "POST"])]
    public function ancillaryList(PaginatorInterface $paginator, Request $request, AncillaryRepository $repository): Response
    {
        $searh = "";
        $ancillaries = $repository->getAll();
        if ($request->isMethod('POST')) {
            $searh = $request->get('search');
            if ($searh != "") {
                $ancillaries = $repository->findByExampleField($searh);
            }
        }
        $actions = [
            [
                "id" => 0,
                "name" => "Delete",
            ]
        ];
        $pagination = $paginator->paginate(
            $ancillaries,
            $request->query->getInt('page', 1),
            25,
            array('wrap-queries'=>true)
        );
        $delete_form_ajax = $this->createCustomForm('ANCILLARY_ID', 'DELETE', 'delete_ancillary');
        return $this->render('ancillary/index.html.twig', [
            'pagination' => $pagination, "actions" => $actions, 'searh' => $searh, 'delete_form_ajax' => $delete_form_ajax->createView()
        ]);
    }

    #[Route('/new', name: 'add_ancillary')]
    public function addAncillary(Request $request): Response
    {
        $ancillary = new Ancillary();
        $form = $this->createForm(AncillaryType::class, $ancillary);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($ancillary);
            $this->em->flush();

            $this->addFlash('success', 'Ancillary Created!');
            return $this->redirectToRoute('list_ancillary');
        }

        return $this->render('ancillary/add.html.twig', ['form' => $form->createView(), 'action' => 'New', 'ancillary' => $ancillary]);
    }

    #[Route('/edit/{id}', name: 'edit_ancillary', defaults: ['id' => null])]
    public function editAncillary(Request $request, $id): Response
    {
        $ancillary = $this->em->getRepository('App\Entity\Ancillary')->find($id);
        $form = $this->createForm(AncillaryType::class, $ancillary);

        if ($ancillary == null) {
            return $this->redirectToRoute('list_ancillary');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($ancillary);
            $this->em->flush();

            $this->addFlash('success', 'Ancillary Updated!');
            return $this->redirectToRoute('list_ancillary');
        }

        return $this->render('ancillary/add.html.twig', ['form' => $form->createView(), 'action' => 'Edit', 'ancillary' => $ancillary]);
    }

    #[Route('/delete', name: 'delete_ancillary', methods: ["POST", "DELETE"])]
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');

        $ancillary = $this->em->getRepository('App\Entity\Ancillary')->find($id);
        $removed = 0;
        $message = "";

        if ($ancillary) {
            try {
                $this->em->remove($ancillary);
                $this->em->flush();
                $removed = 1;
                $message = "The Ancillary has been Successfully removed";
            } catch (Exception $ex) {
                $removed = 0;
                $message = "The Ancillary can't be removed";
            }
        }

        return new Response(
            json_encode(array('removed' => $removed, 'message' => $message)), 200, array('Content-Type' => 'application/json')
        );
    }

    /**
     * @Route("/delete_multiple", name="delete_multiple_ancillary",methods={"POST","DELETE"})
     */
    public function deleteMultipleAction(Request $request)
    {

        $ids = $request->get('ids');
        $removed = 0;
        $message = "";

        foreach ($ids as $id) {
            $ancillary = $this->em->getRepository('App\Entity\Ancillary')->find($id);

            if ($ancillary) {
                try {
                    $this->em->remove($ancillary);
                    $this->em->flush();
                    $removed = 1;
                    $message = "The Ancillary has been removed Successfully";
                } catch (Exception $ex) {
                    $removed = 0;
                    $message = "The Ancillary can't be removed";
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
