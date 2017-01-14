<?php

namespace Salyangoz\ParasutRapor\Commands;

use Illuminate\Console\Command;
use Salyangoz\ParasutRapor\ParasutRapor;

class Report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parasut-rapor:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Paraşüt satış raporu oluşturur ve tanımlanan mail adreslerine gönderir.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        App(ParasutRapor::class)->report();
    }
}
