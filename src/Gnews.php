<?php

namespace Gnews\GnewsPhp;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Gnews
{
    /**
     * @var string GNews API key
     */
    protected string $apiKey;

    /**
     * @var string GNews API base URL
     */
    protected string $baseUrl = 'https://gnews.io/api/v4';

    /**
     * @var Client HTTP client
     */
    protected Client $httpClient;

    /**
     * @var array Default parameters
     */
    protected array $defaults = [
        'lang' => 'en',
        'country' => null,
        'max' => 10,
        'nullable' => true
    ];

    /**
     * Gnews constructor.
     *
     * @param string $apiKey GNews API key
     * @param array $config Optional configuration parameters
     */
    public function __construct(string $apiKey, array $config = [])
    {
        $this->apiKey = $apiKey;
        $this->httpClient = new Client();

        // Set optional configuration
        foreach ($config as $key => $value) {
            if (array_key_exists($key, $this->defaults)) {
                $this->defaults[$key] = $value;
            }
        }
    }

    /**
     * Search for news articles.
     *
     * @param string $query Search query
     * @param array $params Additional parameters
     * @return array Search results
     * @throws GnewsException If the API request fails
     */
    public static function search(string $query, array $params = []): array
    {
        if (!isset($params['apikey'])) {
            throw new GnewsException('API key is required for static method calls');
        }

        $instance = new static($params['apikey']);
        unset($params['apikey']);

        return $instance->searchArticles($query, $params);
    }

    /**
     * Search for news articles.
     *
     * @param string $query Search query
     * @param array $params Additional parameters
     * @return array Search results
     * @throws GnewsException If the API request fails
     */
    public function searchArticles(string $query, array $params = []): array
    {
        $requestParams = $this->prepareParams($params);
        $requestParams['q'] = $query;

        return $this->makeRequest('/search', $requestParams);
    }

    /**
     * Get top headlines.
     *
     * @param array $params Additional parameters
     * @return array Headlines
     * @throws GnewsException If the API request fails
     */
    public static function headlines(array $params = []): array
    {
        if (!isset($params['apikey'])) {
            throw new GnewsException('API key is required for static method calls');
        }

        $instance = new static($params['apikey']);
        unset($params['apikey']);

        return $instance->getHeadlines($params);
    }

    /**
     * Get top headlines.
     *
     * @param array $params Additional parameters
     * @return array Headlines
     * @throws GnewsException If the API request fails
     */
    public function getHeadlines(array $params = []): array
    {
        $requestParams = $this->prepareParams($params);

        return $this->makeRequest('/top-headlines', $requestParams);
    }

    /**
     * Prepare parameters for API request.
     *
     * @param array $params User-provided parameters
     * @return array Prepared parameters
     */
    protected function prepareParams(array $params): array
    {
        $requestParams = [
            'lang' => $params['lang'] ?? $this->defaults['lang'],
            'country' => $params['country'] ?? $this->defaults['country'],
            'max' => $params['max'] ?? $this->defaults['max'],
            'apikey' => $this->apiKey,
        ];

        // Optional parameters that can be passed directly
        $optionalParams = [
            'category', 'sortby', 'from', 'to', 'in', 'nullable', 'expand', 'topic', 'image'
        ];

        foreach ($optionalParams as $param) {
            if (isset($params[$param])) {
                $requestParams[$param] = $params[$param];
            }
        }

        // Remove null values if nullable is false
        $nullable = $params['nullable'] ?? $this->defaults['nullable'];
        if (!$nullable) {
            $requestParams = array_filter($requestParams, function ($value) {
                return $value !== null;
            });
        }

        return $requestParams;
    }

    /**
     * Make a request to the GNews API.
     *
     * @param string $endpoint API endpoint
     * @param array $params Request parameters
     * @return array API response data
     * @throws GnewsException If the API request fails
     */
    protected function makeRequest(string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . $endpoint;

        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => $params,
                'http_errors' => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents(), true);

            if ($statusCode !== 200) {
                throw new GnewsException(
                    isset($body['errors']) ? $body['errors'][0] : 'Unknown error occurred',
                    $statusCode
                );
            }

            return $body;
        } catch (GuzzleException $e) {
            throw new GnewsException('HTTP request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
