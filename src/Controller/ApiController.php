<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameResult;
use App\Entity\User;
use App\Form\GameType;
use App\Repository\AchievementRepository;
use DateTimeImmutable;
use App\Repository\GameRepository;
use App\Repository\GameResultRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
	#[Route('/', name: 'app_api_docs', methods: ['GET'])]
    public function api_docs(): Response
    {
		return $this->render('api/index.html.twig', [
			'endpoint' => 'https://butchgamy.net/api',
		]);
    }

	#[Route('/result', name: 'app_api_result', methods: ['GET', 'POST'])]
    public function api_result(EntityManager $entityManager): Response
    {
		// Check authorization and set game and user variables and such here
		$user = null;
		$game = null;

		// The request is using the POST method
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
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
			} catch (BadRequestException $e) {
				throw $e;
			}
		}

		// Return the filtered collection
		return $this->json([
			'results' => $user->getGameResultsFor($game),
		]);
    }

	#[Route('/result/last', name: 'app_api_result_last', methods: ['GET'])]
    public function api_result_last(): Response
    {
		// Check authorization and set game and user variables and such here
		$user = null;
		$game = null;

		// Return the most recently added game result from the filtered collection
		return $this->json([
			'result' => $user->getGameResultsFor($game)->last(),
		]);
    }

	#[Route('/achievement', name: 'app_api_achievement', methods: ['GET', 'POST'])]
    public function api_achievement(EntityManager $entityManager): Response
    {
		// Check authorization and set game and user variables and such here
		$user = null;
		$game = null;

		// The request is using the POST method
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				// Check - does the User already have this achievement?
				foreach ($user->getAchievementsFor($game) as $achievement) {
					if ($achievement->getName() === $_POST['name']) {
						// Return response with variable false
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
			} catch (BadRequestException $e) {
				throw $e;
			}
		}

		// Return the filtered collection
		return $this->json([
			'achievements' => $user->getAchievementsFor($game),
		]);
    }

	#[Route('/user', name: 'app_api_user', methods: ['GET'])]
    public function api_user(): Response
    {
		// Check authorization and set user variables and such here
		$user = null;

		// Return username and profile pic URL for identified user
		return $this->json([
			'username' => $user->getUsername(),
			'profilePic' => 'http://localhost:9001/' . $user->getProfilePic(),
		]);
    }
}
