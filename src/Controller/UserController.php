<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'user.edit',methods:['GET','POST'])]
    #[Security("is_granted('ROLE_USER') and user == chosenUser")]
    public function edit(User $chosenUser,Request $request,EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('security.login');
        }
        if(!$this->getUser() === $chosenUser){
            return $this->redirectToRoute('recipe.index');
        }
        $form = $this->createForm(UserType::class, $chosenUser);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($chosenUser,$form->getData()->getPlainPassword())){
                $chosenUser = $form->getData();
                $em->persist($chosenUser);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Le profile de votre profile a bien été modifié'
                );

                return $this->redirectToRoute('recipe.index');
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe n\'est pas correct.'
                );
            }
            
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/utilisateur/edition-mot-de-passe/{id}',name:'user.edit.password',methods:['GET','POST'])]
    #[Security("is_granted('ROLE_USER') and user == chosenUser")]
    public function editPassword(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em, User $chosenUser):Response
    {
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($chosenUser,$form->getData()['plainPassword'])){
                $chosenUser->setPassword($hasher->hashPassword($chosenUser,$form->getData()['newPassword']));
                $em->persist($chosenUser);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié'
                );
                

                return $this->redirectToRoute('recipe.index');
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe n\'est pas correct'
                );
            }
       
        }

        return $this->render('pages/user/edit_password.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
