<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe.index')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request,PaginatorInterface $paginator,RecipeRepository $recipeRepository): Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findBy(['user'=>$this->getUser()]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recette/public',name:'recette.index.public')]
    public function indexPublic(Request $request, PaginatorInterface $paginator, RecipeRepository $recipeRepository):Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findPublicRecipe(null),/* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index_public.html.twig',[
            'recipes'=>$recipes
        ]);
    }
    #[Security("is_granted('ROLE_USER') and recipe.isIsPublic() === true")]
    #[Route("/recette/{id}", name:'recipe.show')]
    public function show(MarkRepository $markRepository,Recipe $recipe,Request $request,EntityManagerInterface $em):Response
    {
        $mark = new Mark;
        $form = $this->createForm(MarkType::class,$mark);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $mark = new Mark;
            $mark->setMark($form->getData()->getMark())
                ->setUser($this->getUser())
                ->setRecipe($recipe);
            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(),
                'recipe'=> $recipe
            ]);
            if($existingMark){
                $em->remove($existingMark);

            };
            $em->persist($mark);
            $em->flush();

            $this->addFlash(
                'success',
                'Voter note a été bien compté'
            );

            return $this->redirectToRoute('recipe.show',[
                'id'=>$recipe->getId()
            ]);
        }
        return $this->render('pages/recipe/show.html.twig',[
            'recipe'=>$recipe,
            'form'=>$form->createView()
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
            $recipe->setUser($this->getUser());
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
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
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
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
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
