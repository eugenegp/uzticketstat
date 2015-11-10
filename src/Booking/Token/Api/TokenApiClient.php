<?php

namespace Booking\Token\Api;

use Booking\Token\Credentials;

class TokenApiClient
{
    private $uri;

    /**
     * TokenApi constructor.
     * @param $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
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
        return new Credentials($data['token'], $data['session']);
    }
}