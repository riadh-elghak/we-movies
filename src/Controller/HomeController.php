<?php

namespace App\Controller;

use App\Service\TmdbApiServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(TmdbApiServices $themoviedb , Request $request): Response
    {

       // dump($themoviedb->ratings('359410', '8.5'));die;
        $keyword = $request->request->get('search');
        $genres = $request->query->get('genres');
        $popular = $themoviedb->movies(null, null, true);
        $movies = $themoviedb->movies($genres, $keyword, false);

        $genres = $themoviedb->genres();
        return $this->render('home/index.html.twig', [
            'movies' => $movies,
            'popular' => $popular,
            'genres' => $genres,
        ]);
    }
   
}
