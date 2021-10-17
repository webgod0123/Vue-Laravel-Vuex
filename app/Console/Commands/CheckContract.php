<?php

namespace App\Console\Commands;

use App\Models\Contract;
use Illuminate\Console\Command;

class CheckContract extends Command
{

    protected $signature = 'contract:check {egn} {case_number}';
    protected $description = 'Checks single contract';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        
    }
}
