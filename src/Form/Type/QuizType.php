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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class QuizType.
 */
class QuizType extends AbstractType
{
    /**
     * Builds the form.cd.
     *
     * @param FormBuilderInterface $builder FormBuilderInterface
     * @param array                $options array
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $step = $options['step'] ?? 1;
        $brandingOnly = $options['branding_only'] ?? false;

        if ($brandingOnly) {
            // Formularz tylko do brandingu
            $builder
                ->add('brandName', TextType::class, [
                    'label' => 'Nazwa marki',
                    'required' => false,
                    'attr' => ['placeholder' => 'Wprowadź nazwę marki'],
                ])
                ->add('branddescription', TextType::class, [
                    'label' => 'Opis',
                    'required' => false,
                    'attr' => ['placeholder' => 'Wprowadź opis'],
                ])
                ->add('logoFile', FileType::class, [
                    'label' => 'Logo (plik graficzny)',
                    'mapped' => false, // ważne: nie jest bezpośrednio mapowane na entity
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '2M',
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                            'mimeTypesMessage' => 'Proszę przesłać plik graficzny (jpg, png, gif)',
                        ]),
                    ],
                ]);
        } else {
            // Formularz krokowy ten od edycji
            if (1 === $step) {
                $builder
                    ->add('title', TextType::class, [
                        'label' => false,
                        'required' => true,
                        'attr' => ['max_length' => 255, 'placeholder' => 'Podaj tytuł quizu'],
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Tytuł nie może być pusty.',
                            ]),
                            new Length([
                                'max' => 255,
                                'maxMessage' => 'Tytuł nie może przekraczać {{ limit }} znaków.',
                            ]),
                        ],
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
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Limit czasu nie może być pusty.',
                            ]),
                            new GreaterThan([
                                'value' => 0,
                                'message' => 'Limit czasu musi być większy niż 0 minut.',
                            ]),
                            new Type([
                                'type' => 'integer',
                                'message' => 'Limit czasu musi być liczbą całkowitą.',
                            ]),
                            new LessThanOrEqual([
                                'value' => 1440, // Maksymalnie 24 godziny
                                'message' => 'Limit czasu nie może przekraczać 1440 minut (24 godziny).',
                            ]),
                        ],
                    ])
                    ->add('isPublished', CheckboxType::class, [
                        'label' => 'Opublikuj quiz',
                        'required' => false,
                    ]);
            }
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
            'branding_only' => false,
        ]);
    }
}
