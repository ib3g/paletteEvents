<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
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
                'choice_label' => 'role',
                'label' => 'Rôle',
                'required' => true,
            ])
            ->add('facebook',TextType::class, [
                'label' => 'Lien Facebook',
                'required' => false,
            ])
            ->add('twitter',TextType::class, [
                'label' => 'Lien Twitter',
                'required' => false,
            ])
            ->add('linkedin',TextType::class, [
                'label' => 'Lien Linkedin',
                'required' => false,
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
