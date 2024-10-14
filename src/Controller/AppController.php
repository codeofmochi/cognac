<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController {
    #[Route('/', name: 'root')]
    public function root() {
        return $this->redirectToRoute('app');
    }

    #[Route('/app', name: 'app')]
    public function serveApp() {
        return $this->render('app.html.twig');
    }
}