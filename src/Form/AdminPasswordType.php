<?php

namespace App\Form;

use Symfony\Component\Form\UserAuth;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AdminPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nowe hasło',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Hasło nie może być puste.']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Hasło musi mieć co najmniej {{ limit }} znaków.',
                    ]),
                ],
            ]);
    }
}
