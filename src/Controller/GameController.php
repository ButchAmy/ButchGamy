<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\GameResultsRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_show', methods: ['GET'])]
	#[Route('/{id}/leaderboard', name: 'app_game_show_results', methods: ['GET'])]
    public function show(Game $game) : Response
    {
		$sort = 'score';
		if (isset($_GET['sort'])) {
			$sort = htmlspecialchars($_GET['sort']);
		};
        return $this->render('game/show.html.twig', [
            'game' => $game,
			'developer' => $game->getDeveloper(),
			'gameResults' => $game->getGameResults(),
			'sort' => $sort,
        ]);
    }

	#[Route('/{id}/stats', name: 'app_game_show_stats', methods: ['GET'])]
    public function show_stats(Game $game): Response
    {
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
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    }
}
