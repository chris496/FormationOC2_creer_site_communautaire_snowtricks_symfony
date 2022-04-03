<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TricksController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TricksRepository $repository): Response
    {
        $allTricks = $repository->findAll();
        return $this->render('tricks/index.html.twig', [
            'allTricks' => $allTricks
        ]);
    }
}
