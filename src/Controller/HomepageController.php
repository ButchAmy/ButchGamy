<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class HomepageController extends AbstractController
{
	#[Route('/', name: 'app_index', methods: ['GET'])]
    #[Route('/homepage', name: 'app_homepage', methods: ['GET'])]
	#[Route('/homepage/all', name: 'app_homepage_all', methods: ['GET'])]
	public function indexAll(GameRepository $gameRepository): Response
	{
		if ($this->getUser()) {
			/** @var $appUser User */
			$appUser = $this->getUser();
			if ($appUser->isAdmin()) {
				return $this->render('homepage/index_all.html.twig', [
					'games' => $gameRepository->findAll(),
					// Show both public and private games if user is an admin
				]);
			}
		}
		return $this->render('homepage/index_all.html.twig', [
			'games' => $gameRepository->findBy(['public' => true]),
		]);
    }

	#[Route('/homepage/friends', name: 'app_homepage_friends', methods: ['GET'])]
	public function indexFriends(GameRepository $gameRepository): Response
	{
		if (!$this->getUser())
		{
			return $this->redirectToRoute('app_homepage_all');
			// If user is not logged in, redirect to normal homepage to prevent errors
		}
		/** @var $appUser User */
		$appUser = $this->getUser();
		$friendGames = [];
		foreach ($gameRepository->findAll() as $game) {
			if (in_array($game->getDeveloper(), $appUser->getFriends()->toArray())) {
				$friendGames[] = $game;
			}
		}
		return $this->render('homepage/index_friends.html.twig', [
			'games' => $friendGames,
		]);
    }
}