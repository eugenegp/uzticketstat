<?php

namespace Booking\Token;


class Credentials
{
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $session;


    /**
     * Credentials constructor.
     * @param string $token
     * @param string $session
     */
    public function __construct($token, $session)
    {
        $this->token = $token;
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

}