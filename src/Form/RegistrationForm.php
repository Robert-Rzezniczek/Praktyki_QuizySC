<?php

/**
 * Registration form.
 */

namespace App\Form;

use App\Entity\UserAuth;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RegistrationForm.
 */
class RegistrationForm extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options Form options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.enter_password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'message.password_too_short',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('imie', TextType::class, [
                'mapped' => false,
                'label' => 'label.imie',
                'constraints' => [
                    new NotBlank([
                        'message' => 'massage.prosze_podac_imie',
                    ]),
                ],
            ])
            ->add('nazwisko', TextType::class, [
                'mapped' => false,
                'label' => 'label.nazwisko',
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.prosze_podac_nazwisko',
                    ]),
                ],
            ])
            ->add('szkola', TextType::class, [
                'mapped' => false,
                'label' => 'label.szkola',
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.prosze_podac_nazwe_szkoly',
                    ]),
                ],
            ])
            ->add('wojewodztwo', TextType::class, [
                'mapped' => false,
                'label' => 'label.wojewodztwo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.prosze_podac_wojewodztwo',
                    ]),
                ],
            ])
            ->add('powiat', TextType::class, [
                'mapped' => false,
                'label' => 'label.powiat',
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.prosze_podac_powiat',
                    ]),
                ],
            ])
            ->add('podzialWiekowy', TextType::class, [
                'mapped' => false,
                'label' => 'label.poziom_edukacji',
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.nie_powinno_być_puste',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'message.you_should_agree_to_terms',
                    ]),
                ],
                'label' => 'label.agree_terms',
            ])
            ->add('agreeRodo', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'message.you_should_agree_to_rodo',
                    ]),
                ],
                'label' => 'label.agree_rodo',
            ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAuth::class,
        ]);
    }
}