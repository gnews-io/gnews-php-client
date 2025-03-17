<?php

namespace GNews\GNewsPhp\Tests;

use GNews\GNewsPhp\GNews;
use GNews\GNewsPhp\GNewsException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JsonException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class GNewsTest extends TestCase
{
    private const API_KEY = 'test-api-key';
    private GNews $gnews;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $this->gnews = new GNews(self::API_KEY);

        // Injecter le client HTTP mocké dans l'instance GNews
        $reflectionClass = new ReflectionClass(GNews::class);
        $property = $reflectionClass->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($this->gnews, $client);
    }

    /**
     * @throws ReflectionException
     * @throws GNewsException
     */
    public function testConstructor(): void
    {
        $gnews = new GNews(self::API_KEY, 'v3', 11000);

        $this->assertEquals('v3', $gnews->version);
        $this->assertEquals(11000, $gnews->timeout);
    }

    /**
     * @throws GNewsException
     * @throws JsonException
     */
    public function testSearchArticles(): void
    {
        $expectedResponse = [
            'totalArticles' => 1,
            'articles' => [
                ['title' => 'Test Article']
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse, JSON_THROW_ON_ERROR))
        );

        $result = $this->gnews->search('test query', ['lang' => 'fr']);

        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * @throws GNewsException
     * @throws JsonException
     */
    public function testGetHeadlines(): void
    {
        $expectedResponse = [
            'totalArticles' => 2,
            'articles' => [
                ['title' => 'Headline 1'],
                ['title' => 'Headline 2']
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($expectedResponse, JSON_THROW_ON_ERROR))
        );

        $result = $this->gnews->getHeadlines(['country' => 'fr']);

        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * @throws JsonException
     */
    public function testApiError(): void
    {
        $errorResponse = [
            'errors' => ['API rate limit exceeded']
        ];

        $this->mockHandler->append(
            new Response(429, [], json_encode($errorResponse, JSON_THROW_ON_ERROR))
        );

        $this->expectException(GNewsException::class);
        $this->expectExceptionMessage('API rate limit exceeded');

        $this->gnews->getHeadlines();
    }

    /**
     * @throws GNewsException
     */
    public function testRealSearchArticles(): void
    {
        // Vérifie si une clé API est disponible dans l'environnement
        $apiKey = self::API_KEY;

        if ($apiKey === 'test-api-key') {
            $this->markTestSkipped("No real API key available for integration tests");
        }

        $gnews = new GNews($apiKey);

        $result = $gnews->search('php programming', ['lang' => 'en', 'max' => 3]);

        $this->assertArrayHasKey('totalArticles', $result);
        $this->assertArrayHasKey('articles', $result);
        $this->assertIsArray($result['articles']);

        if (!empty($result['articles'])) {
            $article = $result['articles'][0];
            $this->assertArrayHasKey('title', $article);
            $this->assertArrayHasKey('description', $article);
            $this->assertArrayHasKey('url', $article);
        }
    }

    /**
     * @throws GNewsException
     */
    public function testRealGetHeadlines(): void
    {
        $apiKey = self::API_KEY;

        if ($apiKey === 'test-api-key') {
            $this->markTestSkipped("No real API key available for integration tests");
        }

        $gnews = new GNews($apiKey);

        $result = $gnews->getHeadlines(['country' => 'fr', 'max' => 3]);

        $this->assertArrayHasKey('totalArticles', $result);
        $this->assertArrayHasKey('articles', $result);
        $this->assertIsArray($result['articles']);

        if (!empty($result['articles'])) {
            $article = $result['articles'][0];
            $this->assertArrayHasKey('title', $article);
            $this->assertArrayHasKey('description', $article);
            $this->assertArrayHasKey('url', $article);
        }
    }
}