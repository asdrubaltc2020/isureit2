<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users')]
class UserController extends AbstractController
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


    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
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
