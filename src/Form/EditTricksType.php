<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Tricks;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditTricksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => "Nom du tricks",
                'attr' => [
                    'placeholder' => 'Nom du tricks',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'constraints' => [
                    new NotBlank([
                       'message' => 'Veuillez saisir un nom'
                    ]),
                    new Length([
                       'min' => 2,
                       'minMessage' => 'Le nom doit contenir au minimum {{ limit }} caractères'
                    ]),
                 ]
            ])
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => "Description du tricks",
                'attr' => [
                    'placeholder' => 'Description...'
                ]
            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'label' => 'Choisir un groupe :',
                'class' => Category::class,
                'choice_label' => 'name',
                'row_attr' => [
                    'class' => 'input-group',
                ],
                'constraints' => [
                  new NotBlank([
                    'message' => 'Veuillez choisir un groupe'
                  ])
                ]
              ])
            ->add('medias', FileType::class,[
                'label' => 'Ajouter des fichiers :',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group',
                ]
            ])
            ->add('urls', CheckboxType::class,[
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'checked',
                ]])
            ->add('save', SubmitType::class, [
                'label' => "Valider"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
