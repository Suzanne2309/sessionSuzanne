<?php

namespace App\Controller;

use App\Repository\ProgrammeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProgrammeController extends AbstractController
{
    #[Route('/programme', name: 'app_programme')]
    public function index(ProgrammeRepository $programmeRepository): Response
    {

        $programmes = $programmeRepository->findBy([], ['session' => 'ASC']);

        return $this->render('programme/index.html.twig', [
            'programmes' => $programmes,
        ]);
    }
}
