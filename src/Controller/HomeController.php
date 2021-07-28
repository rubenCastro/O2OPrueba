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

use App\Exception\ApiException;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/search", name="search_view")
     */
    public function searching(): Response
    {
        return $this->render('home/search.html.twig');
    }

    /**
     * @Route("/showAll", name="app_show_all")
     */
    public function showAll(APIPunkService $apiPunkService): Response
    {
            $beers = $apiPunkService->showAll();
            return new JsonResponse(['msg' => 'Todas las cervezas obtenidas', 'output' => $beers], Response::HTTP_OK);
            /*return $this->render('home/showAll.html.twig', [
                'list' => $output,
            ]);*/
        
    }

    /**
     * @Route("/beers", name="app_beers_filtered", methods={"GET"})
     */
    public function beersByFilterCriteria(Request $request, APIPunkService $apiPunkService)
    {
        $word = $request->query->get('word');

        if (empty($word)) {
            throw (new ApiException('error'))
                ->withPublicMessage('No se ha enviado el parametro de búsqueda')
                ->withHttpStatus(400);
        }

        $beers = $apiPunkService->searchByFood($word);

        return new JsonResponse(['msg' => 'Cervezas filtradas', 'output' => $beers], Response::HTTP_OK);
        /*return $this->render('home/list.html.twig', [
            'beers' => $output,
        ]);*/
    }

    /**
     * @Route("/beers/{id}", name="app_beers_by_id", methods={"GET"})
     */
    public function searchById(int $id, BeersRepository $beersRepository)
    {
        $output = array();

        $beers = $beersRepository->findAll();
        foreach($beers as $beer){
            $output[] = $beer->toArray();
        }
        return new JsonResponse(['msg' => 'Cerveza obtenida', 'output' => $output], Response::HTTP_OK);
        /*return $this->render('home/list.html.twig', [
            'beers' => $output,
        ]);*/
    }
}
