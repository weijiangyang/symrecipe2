<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe.index')]
    public function index(Request $request,PaginatorInterface $paginator,RecipeRepository $recipeRepository): Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * This fonction permet de créer une nouvelle recette
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/recipe/nouveau', name: 'recipe.new', methods: ['GET', 'POST'])]
    
    public function new(Request $request, EntityManagerInterface $em): Response
    {
         $recipe = new Recipe;
         $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $em->persist($recipe);
            $em->flush();
            $this->addFlash(
                'success',
                'Votre recette a bien été crée!'
            );
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/new.html.twig', [
             'form' => $form->createView()
        ]);
    }
/**
     * This fonction permet de modifier une recette
     *
     * @param RecipeRepository $recipeRepository
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recette/edition/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
    
    public function edit(RecipeRepository $recipeRepository, Request $request, EntityManagerInterface $em, Recipe $recipe): Response
    {

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $em->persist($recipe);
            $em->flush();
            $this->addFlash(
                'success',
                'Votre recette a bien été modifié!'
            );
            return $this->redirectToRoute('recipe.index');
        }


        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
/**
     * This fonction permet de supprimer une recette
     *
     * @param RecipeRepository $recipeRepository
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recette/suppression/{id}', name: 'recipe.delete', methods: ['GET', 'POST'])]
    
    public function delete(RecipeRepository $recipeRepository, Request $request, EntityManagerInterface $em, Recipe $recipe): Response
    {
        if (!$recipe) {
            return $this->redirectToRoute('recipe.index');
        }
        $em->remove($recipe);
        $em->flush();
        $this->addFlash(
            'success',
            'Vous avez reusis à supprimer la recette!'
        );
        return $this->redirectToRoute('recipe.index');
    }
}
