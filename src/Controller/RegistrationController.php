<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
			// handle profile picture
				/** @var UploadedFile $profilePicFile */
				$profilePicFile = $form->get('profilePic')->getData();
				// this condition is needed because the 'profile pic' field is not required
            	// so the file must be processed only when a file is uploaded
				if ($profilePicFile) {
					$filename = uniqid().'.'.$profilePicFile->guessExtension();
                // Move the file to the public assets directory
                try {
                    $profilePicFile->move(
                        'assets/images/users/',
                        $filename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $user->setProfilePic('assets/images/users/' . $filename);
			};
			// store creation and update time
			$user->setCreatedOn(new DateTimeImmutable('now'));
			$user->setUpdatedOn(new DateTimeImmutable('now'));
			// set default permissions
			$user->setAdmin(false);
			$user->setFriendAllowed(true);
			$user->setLoginAllowed(true);
			$user->setMessageAllowed(true);
			// save user, then flush data
            $entityManager->persist($user);
            $entityManager->flush();
			// redirect to login page
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}