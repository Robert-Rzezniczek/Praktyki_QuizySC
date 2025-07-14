<?php

/**
 * Quiz type.
 */

namespace App\Form\Type;

use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class QuizType.
 */
class QuizType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder FormBuilderInterface
     * @param array                $options array
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $step = $options['step'] ?? 1;

        if (1 === $step) {
            $builder
                ->add('title', TextType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => ['max_length' => 255, 'placeholder' => 'Podaj tytuł quizu'],
                ])
                ->add('description', TextareaType::class, [
                    'label' => false,
                    'required' => false,
                    'attr' => ['max_length' => 1000, 'placeholder' => 'Podaj opis quizu'],
                ]);
        } elseif (2 === $step) {
            $builder
                ->add('questions', CollectionType::class, [
                    'entry_type' => QuestionType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => false,
                    'attr' => ['data-collection-holder' => 'questions'],
                    'prototype' => true,
                    'prototype_name' => '__name__',
                ]);
        } elseif (3 === $step) {
            $builder
                ->add('timeLimit', IntegerType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => ['min' => 1, 'placeholder' => 'Podaj limit czasu w minutach'],
                ])
                ->add('isPublished', CheckboxType::class, [
                    'label' => 'Opublikuj quiz',
                    'required' => false,
                ]);
        }
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver OptionsResolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
            'step' => 1,
        ]);
    }
}
