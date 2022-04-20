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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CreateTricksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => "Nom du tricks",
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
                'label' => "Description du tricks"
            ])
            ->add('user', EntityType::class, [
                'required' => true,
                'label' => 'Choisir un user',
                'class' => User::class,
                'choice_label' => 'id'
              ])
            ->add('category', EntityType::class, [
                'required' => true,
                'label' => 'Choisir un groupe',
                'class' => Category::class,
                'choice_label' => 'name',
                'constraints' => [
                  new NotBlank([
                    'message' => 'Veuillez choisir un groupe'
                  ])
                ]
              ])
            ->add('medias', FileType::class,[
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => "Créer"
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
