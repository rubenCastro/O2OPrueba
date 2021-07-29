<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

use App\Repository\FoodPairingRepository;
use App\Repository\BeersRepository;

use App\Entity\Beers;
use App\Entity\FoodPairing;

use App\Service\ApiPunkService;

use App\Exception\ApiException;

class ApiPunkServiceTest extends TestCase
{
    private $httpClient;
    private $em;
    private $foodPairingRepository;
    private $beersRepository;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->foodPairingRepository = $this->createMock(FoodPairingRepository::class);
        $this->beersRepository = $this->createMock(BeersRepository::class);
    }

    public function testShowAllBeers(): void 
    {
        $beer = new Beers();
        $beer->setId(99);
        $beer->setName('prueba');
        $beer->setDescription('lorem ipsum');
        $beer->setImage('https://images.punkapi.com/v2/keg.png');
        $beer->setSlogan('lorem ipsum');
        $beer->setFirstBrewed(new \Datetime());

        $this->beersRepository->method('findAll')
                              ->willReturn(array($beer));
        
        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $output = $apiPunkService->showAllBeers();
        $this->assertEquals(!empty($output), true);
    }

    public function testShowAllBeersFromApi(): void 
    {
        $beersInfo = file_get_contents(__DIR__."/responsesApi/beers.json");
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->once())
                    ->method('getContent')
                    ->willReturn($beersInfo);

        $this->httpClient->expects($this->once())
                         ->method('request')
                         ->with('GET','https://api.punkapi.com/v2/beers')
                         ->willReturn($mockResponse);
        
        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $output = $apiPunkService->showAllBeersFromApi();
        $this->assertEquals(!empty($output), true);
    }

    public function testSearchByFoodNonWord(): void 
    {
        $word = "";

        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $this->expectException(ApiException::class);
        $output = $apiPunkService->searchByFood($word);
    }

    public function testSearchByFoodNonExistingWord(): void 
    {
        $word = "supercalifragilistico";

        $this->foodPairingRepository->method('findByNameField')
                                    ->willReturn(array());

        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $output = $apiPunkService->searchByFood($word);
        $this->assertEquals(empty($output), true);
    }

    public function testSearchByFoodExistingWord(): void 
    {
        $word = "lemon";

        $beer = new Beers();
        $beer->setId(99);
        $beer->setName('prueba');
        $beer->setDescription('lorem ipsum');
        $beer->setImage('https://images.punkapi.com/v2/keg.png');
        $beer->setSlogan('lorem ipsum');
        $beer->setFirstBrewed(new \Datetime());

        $food = new FoodPairing();
        $food->setName("lemon spicy");
        $food->addBeer($beer);
        
        $beer->addFoodPairing($food);

        $this->foodPairingRepository->method('findByNameField')
                                    ->willReturn(array($food));

        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $output = $apiPunkService->searchByFood($word);
        $this->assertEquals(!empty($output), true);
    }

    public function testSaveDatabaseFromApiFailedData(): void 
    {
        $beersInfo = file_get_contents(__DIR__."/responsesApi/beersFailedData.json");
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->once())
                    ->method('getContent')
                    ->willReturn($beersInfo);

        $this->httpClient->expects($this->once())
                         ->method('request')
                         ->with('GET','https://api.punkapi.com/v2/beers')
                         ->willReturn($mockResponse);
        
        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $this->expectException(\Exception::class);
        $output = $apiPunkService->saveDatabaseFromApi();
    }

    public function testSaveDatabaseFromApiNonCreateObjects(): void 
    {
        $beersInfo = file_get_contents(__DIR__."/responsesApi/beers.json");
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->once())
                    ->method('getContent')
                    ->willReturn($beersInfo);

        $this->httpClient->expects($this->once())
                         ->method('request')
                         ->with('GET','https://api.punkapi.com/v2/beers')
                         ->willReturn($mockResponse);
        
        $beer = new Beers();
        $beer->setId(99);
        $beer->setName('prueba');
        $beer->setDescription('lorem ipsum');
        $beer->setImage('https://images.punkapi.com/v2/keg.png');
        $beer->setSlogan('lorem ipsum');
        $beer->setFirstBrewed(new \Datetime());
        $this->beersRepository->method('find')
                              ->willReturn($beer);
        
        $food = new FoodPairing();
        $food->setName("lemon spicy");
        $food->addBeer($beer);
        $this->foodPairingRepository->method('findOneBy')
                                    ->willReturn($food);

        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $apiPunkService->saveDatabaseFromApi();

        $this->assertEquals(true, true);
    }

    public function testSaveDatabaseFromApiCreateObjects(): void 
    {
        $beersInfo = file_get_contents(__DIR__."/responsesApi/beers.json");
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->once())
                    ->method('getContent')
                    ->willReturn($beersInfo);

        $this->httpClient->expects($this->once())
                         ->method('request')
                         ->with('GET','https://api.punkapi.com/v2/beers')
                         ->willReturn($mockResponse);
        
        $this->beersRepository->method('find')
                              ->willReturn(null);
        
        $this->foodPairingRepository->method('findOneBy')
                                    ->willReturn(null);

        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $apiPunkService->saveDatabaseFromApi();

        $this->assertEquals(true, true);
    }

    public function beerDataProvider()
    {
        return [
            [''],
            ['test']
        ]; 
    }

    /**
     * @dataProvider beerDataProvider
     */
    public function testGetBeerWrongData($id): void 
    {
        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $this->expectException(ApiException::class);
        $apiPunkService->getBeer($id);
    }

    public function testGetBeerNonFoundBeer(): void 
    {
        $id = 12345;
        $this->beersRepository->method('find')
                              ->willReturn(null);

        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $this->expectException(ApiException::class);
        $apiPunkService->getBeer($id);
    }

    public function testGetBeerFoundBeer(): void 
    {
        $id = 1;
        $beer = new Beers();
        $beer->setId(99);
        $beer->setName('prueba');
        $beer->setDescription('lorem ipsum');
        $beer->setImage('https://images.punkapi.com/v2/keg.png');
        $beer->setSlogan('lorem ipsum');
        $beer->setFirstBrewed(new \Datetime());

        $food = new FoodPairing();
        $food->setName("lemon spicy");
        
        $beer->addFoodPairing($food);

        $this->beersRepository->method('find')
                              ->willReturn($beer);

        $apiPunkService = new ApiPunkService($this->httpClient, $this->em, $this->foodPairingRepository, $this->beersRepository);
        $output = $apiPunkService->getBeer($id);

        $this->assertEquals(!empty($output), true);
    }
}
