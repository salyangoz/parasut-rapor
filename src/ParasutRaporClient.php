<?php

namespace Salyangoz\ParasutRapor;

use Salyangoz\ParasutRapor\Services\Report;

class ParasutRaporClient implements ParasutRapor
{
    private $config;
    private $parasut;

    public function __construct($config)
    {
        $this->config   = $config;

        $parasut        =   new ParasutAdapter($config["parasut"]);
        $this->parasut  =   $parasut->getParasutClient();

    }

    /**
     * Yeni bir rapor üretir ve iletir.
     */
    public function report() {

        $report = new Report($this->parasut);
        $report->create();

    }
}