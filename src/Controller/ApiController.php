<?php

namespace App\Controller;

use App\Service\TmdbApiServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/search", name="api_search")
     */
    public function getKeywords(TmdbApiServices $themoviedb, Request $request): JsonResponse
    {
        $keyword = $request->query->get('query');
        return $themoviedb->getKeywords($keyword);
    }

    /**
     * @Route("/api/rating", name="api_rating")
     */
    public function ratings(TmdbApiServices $themoviedb, Request $request): JsonResponse
    {
        $movieId = $request->request->get('movie_id');
        $score = $request->request->get('score');
        return $themoviedb->ratings($movieId, $score);
    }    
}
