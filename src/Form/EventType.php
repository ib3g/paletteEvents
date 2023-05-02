<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'label' => 'Titre de l\'événement',
                'required' => true,
            ])
            ->add('shortDescription',TextareaType::class, [
                'label' => 'Courte description de l\'événement',
                'attr' => ['rows' => 3],
                'required' => true,
            ])
            ->add('description',CKEditorType::class, [
                'label' => 'Description complète de l\'événement',
                'required' => true,
            ])
            ->add('dateEvent', DateType::class, [
                'label' => 'Date de l\'événement',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('lieu',TextType::class, [
                'label' => 'Lieu de l\'événement',
                'required' => true,
            ])
            ->add('sponsors',TextType::class, [
                'label' => 'Liste de sponsors',
                'help' => '<div class="col-12">
                            <span class="text-info font-size-lg"> Séparer les sponsors par une virgule (,)</span>
                           </div>',
                'help_html' => true
            ])
            ->add('animators', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'label' => 'Animateurs',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.role', 'r')
                        ->where('r.role = :role')
                        ->setParameter('role', Role::ROLE_ANIMATEUR);
                },
                'help' => '<div class="col-12">
                            <span class="text-info font-size-lg"> Un email sera envoyé à chaque animateur pour l\'informer de son invitation</span>
                           </div>',
                'help_html' => true
            ])
            ->add('categories')
            ->add('tags')
        ;

        if ($builder->getData()->getId() != null) {
            $builder->add('status', ChoiceType::class, [
                'label' => 'Statut de l\'événement',
                'choices' => [
                    'En cours' => Event::STATUS_IN_PROGRESS,
                    'Terminé' => Event::STATUS_FINISHED,
                    'Annulé' => Event::STATUS_CANCELED,
                    'Reporté' => Event::STATUS_POSTPONED,
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ]);

        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
