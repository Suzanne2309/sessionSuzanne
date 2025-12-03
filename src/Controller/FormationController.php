<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\SessionRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormationController extends AbstractController
{
    //Fonction pour lister les formations
    #[Route('/formation', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response
    {
        //La variable formations est un tableau qui va se remplir avec les données du tableau formation en base de données grâce au repository Formation
        $formations = $formationRepository->findBy([], ['title' => 'ASC']);

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    //La fonction va permettre de créer un formulaire et si c'est pour ajouter une formation, alors le formulaire sera vide, mais si c'est pour modifier un module existant le formulaire sera pré-remplit
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

    //Fonction de suppression d'une formation
    #[Route('/formation/{id}/delete', name: 'delete_formation')]
    public function deleteFormation(Formation $formation, EntityManagerInterface $entityManager) {
        $entityManager->remove($formation);
        $entityManager->flush();

        return $this->redirectToRoute('app_formation');
    }

    //Fonction qui va afficher le détail d'une formation
    #[Route('/formation/{id}', name: 'show_formation')]
    public function showFormation($id, Formation $formation): Response {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/session/{id}/showSessionByFormation', name: 'showSession_formation')]
    public function showSessionByFormation($id, Formation $formation, Session $session, SessionRepository $sessionRepository, EntityManagerInterface $entityManager): Response
    {
        $sessions = $sessionRepository->findByCategory($id);


        return $this->render('formation/show.html.twig', [
            'sessions' => $sessions,
            'formation' => $formation->getId(),
        ]);
    }
}
