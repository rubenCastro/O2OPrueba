<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FoodPairingRepository;
use App\Repository\BeersRepository;

use App\Entity\Beers;
use App\Entity\FoodPairing;

class ApiPunkService 
{
    private $client;
    private $foodPairingRepository;
    private $beersRepository;
    private $em;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $em, FoodPairingRepository $foodPairingRepository, BeersRepository $beersRepository)
    {
        $this->client = $client;
        $this->em = $em;
        $this->foodPairingRepository = $foodPairingRepository;
        $this->beersRepository = $beersRepository;
    }

    public function searchByFood($text)
    {
        $output = [];

        $foodPairings = $this->foodPairingRepository->findByExampleField($text);
        foreach($foodPairings as $foodPairing){
            $beers = $foodPairing->getBeers();
            $this->parseBeers($beers, $output);
        }
        return $output;
    }

    public function showAll()
    {
        $output = [];

        $beers = $this->beersRepository->findAll();
        $output = $this->parseBeers($beers);

        return $output;
    }

    public function showAllFromApi()
    {
        $response = $this->client->request(
            'GET',
            'https://api.punkapi.com/v2/beers'
        );

        return $response->getContent();
    }

    private function parseBeers($beers, &$output=array())
    {
        foreach($beers as $beer){
            $output[] = $beer->toArray();
        }
        return $output;
    }

    public function saveDatabaseFromApi()
    {
        $dataApi = json_decode($this->showAllFromApi(), true);
        foreach($dataApi as $beer){
            $beerObj = $this->beersRepository->find($beer['id']);
            if(!$beerObj){
                $beerObj = new Beers();
            }
            if(!isset($beer['id']) || !isset($beer['name']) || !isset($beer['description']) || !isset($beer['image_url']) || !isset($beer['tagline'])){
                throw new \Exception("Faltan datos");
            }
            $beerObj->setId($beer['id']);
            $beerObj->setName($beer['name']);
            $beerObj->setDescription($beer['description']);
            $beerObj->setImage($beer['image_url']);
            $beerObj->setSlogan($beer['tagline']);
            $date=date_create_from_format("m/Y",$beer['first_brewed']);
            $beerObj->setFirstBrewed($date);

            foreach($beer['food_pairing'] as $food){
                $foodObj = $this->foodPairingRepository->findOneBy(array('name' => $food));
                if(!$foodObj){
                    $foodObj = new FoodPairing();
                }
                $foodObj->setName($food);
                

                $this->em->persist($foodObj);
                $this->em->flush();

                $beerObj->addFoodPairing($foodObj);
            }

            $this->em->persist($beerObj);
            $this->em->flush();
        }
    }
}