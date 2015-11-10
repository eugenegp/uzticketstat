<?php

namespace Booking\Loader;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Parse tickets from http://booking.uz.gov.ua/
 */
class UzLoader
{

    const URL = 'http://booking.uz.gov.ua/ru/purchase/search/';
    const REFERER = 'http://booking.uz.gov.ua/ru/';
    private $userAgents = [
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/45.0.2454.101 Chrome/45.0.2454.101 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
        'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
        'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
    ];

    public function load($deprtureCode, $arrivalCode, \DateTime $date)
    {
        exec('cd ' . ROOT_DIR . '&& docker run  -v `pwd`:/mnt/test --rm  --entrypoint=/usr/bin/phantomjs  cmfatih/phantomjs   /mnt/test/src/Booking/Resources/uz-data-fetcher.js', $rawData, $code);

        if ($code) {
            throw new \Exception("Exit code $code");
        }

        if (empty($rawData) || count($rawData) != 2) {
            throw new \Exception("Can't init session from UZ " . implode("\n", $rawData));
        }
        // TODO validate;
        $token = $rawData[0];
        $session = $rawData[1];

        if (strlen($token) != 32) {
            throw new \Exception("Can't get token from string [$token]");
        }

        $client = new \GuzzleHttp\Client();
        $sendData = array(
            'station_id_from' => $deprtureCode,
            'station_id_till' => $arrivalCode,
            'date_dep' => $date->format('d.m.Y'),
            'time_dep' => '00:00',
            'time_dep_till' => '',
            'another_ec' => 0,
            'search' => '',
        );
        $response = $client->post(self::URL, [
            'form_params' => $sendData,
            'headers' => [
                'User-Agent' => $this->userAgents[rand(0,count($this->userAgents) - 1)],
                'referer'     => self::REFERER,
                'Cookie' => "_gv_sessid=$session; path=/",
                'Accept-Encoding' => 'gzip, deflate, sdch',
                'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4,uk;q=0.2',
                'Upgrade-Insecure-Requests' => '1',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Cache-Control' => 'max-age=0',
                'Connection' => 'keep-alive',
                'GV-Token' => $token,
                'GV-Ajax' => 1,
                'GV-Screen' => '1920x1200',
                'GV-Referer' => 'http://booking.uz.gov.ua/ru/',
                'GV-Unique-Host' => 1,
            ],
        ]);
        return $response->getBody()->getContents();
    }

}
