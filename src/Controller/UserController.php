<?php

namespace App\Controller;

use App\Entity\FriendRequest;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPermissionsType;
use DateTimeImmutable;
use App\Repository\UserRepository;
use App\Repository\FriendRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
	#[Route('/', name: 'app_user_home', methods: ['GET'])]
    #[Route('/all', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
		$filter = '';
		if (isset($_GET['filter'])) {
			$filter = htmlspecialchars($_GET['filter']);
		};
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findByName($filter),
			'filter' => $filter,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET', 'POST'])]
	#[Route('/{id}/games', name: 'app_user_show_games', methods: ['GET', 'POST'])]
    public function show_games(User $user, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $entityManager): Response
    {
		if (isset($_POST["request"])) {
			$this->handleResponse($user, $friendRequestRepository, $entityManager, $_POST["request"]);
		}

        return $this->render('user/show_games.html.twig', [
            'user' => $user,
			'friendStatus' => $this->friendStatus($user, $friendRequestRepository), // 0 = Not friends, 1 = Sent friend request, 2 = Received friend request, 3 = Friends
			'games' => $user->getGamesDeveloped(),
        ]);
    }

	#[Route('/{id}/achievements', name: 'app_user_show_achievements', methods: ['GET', 'POST'])]
	public function show_achievements(User $user, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $entityManager): Response
    {
		if (isset($_POST["request"])) {
			$this->handleResponse($user, $friendRequestRepository, $entityManager, $_POST["request"]);
		}

        return $this->render('user/show_achievements.html.twig', [
            'user' => $user,
			'friendStatus' => $this->friendStatus($user, $friendRequestRepository), // 0 = Not friends, 1 = Sent friend request, 2 = Received friend request, 3 = Friends
			// 'achievements' => $user->getAchievements(),
        ]);
    }

	#[Route('/{id}/stats', name: 'app_user_show_stats', methods: ['GET', 'POST'])]
	public function show_stats(User $user, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $entityManager): Response
    {
		if (isset($_POST["request"])) {
			$this->handleResponse($user, $friendRequestRepository, $entityManager, $_POST["request"]);
		}

        return $this->render('user/show_stats.html.twig', [
            'user' => $user,
			'friendStatus' => $this->friendStatus($user, $friendRequestRepository), // 0 = Not friends, 1 = Sent friend request, 2 = Received friend request, 3 = Friends
			'gameResults' => $user->getGameResults(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
		if ($this->getUser() != $user) {
			throw $this->createAccessDeniedException('You are not authorized to edit this profile!');
		}

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			/** @var UploadedFile $profilePicFile */
			$profilePicFile = $form->get('profilePic')->getData();
			if ($profilePicFile) {
				if ($user->getProfilePic()) {
					unlink($user->getProfilePic());
				}
				$filename = uniqid().'.'.$profilePicFile->guessExtension();
				try {
					$profilePicFile->move(
						'assets/images/users/',
						$filename
					);
                } catch (FileException $e) {
					// ... handle exception if something happens during file upload
				}
				$user->setProfilePic('assets/images/users/' . $filename);
			}
			if ($form->get('profilePicReset')->getData()) {
				if ($user->getProfilePic()) {
					unlink($user->getProfilePic());
				}
				$user->setProfilePic(null);
			}
			if ($form->get('newPassword')->getData()) {
				$user->setPassword(
					$userPasswordHasher->hashPassword(
						$user,
						$form->get('newPassword')->getData()
					)
				);
			}
			// also store time of update
			$user->setUpdatedOn(new DateTimeImmutable('now'));
			// flush data
            $entityManager->flush();
			// redirect to profile page
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'userForm' => $form->createView(),
        ]);
    }

	#[Route('/{id}/edit/admin', name: 'app_user_edit_admin', methods: ['GET', 'POST'])]
    public function edit_admin(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
		/** @var $appUser User */
		$appUser = $this->getUser();
		if ($appUser->isAdmin() == false) {
			throw $this->createAccessDeniedException('You are not authorized to access this page!');
		}

		$form = $this->createForm(UserPermissionsType::class, $user);
        $form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// store time of update
			$user->setUpdatedOn(new DateTimeImmutable('now'));
			// flush data
            $entityManager->flush();
			// redirect to profile page
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit_admin.html.twig', [
            'user' => $user,
            'userForm' => $form->createView(),
        ]);
    }

	public function friendStatus(User $user, FriendRequestRepository $friendRequestRepository): int
	{
		if (in_array($this->getUser(), $user->getFriends()->toArray())) {
			return 3;
		}
		if ($friendRequestRepository->findBy([ 'userA' => $user, 'userB' => $this->getUser() ])) {
			return 2;
		}
		if ($friendRequestRepository->findBy([ 'userA' => $this->getUser(), 'userB' => $user ])) {
			return 1;
		}
		return 0;
	}

	public function handleResponse(User $user, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $entityManager, string $response): static
	{
		if ($response == "send") {
			if (!$friendRequestRepository->findBy([ 'userA' => $this->getUser(), 'userB' => $user ])) {
				$friendRequest = new FriendRequest();
				$friendRequest->setUserA($this->getUser());
				$friendRequest->setUserB($user);
				$friendRequest->setRequestedOn(new DateTimeImmutable('now'));
				$friendRequest->setAccepted(false);
				$entityManager->persist($friendRequest);
			}
		} elseif ($response == "accept") {
			$entityManager->getRepository(FriendRequest::class)->findOneBy([ 'userA' => $user, 'userB' => $this->getUser() ])->setAccepted(true);
			$entityManager->getRepository(FriendRequest::class)->findOneBy([ 'userA' => $user, 'userB' => $this->getUser() ])->getUserA()->addFriend($this->getUser());
			$entityManager->getRepository(FriendRequest::class)->findOneBy([ 'userA' => $user, 'userB' => $this->getUser() ])->getUserB()->addFriend($user);
		} elseif ($response == "refuse") {
			$entityManager->remove($friendRequestRepository->findOneBy([ 'userA' => $user, 'userB' => $this->getUser() ]));
		}
		$entityManager->flush();

		return $this;
	}
}
