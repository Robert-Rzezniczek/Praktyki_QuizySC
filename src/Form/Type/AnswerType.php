<?php

/**
 * Answer form type.
 */

namespace App\Form\Type;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AnswerType class.
 */
class AnswerType extends AbstractType
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
                'label' => 'label.answer_content',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Dodaj odpowiedź...',
                ],
            ])
            ->add('isCorrect', CheckboxType::class, [
                'label' => 'label.correct_answer',
                'required' => false,
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
            'data_class' => Answer::class,
        ]);
    }
}
