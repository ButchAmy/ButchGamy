<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
			->add('email')
			->add('birthday')
			->add('gender')
			->add('profilePic', FileType::class, [
				'label' => 'Profile picture',
				'mapped' => false,
				'required' => false,
				'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => ["image/bmp", "image/png", "image/gif", "image/jpeg", "image/webp"],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
				],
			])
            ->add('plainPassword', RepeatedType::class, [
				// instead of being set onto the object directly,
				// this is read and encoded in the controller
				'type' => PasswordType::class,
				'invalid_message' => 'The password fields must match',
				'options' => ['attr' => ['class' => 'password-field']],
				'required' => true,
				'first_options'  => ['label' => 'Password'],
				'second_options' => ['label' => 'Confirm Password'],
				'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
