<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Formation;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Form\FormationType;
use App\Form\StagiaireType;
use App\Repository\SessionRepository;
use App\Repository\FormationRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\StagiaireRepository;
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
    public function showFormation(Formation $formation, Session $session, SessionRepository $sessionRepository, EntityManagerInterface $entityManager): Response {
        
        $sessions = $sessionRepository->findByFormation($formation->getId());

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'sessions' => $sessions,
        ]);
    }

    #[Route('/session/add', name: 'add_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function add_editSession(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(! $session) {
            $session = new Session();
        }

        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $session = $form->getData();

            $entityManager->persist($session);

            $entityManager->flush();

            return $this->redirectToRoute('app_formation');
        }

        return $this->render('formation/addSession.html.twig', [
            'formAddSession' => $form->createView(),
            'edit' => $session->getId(),
        ]);
    }

    #[Route('/session/{id}/delete', name: 'delete_session')]
    public function deleteSession(Session $session, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($session);

        $entityManager->flush();

        return $this->redirectToRoute('app_formation');
    }

    //Fonction qui va afficher le détail d'une session
    #[Route('/session/{id}/show', name: 'show_session')]
    public function showSession(Session $session, Programme $programme, ProgrammeRepository $programmeRepository, EntityManagerInterface $entityManager): Response 
    {
        $programmes = $programmeRepository->findBySession($session->getId());

        $stagiaires = $session->getStagiaires();

        return $this->render('formation/showSession.html.twig', [
            'session' => $session,
            'programmes' => $programmes,
            'stagiaires' => $stagiaires,
        ]);
    }

    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function appStagiaire(StagiaireRepository $stagiaireRepository) : Response
    {
        $stagiaires = $stagiaireRepository->findBy([], ['lastName' => 'ASC']);

        return $this->render('formation/listStagiaire.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }

    #[Route('/stagiaire/add', name: 'add_stagiaire')]
    #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')]
    public function add_editStagiaire(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $entityManager) :Response
    {

        if(! $stagiaire) {
            $stagiaire = new Stagiaire;
        }

        $form = $this->createForm(StagiaireType::class, $stagiaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $stagiaire = $form->getData();
            $entityManager->persist($stagiaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_stagiaire');
        }

        return $this->render('formation/addStagiaire.html.twig', [
            'formAddStagiaire' => $form->createView(),
            'edit' => $stagiaire->getId(),
        ]);
    }

    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function showStagiaire(Stagiaire $stagiaire, StagiaireRepository $stagiaireRepository, EntityManagerInterface $entityManager) : Response
    {
        
        return $this->render('formation/showStagiaire.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }
}
