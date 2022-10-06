<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IngredientController extends AbstractController
{
    /**
     * This function displays all the ingrédients
     *
     * @param Request $request
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index',methods:['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request,IngredientRepository $ingredientRepository,PaginatorInterface $paginator): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepository->findBy(['user'=>$this->getUser()]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/ingredient/index.html.twig', [
           'ingredients' => $ingredients
        ]);
    }
/**
     * This fonction permet de créer un nouveau ingrédient
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/ingredient/nouveau',name:'ingredient.new',methods:['GET','POST'])]
    
    public function new(Request $request,EntityManagerInterface $em):Response{
        $ingredient = new Ingredient;
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /**
             * @var Ingredient  */
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());
        
            $em->persist($ingredient);
            $em->flush();
        $this->addFlash(
            'success',
            'Votre ingrédient a bien été crée!'
        );
        return $this->redirectToRoute('ingredient.index');
            
        }
        return $this->render('pages/ingredient/new.html.twig',[
            'form'=>$form->createView()
        ]);
    }
/**
     * This fonction permet de modifier un ingrédient demandé
     *
     * @param IngredientRepository $ingredientRepository
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Ingredient $ingredient
     * @return Response
     */
    #[Route('/ingredient/edition/{id}',name: 'ingredient.edit',methods:['GET','POST'])]
    #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    public function edit(IngredientRepository $ingredientRepository,Request $request, EntityManagerInterface $em, Ingredient $ingredient):Response{
        
        $form = $this->createForm(IngredientType::class,$ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $em->persist($ingredient);
            $em->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient a bien été modifié!'
            );
            return $this->redirectToRoute('ingredient.index');
        }

        
        return $this->render('pages/ingredient/edit.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/ingredient/suppression/{id}',name:'ingredient.delete',methods:['GET','POST'])]
    #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    public function delete(IngredientRepository $ingredientRepository, Request $request, EntityManagerInterface $em, Ingredient $ingredient):Response
    {
        if(!$ingredient){
            return $this->redirectToRoute('ingredient.index');
        }
       $em->remove($ingredient);
       $em->flush();
       $this->addFlash(
        'success',
        'Vous avez reusis à supprimer un ingrédient!'
       );
       return $this->redirectToRoute('ingredient.index');

    }
   
}
