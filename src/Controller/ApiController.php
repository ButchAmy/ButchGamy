<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\Game;
use App\Entity\GameResult;
use App\Entity\User;
use DateTimeImmutable;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

#[Route('/api')]
class ApiController extends AbstractController
{
	#[Route('/', name: 'app_api_docs', methods: ['GET'])]
    public function api_docs(): Response
    {
		return $this->render('api/index.html.twig');
    }

	#[Route('/achievement', name: 'app_api_achievement', methods: ['POST'])]
    public function api_achievement(EntityManagerInterface $entityManager, GameRepository $gameRepository, UserRepository $userRepository): Response
    {
		try {
			// Set user and game variables, then check authorization
			$user = $userRepository->findOneById($_POST['userId']);
			$game = $gameRepository->findOneByApiKey($_POST['apiKey']);
			$this->checkHash($game, $user, $_POST['hash']);
			// Does the User already have this achievement?
			foreach ($user->getAchievementsFor($game) as $achievement) {
				if ($achievement->getName() === $_POST['name']) {
					return $this->json([
						'status' => 'OK',
						'new' => false,
					]);
				}
			}
			// Create new achievement
			$achievement = new Achievement;
			// Fill out fields
			$achievement->setUser($user);
			$achievement->setGame($game);
			$achievement->setName($_POST['name']);
			$achievement->setDescription($_POST['description']);
			$achievement->setImage($_POST['image']);
			$achievement->setAchievedOn(new DateTimeImmutable('now'));
			// Persist and flush
			$entityManager->persist($achievement);
			$entityManager->flush();
			// Return response with variable true
			return $this->json([
				'status' => 'OK',
				'new' => true,
			]);
		} catch (BadRequestException $e) {
			throw $e;
		}
    }

	#[Route('/result', name: 'app_api_result', methods: ['POST'])]
    public function api_result(EntityManagerInterface $entityManager, GameRepository $gameRepository, UserRepository $userRepository): Response
    {
		try {
			// Set user and game variables, then check authorization
			$user = $userRepository->findOneById($_POST['userId']);
			$game = $gameRepository->findOneByApiKey($_POST['apiKey']);
			$this->checkHash($game, $user, $_POST['hash']);
			// Create new game result
			$gameResult = new GameResult;
			// Fill out fields
			$gameResult->setUser($user);
			$gameResult->setGame($game);
			$gameResult->setScore($_POST['score']);
			$gameResult->setTime($_POST['time']);
			$gameResult->setAchievedOn(new DateTimeImmutable('now'));
			// Persist and flush
			$entityManager->persist($gameResult);
			$entityManager->flush();
			// Return response
			return $this->json([
				'status' => 'OK',
			]);
		} catch (BadRequestException $e) {
			throw $e;
		}
    }

	#[Route('/user/{id}', name: 'app_api_user', methods: ['GET'])]
    public function api_user(User $user): Response
    {
		// Return username and profile pic URL
		return $this->json([
			'username' => $user->getUsername(),
			'profilePic' => $this->generateUrl('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL) . $user->getProfilePic(),
		]);
    }

	#[Route('/user/{id}/achievements', name: 'app_api_user_achievements', methods: ['GET'])]
    public function api_user_achievements(User $user, GameRepository $gameRepository): Response
    {
		// Set game variable
		$game = $gameRepository->findOneByApiKey($_GET['apiKey']);

		// Return user's achievements for specified game
		return $this->json([
			'achievements' => $user->getAchievementsFor($game),
		]);
    }

	#[Route('/user/{id}/results', name: 'app_api_user_results', methods: ['GET'])]
    public function api_user_results(User $user, GameRepository $gameRepository): Response
    {
		// Set game variable
		$game = $gameRepository->findOneByApiKey($_GET['apiKey']);

		// Return user's game results for specified game
		return $this->json([
			'results' => $user->getGameResultsFor($game),
		]);
    }

	#[Route('/test', name: 'app_test', methods: ['POST'])]
	public function api_test(): Response
	{
		return $this->json([
			'status' => 'OK',
			'new' => true,
		]);
	}

	public function checkHash(Game $game, User $user, string $hash): static
	{
		if ($game->getHash($user) !== $hash) {
			die('You should kill yourself NOW');
		}
		return $this;
	}
}
