<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Form\GameType;
use App\Repository\AchievementRepository;
use DateTimeImmutable;
use App\Repository\GameResultRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'app_game_index', methods: ['GET'])]
    public function index(): Response
    {
		return $this->redirectToRoute('app_homepage');
    }

    #[Route('/new', name: 'app_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			/** @var UploadedFile $imageFile */
			$imageFile = $form->get('image')->getData();
			if ($imageFile) {
				$filename = uniqid().'.'.$imageFile->guessExtension();
				try {
					$imageFile->move(
						'assets/images/games/',
						$filename
					);
                } catch (FileException $e) {
					throw $e;
				}
				$game->setImage('assets/images/games/' . $filename);
			}
			// set developer - important!!
			$game->setDeveloper($this->getUser());
			// generate API key
			$game->setApiKey(uniqid("", true));
			// also store creation time
			$game->setCreatedOn(new DateTimeImmutable('now'));
			$game->setUpdatedOn(new DateTimeImmutable('now'));
			// persist & flush data
            $entityManager->persist($game);
            $entityManager->flush();
			// redirect to game page
			return $this->redirectToRoute('app_game_show', ['id' => $game->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'gameForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_show', methods: ['GET'])]
	#[Route('/{id}/leaderboard', name: 'app_game_show_results', methods: ['GET'])]
    public function show_results(Game $game) : Response
    {
		/** @var $appUser User */
		$appUser = $this->getUser();
		if (!$game->isPublic() &&
				!$appUser->isAdmin() &&
				$appUser != $game->getDeveloper() &&
				!in_array($appUser, $game->getDeveloper()->getFriends()->toArray())) {
			throw $this->createAccessDeniedException('You are not authorized to access this game!');
		}
		$sort = 'score';
		if (isset($_GET['sort'])) {
			$sort = htmlspecialchars($_GET['sort']);
		};
        return $this->render('game/show_results.html.twig', [
            'game' => $game,
			'developer' => $game->getDeveloper(),
			'gameResults' => $game->getGameResults(),
			'sort' => $sort,
        ]);
    }

	#[Route('/{id}/achievements', name: 'app_game_show_achievements', methods: ['GET'])]
    public function show_achievements(Game $game, AchievementRepository $achievementRepository, GameResultRepository $gameResultRepository): Response
    {
		/** @var $appUser User */
		$appUser = $this->getUser();
		if (!$game->isPublic() &&
				!$appUser->isAdmin() &&
				$appUser != $game->getDeveloper() &&
				!in_array($appUser, $game->getDeveloper()->getFriends()->toArray())) {
			throw $this->createAccessDeniedException('You are not authorized to access this game!');
		}

		$achievements = $game->getUniqueAchievements();
		$achievementCounts = [];
		foreach ($achievements as $achievement) {
			$achievementCounts[] = $achievement->getAchieverCount($achievementRepository) / $achievement->getGame()->getPlayerCount($gameResultRepository);
		}
		
        return $this->render('game/show_achievements.html.twig', [
            'game' => $game,
			'developer' => $game->getDeveloper(),
			'achievements' => $achievements,
			'achievementCounts' => $achievementCounts,
        ]);
    }

	#[Route('/{id}/stats', name: 'app_game_show_stats', methods: ['GET'])]
    public function show_stats(Game $game): Response
    {
		/** @var $appUser User */
		$appUser = $this->getUser();
		if (!$game->isPublic() &&
				!$appUser->isAdmin() &&
				$appUser != $game->getDeveloper() &&
				!in_array($appUser, $game->getDeveloper()->getFriends()->toArray())) {
			throw $this->createAccessDeniedException('You are not authorized to access this game!');
		}
		$scoreChartData = [
			['Time', 'Score', ['role' => 'annotation']],
			[$game->getCreatedOn(), 0, null],
		];
		foreach ($game->getGameResults() as $gameResult) {
			$scoreChartData[] = [$gameResult->getAchievedOn(), $gameResult->getScore(), $gameResult->getUser()->getUsername()];
		};
		$scoreChart = new LineChart();
		$scoreChart->getData()->setArrayToDataTable($scoreChartData);
		$scoreChart->getOptions()->setTitle('Score History');
		$scoreChart->getOptions()->setHeight(500);
		$scoreChart->getOptions()->setWidth(900);
		$scoreChart->getOptions()->setPointsVisible(true);
        return $this->render('game/show_stats.html.twig', [
            'game' => $game,
			'developer' => $game->getDeveloper(),
			'scoreChart' => $scoreChart,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
		/** @var $appUser User */
		$appUser = $this->getUser();
		if ($appUser != $game->getDeveloper() && !$appUser->isAdmin() ) {
			throw $this->createAccessDeniedException('You are not authorized to edit this game!');
		}

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			/** @var UploadedFile $imageFile */
			$imageFile = $form->get('image')->getData();
			if ($imageFile) {
				unlink($game->getImage());
				$filename = uniqid().'.'.$imageFile->guessExtension();
				try {
					$imageFile->move(
						'assets/images/games/',
						$filename
					);
                } catch (FileException $e) {
					throw $e;
				}
				$game->setImage('assets/images/games/' . $filename);
			}
			// also store time of update
			$game->setUpdatedOn(new DateTimeImmutable('now'));
			// flush data
            $entityManager->flush();
			// redirect to game page
			return $this->redirectToRoute('app_game_show', ['id' => $game->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'gameForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
		/** @var $appUser User */
		$appUser = $this->getUser();
		if ($appUser != $game->getDeveloper() && !$appUser->isAdmin() ) {
			throw $this->createAccessDeniedException('You are not authorized to delete this game!');
		}

        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
			unlink($game->getImage());
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    }
}
