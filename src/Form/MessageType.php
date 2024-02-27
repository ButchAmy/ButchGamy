<?php

namespace App\Form;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
				'mapped' => true,
				'required' => true,
				'attr' => [
					'placeholder' => 'Type your message here'
				],
			])
            ->add('userTo', EntityType::class, [
				'label' => 'To',
                'class' => User::class,
				'choices' => $options['app_user']->getFriends(),
				'choice_label' => 'username',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
			'app_user' => null,
        ]);

		$resolver->setAllowedTypes('app_user', 'App\Entity\User');
    }
}
