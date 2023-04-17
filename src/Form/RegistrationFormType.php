<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('fullName',TextType::class, [
                'label' => 'Nom complet',
                'required' => true,
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Date de  naissance',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('profession',TextType::class, [
                'label' => 'Profession',
                'required' => true,
            ])
            ->add('centreInteret',TextType::class, [
                'label' => 'Centre d\'intérêt',
                'help' => '<div class="col-12">
                            <span class="text-info font-size-lg"> Séparer les centres d\'intérêt par une virgule (,)</span>
                           </div>',
                'help_html' => true
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => function($role){
                    return match ($role->getRole()) {
                        Role::ROLE_ORGANISATEUR => 'Organisateur',
                        Role::ROLE_ANIMATEUR => 'Animateur',
                        default => 'Utilisateur',
                    };
                },
                'label' => 'Rôle',
                'required' => true,
                'query_builder' => function (RoleRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->where('r.role IN (:role)')
                        ->setParameter('role', [Role::ROLE_USER, Role::ROLE_ORGANISATEUR, Role::ROLE_ANIMATEUR]);
                },
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
