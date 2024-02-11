<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
	#[Route('/', name: 'app_index', methods: ['GET'])]
    #[Route('/homepage', name: 'app_homepage', methods: ['GET'])]
	#[Route('/homepage/all', name: 'app_homepage_all', methods: ['GET'])]
	public function index(GameRepository $gameRepository): Response
	{
		return $this->render('homepage/index_all.html.twig', [
			'games' => $gameRepository->findBy(['public' => true]),
		]);
    }
}