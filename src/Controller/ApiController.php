<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\BeersRepository;
use App\Service\ApiPunkService;

class ApiController extends AbstractController
{
    /**
     * @Route("/showAllBeers", name="app_show_all", methods={"GET"})
     */
    public function showAllBeers(APIPunkService $apiPunkService): JsonResponse
    {
        $beers = $apiPunkService->showAllBeers();

        return new JsonResponse(['msg' => 'Todas las cervezas obtenidas', 'output' => $beers], Response::HTTP_OK);
    }

    /**
     * @Route("/beers", name="app_beers_filtered", methods={"GET"})
     */
    public function beersByFood(Request $request, APIPunkService $apiPunkService): JsonResponse
    {
        $word = $request->query->get('word');

        $beers = $apiPunkService->searchByFood($word);

        return new JsonResponse(['msg' => 'Cervezas filtradas', 'output' => $beers], Response::HTTP_OK);
    }

    /**
     * @Route("/beer/{id}", name="app_beers_by_id", methods={"GET"})
     */
    public function searchById(int $id, APIPunkService $apiPunkService): JsonResponse
    {
        $output = array();

        $output = $apiPunkService->getBeer($id);

        return new JsonResponse(['msg' => 'Cerveza obtenida', 'output' => $output], Response::HTTP_OK);
    }
}
