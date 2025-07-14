<?php

/**
 * Answer type.
 */

namespace App\Form\Type;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AnswerType.
 */
class AnswerType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder FormBuilderInterface
     * @param array                $options array
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => ['max_length' => 500, 'placeholder' => 'Podaj odpowiedź'],
            ])
            ->add('isCorrect', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ]);
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver OptionsResolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);
    }
}
