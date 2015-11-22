<?php

namespace Booking\Token\Api;

use Booking\Token\Credentials;
use Booking\Token\Statistics;

class TokenApiClient
{
    private $uri;
    /**
     * @var Statistics
     */
    private $statistic;

    /**
     * TokenApi constructor.
     * @param $uri
     * @param Statistics $statistic
     */
    public function __construct($uri, Statistics $statistic)
    {
        $this->uri = $uri;
        $this->statistic = $statistic;
    }

    /**
     * @return Credentials
     * @throws \Exception
     */
    public function getTokenData()
    {
        $client = new \GuzzleHttp\Client(['base_uri' => $this->uri]);
        $response = $client->get('/api/1.0/token/');
        $contents = $response->getBody()->getContents();
        $data = json_decode($contents, true);
        if (false == $data || !isset($data['token']) || !isset($data['session'])) {
            throw new \Exception('Token and session data is incorrect');
        }
        $this->statistic->storeTokenUsage();
        return new Credentials($data['token'], $data['session']);
    }
}