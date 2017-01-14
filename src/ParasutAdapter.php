<?php

namespace Salyangoz\ParasutRapor;
use Parasut\Client as ParasutClient;

class ParasutAdapter
{

    private $parasut;

    public function __construct(array $config) {

        // create a new client instance
        $this->parasut = new ParasutClient([
            'client_id'     => array_get($config, 'client_id'),
            'client_secret' => array_get($config, 'client_secret'),
            'username'      => array_get($config, 'username'),
            'password'      => array_get($config, 'password'),
            'company_id'    => array_get($config, 'company_id'),
            'grant_type'    => 'password',
            'redirect_uri'  => 'urn:ietf:wg:oauth:2.0:oob',
        ]);

        $this->parasut->authorize();

    }

    /**
     * Parasut paketi istemcisini döndürür. Paraşüt istekleri bu istemci üzerinden iletilir.
     * @return ParasutClient
     */
    public function getParasutClient() {

        return $this->parasut;

    }

}