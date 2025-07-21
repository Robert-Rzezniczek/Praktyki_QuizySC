<?php

/**
 * UserEdit form.
 */

namespace App\Form;

use App\Entity\UserAuth;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserEditForm.
 */
class UserEditForm extends AbstractType
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
        ->add('email', EmailType::class);
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
