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
use App\Exception\ApiException;

class ApiPunkService 
{
    private $client;
    private $foodPairingRepository;
    private $beersRepository;
    private $em;
    private $endpoint;

    public function __construct(string $apiEndpoint, HttpClientInterface $client, EntityManagerInterface $em, FoodPairingRepository $foodPairingRepository, BeersRepository $beersRepository)
    {
        $this->client = $client;
        $this->em = $em;
        $this->foodPairingRepository = $foodPairingRepository;
        $this->beersRepository = $beersRepository;
        $this->endpoint = $apiEndpoint;
    }

    public function searchByFood($text)
    {
        $output = [];

        if (empty($text)) {
            throw (new ApiException('Error'))
                ->withPublicMessage('Search parameter has not been sent')
                ->withHttpStatus(400);
        }

        $foodPairings = $this->foodPairingRepository->findByNameField($text);
        foreach($foodPairings as $foodPairing) {
            $beers = $foodPairing->getBeers();
            $this->parseBeers($beers, $output);
        }
        return $output;
    }

    public function showAllBeers()
    {
        $output = [];

        $beers = $this->beersRepository->findAll();
        $output = $this->parseBeers($beers);

        return $output;
    }

    public function getBeer($id)
    {
        $output = [];

        if(!$id || !is_numeric($id)) {
            throw (new ApiException('Error'))
                ->withPublicMessage('Data not sent correctly')
                ->withHttpStatus(400);
        }

        $beer = $this->beersRepository->find($id);
        
        if(!$beer){
            throw (new ApiException('Error'))
                ->withPublicMessage('Beer not found')
                ->withHttpStatus(400);
        }
        $output = $beer->toArray();
        return $output;
    }

    public function showAllBeersFromApi()
    {
        $response = $this->client->request(
            'GET',
            $this->endpoint
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
        $dataApi = json_decode($this->showAllBeersFromApi(), true);
        foreach($dataApi as $beer) {
            if(!isset($beer['id'], $beer['name'], $beer['description'], $beer['image_url'], $beer['tagline'])) {
                throw (new ApiException('Error'))
                ->withPublicMessage('Data not sent correctly')
                ->withHttpStatus(400);
            }
            $beerObj = $this->beersRepository->find($beer['id']);
            if(!$beerObj){
                $beerObj = new Beers();
            }
            $date=date_create_from_format("m/Y",$beer['first_brewed']);
            $beerObj->setId($beer['id'])
                ->setName($beer['name'])
                ->setDescription($beer['description'])
                ->setImage($beer['image_url'])
                ->setSlogan($beer['tagline'])
                ->setFirstBrewed($date);

            foreach($beer['food_pairing'] as $food) {
                $foodObj = $this->foodPairingRepository->findOneBy(array('name' => $food));
                if(!$foodObj) {
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