<?php

/**
 * Quiz type.
 */

namespace App\Form\Type;

use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     *
     * @return void void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'label.title',
                'required' => true,
                'attr' => ['max_length' => 255],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description',
                'required' => false,
                'attr' => ['max_length' => 1000],
            ])
            ->add('timeLimit', IntegerType::class, [
                'label' => 'label.time_limit',
                'required' => true,
                'attr' => ['min' => 1],
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'label.is_published',
                'required' => false,
            ])
            ->add('questions', CollectionType::class, [
                'entry_type' => QuestionType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'label.questions',
                'attr' => ['data-collection-holder' => 'questions'],
            ]);
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver OptionsResolver
     *
     * @return void void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
