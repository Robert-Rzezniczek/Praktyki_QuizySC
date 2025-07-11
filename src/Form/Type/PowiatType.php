<?php

/**
 * Powiat type.
 */

namespace App\Form\Type;

use App\Entity\Powiat;
use App\Entity\Wojewodztwo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PowiatType.
 */
class PowiatType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'label.name',
                'required' => true,
                'attr' => ['max_length' => 100],
            ]
        );
        $builder->add(
            'wojewodztwo',
            EntityType::class,
            [
                'class' => Wojewodztwo::class,
                'choice_label' => fn (Wojewodztwo $wojewodztwo): ?string => $wojewodztwo->getName(),
                'label' => 'label.wojewodztwo',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Powiat::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     *
     * @psalm-return 'powiat'
     */
    public function getBlockPrefix(): string
    {
        return 'powiat';
    }
}
