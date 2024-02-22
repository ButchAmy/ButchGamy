<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
				'mapped' => true,
				'required' => true,
			])
            ->add('description', TextareaType::class, [
				'mapped' => true,
				'required' => false,
			])
            ->add('image', FileType::class, [
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
            ->add('url', UrlType::class, [
				'label' => 'URL',
				'mapped' => true,
				'required' => true,
			])
            ->add('public', CheckboxType::class, [
				'mapped' => true,
				'required' => false,
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
