<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response
    {

        $formations = $formationRepository->findBy([], ['title' => 'ASC']);

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/formation/add', name: 'add_formation')]
    #[Route('/formation/{id}/edit', name: 'edit_formation')]
    public function add_editFormation(Formation $formation = null, Request $request, EntityManagerInterface $entityManager): Response
    {

        if(!$formation) {
            $formation = new Formation();
        }

        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $formation = $form->getData();

            $entityManager->persist($formation);

            $entityManager->flush();

            return $this->redirectToRoute('app_formation');
        }

        return $this->render('formation/add.html.twig', [
            'formAddFormation' => $form->createView(),
            'edit' => $formation->getId()
        ]);
    }

    #[Route('/formation/{id}/delete', name: 'delete_formation')]
    public function deleteFormation(Formation $formation, EntityManagerInterface $entityManager) {
        $entityManager->remove($formation);
        $entityManager->flush();

        return $this->redirectToRoute('app_formation');
    }


    #[Route('/formation/{id}', name: 'show_formation')]
    public function showFormation(Formation $formation): Response {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }
}
