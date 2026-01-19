<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    #[Route('/change_locale/{locale}', name: 'app_change_locale')]
    public function changeLocale($locale, Request $request): Response
    {
        // Store the locale in the session
        $request->getSession()->set('_locale', $locale);
        $request->setLocale($locale);

        // Go back to the previous page
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_home'));
    }
}
