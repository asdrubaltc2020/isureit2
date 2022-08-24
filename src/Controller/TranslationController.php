<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[Route('/admin/translation')]
class TranslationController extends AbstractController
{

    #[Route('/change-locale/{locale}', name: 'app_change_locale_translation')]
    public function setLocale(Request $request, $locale): Response
    {

       $request->getSession()->set('_locale',$locale);

       return $this->redirect($request->headers->get('referer'));
    }
}
