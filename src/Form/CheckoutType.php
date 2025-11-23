<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @template-extends AbstractType<array<string, mixed>>
 */
class CheckoutType extends AbstractType
{
    /**
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', TextType::class, [
                'label' => 'Items (e.g. AABAC) ',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Please enter at least one item.'),
                    new Assert\Regex(
                        pattern: '/^[A-Za-z]+$/',
                        message: 'Only letters A-Z are allowed.'
                    ),
                ],
                'attr' => [
                    'placeholder' => 'AAABBD',
                ],
            ]);
    }
}
