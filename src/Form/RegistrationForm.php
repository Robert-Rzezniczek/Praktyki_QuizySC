<?php

/**
 * Registration form.
 */

namespace App\Form;

use App\Entity\Enum\EducationLevel;
use App\Entity\UserAuth;
use App\Repository\PowiatRepository;
use App\Repository\WojewodztwoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * RegistrationForm class.
 */
class RegistrationForm extends AbstractType
{
    private WojewodztwoRepository $wojewodztwoRepository;
    private PowiatRepository $powiatRepository;

    /**
     * Constructor.
     *
     * @param WojewodztwoRepository $wojewodztwoRepository WojewodztwoRepository
     * @param PowiatRepository      $powiatRepository      PowiatRepository
     */
    public function __construct(WojewodztwoRepository $wojewodztwoRepository, PowiatRepository $powiatRepository)
    {
        $this->wojewodztwoRepository = $wojewodztwoRepository;
        $this->powiatRepository = $powiatRepository;
    }

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
        // Pobieranie województw
        $wojewodztwa = $this->wojewodztwoRepository->findAllVoivodeships();
        $wojewodztwaChoices = [];
        foreach ($wojewodztwa as $wojewodztwo) {
            $wojewodztwaChoices[$wojewodztwo->getName()] = $wojewodztwo->getId();
        }

        // Pobieranie powiatów
        $powiaty = $this->powiatRepository->findAll();
        $powiatyChoices = [];
        foreach ($powiaty as $powiat) {
            $powiatyChoices[$powiat->getName()] = $powiat->getId();
        }

        // Opcje dla podziału wiekowego
        $educationChoices = [];
        foreach (EducationLevel::cases() as $level) {
            $educationChoices[$level->label()] = $level->value;
        }

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
            ->add('wojewodztwo', ChoiceType::class, [
                'mapped' => false,
                'label' => 'label.wojewodztwo',
                'choices' => $wojewodztwaChoices,
                'choice_value' => function ($value) {
                    return $value;
                },
                'placeholder' => 'Wybierz województwo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.prosze_podac_wojewodztwo',
                    ]),
                ],
            ])
            ->add('powiat', ChoiceType::class, [
                'mapped' => false,
                'label' => 'label.powiat',
                'choices' => $powiatyChoices,
                'choice_value' => function ($value) {
                    return $value;
                },
                'placeholder' => 'Wybierz powiat',
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.prosze_podac_powiat',
                    ]),
                ],
            ])
            ->add('podzialWiekowy', ChoiceType::class, [
                'mapped' => false,
                'label' => 'label.poziom_edukacji',
                'choices' => $educationChoices,
                'choice_label' => function ($value) {
                    return $value;
                },
                'placeholder' => 'Wybierz etap edukacyjny',
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.nie_powinno_byc_puste',
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
     * Configures the options.
     *
     * @param OptionsResolver $resolver OptionsResolver
     *
     * @return void void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAuth::class,
        ]);
    }
}
