<?php

namespace App\Controller;

use App\Repository\LogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/logs')]
class LogController extends AbstractController
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

    #[Route('/list', name: 'app_log_list',  methods: ["GET","POST"])]
    public function index(LogRepository $repository,  PaginatorInterface $paginator, Request $request): Response
    {

        $searh="";
        $logs=$repository->getAll();

        if($request->isMethod('POST')){
            $searh=$request->get('search');
            if($searh!=""){
                $logs=$repository->findByExampleField($searh);
            }
        }

        $pagination = $paginator->paginate(
            $logs,
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


        return $this->render('log/index.html.twig', [
            'pagination' => $pagination,'delete_form_ajax' => $delete_form_ajax->createView(), "actions"=>$actions, 'searh'=>$searh
        ]);
    }

    private function createCustomForm($id, $method, $route) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod($method)
            ->getForm();
    }
}
