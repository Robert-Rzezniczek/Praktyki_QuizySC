<?php

/**
 * Question form type.
 */

namespace App\Form\Type;

use App\Entity\Question;
use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * QuestionType class.
 */
class QuestionType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array                $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'label.question_content',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Dodaj treść pytania',
                ],
            ])
            ->add('points', IntegerType::class, [
                'label' => 'label.points',
                'required' => true,
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'Podaj ilość punktów',
                ],
            ])
            ->add('answers', CollectionType::class, [
                'entry_type' => AnswerType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'prototype' => true,
                'attr' => ['class' => 'answers-collection'],
                'entry_options' => ['label' => false],
                // Inicjalizacja z 4 odpowiedziami
                'data' => array_fill(0, 4, new Answer()),
                // Upewnij się, że formularz zawsze przesyła co najmniej 2 odpowiedzi
                'empty_data' => fn () => array_fill(0, 4, new Answer()),
            ]);
    }

    /**
     * Configures the options.
     *
     * @param OptionsResolver $resolver Options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
