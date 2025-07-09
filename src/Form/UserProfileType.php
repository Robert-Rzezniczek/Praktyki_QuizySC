<?php

namespace App\Form;

use App\Entity\Enum\EducationLevel;
use App\Entity\Powiat;
use App\Entity\UserProfile;
use App\Entity\Wojewodztwo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imie', TextType::class)
            ->add('nazwisko', TextType::class)
            ->add('szkola', TextType::class)
            ->add('wojewodztwo', EntityType::class, [
                'class' => Wojewodztwo::class,
                'choice_label' => 'name',
                'placeholder' => 'Wybierz województwo',
            ])
            ->add('powiat', EntityType::class, [
                'class' => Powiat::class,
                'choice_label' => 'name',
                'placeholder' => 'Wybierz powiat',
            ])

            ->add('podzialWiekowy', ChoiceType::class, [
                'choices' => EducationLevel::cases(),
                'choice_label' => fn (EducationLevel $choice) => $choice->value,
                'choice_value' => fn (?EducationLevel $choice) => $choice?->value,
                'label' => 'Poziom edukacji',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserProfile::class,
        ]);
    }
}
