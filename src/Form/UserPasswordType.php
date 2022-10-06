<?php 
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserPasswordType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
         $builder
            ->add('plainPassword', RepeatedType::class, [

                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ],
                'invalid_message' => 'Les mots de passe ne correspond pas'
            ])
            ->add('newPassword',PasswordType::class,[
                'attr'=>[
                    'class'=>'form-control',
                    
                ],
                'label'=>'New Password',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints'=>[
                    new Assert\NotBlank()
                ]
            ])
            ->add('submit',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-primary mt-4',
                    
                ],
                'label'=>'Inscrivez vous'
            ]);
           
        
    }
}