<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Category;
use App\Form\ModuleType;
use App\Form\CategoryType;
use App\Repository\ModuleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ModuleController extends AbstractController
{
    //Fonction pour lister les modules
    #[Route('/module', name: 'app_module')]
    public function index(ModuleRepository $moduleRepository): Response
    {
        $modules = $moduleRepository->findBy([], ['moduleName' => 'ASC']);

        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    ////La fonction va permettre de créer un formulaire et si c'est pour ajouter une formation, alors le formulaire sera vide, mais si c'est pour modifier un module existant le formulaire sera pré-remplit
    #[Route('/module/add', name: 'add_module')]
    #[Route('/module/{id}/edit', name: 'edit_module')]
    public function add_editModule(Module $module = null, Request $request, EntityManagerInterface $entityManager): Response {
        // On crée une condition If pour dire que S'IL n'y a pas d'id de module, alors on crée un nouveau object module
        if(!$module) {
            $module = new Module();
        }

        //On crée la variable form qui va créer un formulaire avec le module actuel
        $form = $this->createForm(ModuleType::class, $module);

        //handleRequest permet de récupérer les données de POST pour faire les test de validation et filtrage
        $form->handleRequest($request);
            //SI les données du formulaire sont envoyé et ont passée les tests de validation 
            if ($form->isSubmitted() && $form->isValid()) {
                //$form->getData() va retenir les valeurs des données et la variable module à été actualisé avec les nouvelles valeurs 
                $module = $form->getData();

                //L'interface de manager d'entité va préparer (persist) la requête SQL avec les données du formaulaire
                $entityManager->persist($module); 

                //L'interface de manager d'entité va envoyer et enregistrer (flush), la requête avec les nouvelles données, dans la base de données
                $entityManager->flush();

            //Une fois l'envois enregistré, on redirige vers la liste des modules 
            return $this->redirectToRoute('app_module');
        }

        return $this->render('module/add.html.twig', [
            'formAddModule' => $form->createView(),
            'edit' => $module->getId(),
        ]);
    }

    //On crée la route pour la fonction de suppression d'un module
    #[Route('/module/{id}/delete', name: 'delete_module')]
    public function deleteModule(Module $module, EntityManagerInterface $entityManager) {

        //L'interface de manager d'entité va identifier le module et prépare la suppression de la base de données
        $entityManager->remove($module);

        //L'interface de manager d'entité va enregistrer la suppression de la base de données
        $entityManager->flush();

        //On redirige vers la liste des modules quand la suppresion est enregistré
        return $this->redirectToRoute('app_module');
    }

    #[Route('/module/{id}', name: 'show_module')]
    public function showModule(Module $module, ): Response {
        return $this->render('module/show.html.twig', [
            'module' => $module,
        ]);
    }


    //Fonction pour afficher la liste des catégories
    #[Route('/category', name: 'list_category')]
    public function listCategory(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ['categoryName' => 'ASC']);

        return $this->render('module/listCategory.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/module/{id}/showModuleByCategory', name: 'showModule_category')]
    public function showModuleByCategory($id, Category $category, Module $module, ModuleRepository $moduleRepository, EntityManagerInterface $entityManager): Response
    {
        $modules = $moduleRepository->findByCategory($id);


        return $this->render('module/index.html.twig', [
            'modules' => $modules,
            'category' => $category->getId(),
        ]);
    }

    #[Route('/category/add', name: 'add_category')]
    #[Route('/category/{id}/edit', name: 'edit_category')]
    public function add_editCategory(Category $category = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$category) {
            $category = New Category();
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager->persist($category); 
            $entityManager->flush();

            return $this->redirectToRoute('list_category');
        }

        return $this->render('module/addCategory.html.twig', [
            'formAddCategory' => $form->createView(),
            'category' => $category->getId(),
        ]);
    }

    #[Route('/category/{id}/delete', name: 'delete_category')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager) 
    {
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('list_category');
    }
}
