<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SandersITController;
use App\Http\Controllers\FareController;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        $fare = new FareController();
//        $fare->convert();
//
//        $sandersIt = new SandersITController();
//        $sandersIt->convert();

        $promiData = new DashboardController();
        $promiData->index();
    }
}
