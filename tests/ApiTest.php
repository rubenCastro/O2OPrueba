<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

use App\Exception\ApiException;

class ApiTest extends TestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = new \GuzzleHttp\Client();
    }

    public function testShowAllBeers(): void
    {
        $response = $this->client->get('http://localhost:8000/showAllBeers');
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testBeersByFoodEmptyWord(): void
    {
        $this->expectException(\Exception::class);
        $response = $this->client->request('GET', 'http://localhost:8000/beers', [
            'query' => ['word' => '']
        ]);
    }

    public function testBeersByFoodNonExistingWord(): void
    {
        $response = $this->client->request('GET', 'http://localhost:8000/beers', [
            'query' => ['word' => 'supercalifragilistico']
        ]);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testBeersByFoodCorrectData(): void
    {
        $response = $this->client->request('GET', 'http://localhost:8000/beers', [
            'query' => ['word' => 'lemon']
        ]);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testSearchByIdNonNumericId(): void
    {
        $id = "supercalifragilistico";
        $this->expectException(\Exception::class);
        $response = $this->client->request('GET', 'http://localhost:8000/beer/' . $id);
    }

    public function testSearchByIdNonExistingId(): void
    {
        $id = 1111111;
        $this->expectException(\Exception::class);
        $response = $this->client->request('GET', 'http://localhost:8000/beer/' . $id);
    }

    public function testSearchById(): void
    {
        $id = 1;
        $response = $this->client->request('GET', 'http://localhost:8000/beer/' . $id);
        $this->assertEquals($response->getStatusCode(), 200);
    }
}