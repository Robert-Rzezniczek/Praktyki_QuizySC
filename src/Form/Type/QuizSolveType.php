<?php

/**
 * QuizSolve type.
 */

namespace App\Form\Type;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class QuizSolveType.
 */
class QuizSolveType extends AbstractType
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
        /** @var Question $question */
        $question = $options['question'];
        $answers = $question->getAnswers()->toArray();

        $choices = [];
        foreach ($answers as $answer) {
            $choices[$answer->getContent()] = $answer->getId();
        }

        $builder
            ->add('answer', ChoiceType::class, [
                'label' => $question->getContent(),
                'choices' => $choices,
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('question_index', HiddenType::class, [
                'data' => $options['question_index'],
            ]);
    }

    /**
     * Configures the options.
     *
     * @param OptionsResolver $resolver OptionsResolver
     *
     * @return void void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['question', 'question_index']);
        $resolver->setAllowedTypes('question', [Question::class]);
        $resolver->setAllowedTypes('question_index', ['int']);
    }
}
