<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('username', TextType::class, [
				'required' => true,
			])
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
			->add('profilePicReset', CheckboxType::class, [
				'label' => 'Reset profile picture',
				'mapped' => false,
				'required' => false,
			])
			->add('profileBio', TextareaType::class, [
				'required' => false,
			])
			->add('newPassword', RepeatedType::class, [
				'type' => PasswordType::class,
				'invalid_message' => 'The password fields must match',
				'options' => ['attr' => ['class' => 'password-field']],
				'required' => false,
				'first_options'  => ['label' => 'New password'],
				'second_options' => ['label' => 'Confirm new password'],
				'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
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
