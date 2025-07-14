<?php

/**
 * Quiz form type.
 */

namespace App\Form\Type;

use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * QuizType class.
 */
class QuizType extends AbstractType
{
    /**
     * Builds the form.cd
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array                $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $step = $options['step'] ?? 1;

        if (1 === $step) {
            $builder
                ->add('title', TextType::class, [
                    'label' => 'label.title',
                    'required' => true,
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'label.description',
                    'required' => false,
                ]);
        } elseif (2 === $step) {
            $builder
                ->add('questions', CollectionType::class, [
                    'entry_type' => QuestionType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => false,
                    'prototype' => true,
                ]);
        } elseif (3 === $step) {
            $builder
                ->add('timeLimit', TextType::class, [
                    'label' => 'label.time_limit',
                    'required' => true,
                    'attr' => ['placeholder' => 'W minutach'],
                ]);
        }
    }

    /**
     * Configures the options.
     *
     * @param OptionsResolver $resolver Options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
            'step' => 1, // Domyślny krok
        ]);
    }
}
