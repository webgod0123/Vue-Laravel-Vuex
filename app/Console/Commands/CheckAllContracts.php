<?php

namespace App\Console\Commands;

use DB;
use Log;
use Mail;
use Boolean;

use Carbon\Carbon;

use App\Models\Contract;

use App\Mail\ContractAdded;
use App\Mail\ContractRemoved;
use App\Mail\ContractChanged;

use App\Exceptions\BadRequestException;

use App\Http\Controllers\ApiController;

use Illuminate\Console\Command;

class CheckAllContracts extends Command
{
    
    protected $signature = 'contract:check-all {check?}';
    protected $description = 'Checks all the loaded contracts';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $api = new ApiController();
        $api->isTesting = Boolean::parse(env('TESTING_MODE'));

        $checkAll = $this->argument('check') === 'all';

        $query = Contract::query();
        if($checkAll) {
            $query->where(function($q) {
                $q->orWhere(DB::raw('DATE(last_check_on)'),'<',DB::raw('CURDATE()'));
                $q->orWhereNull('last_check_on');
            });
        } else {
            $query->whereNull('last_check_on');
        }
        $itemsToCheck = $query->get();
        
        foreach($itemsToCheck as $contract) {
            $api->callContextUser = $contract->createdBy;

            Log::info('Auto checking egn/bulstat(id:'.$contract->id.'): '.$contract->value);

            try {
                $result = $api->getContract($contract->value, $contract->case_number);
            } catch(BadRequestException $ex) {
                Log::info('Regix error: '.$ex->getMessage());
                continue;
            }

            $currentContracts = $result['contracts'];
            $previousContracts = $contract->last()->get();

            // compare 
            $changeType = null;
            if(count($currentContracts) > $previousContracts->count()) {
                $changeType = 'add_contract';
            } else if(count($currentContracts) < $previousContracts->count()) {
                $changeType = 'remove_contract';
            } else {
                foreach($currentContracts as $currentContract) {
                    $previous = $previousContracts->where('contractor_bulstat',$currentContract->contractor_bulstat)->first();
                    if($previous) {
                        foreach(array_keys($previous->toArray()) as $key) {
                            if($key === 'contract_id' || $key === 'created_at') {
                                continue;
                            }
                            if($currentContract->$key != $previous->$key) {
                                $changeType = 'change_contract';
                                break 2;
                            }
                        }
                    }
                }
            }
            $contract->change = $changeType;
            $contract->last_check_on = Carbon::now();
            $contract->save();

            // update current 
            $contract->status()->delete();
            if($result['count'] > 0) {
                $contract->status()->saveMany($currentContracts);
            }

            $user = $contract->createdBy;
            if($changeType === 'add_contract') {
                Mail::to($user)->cc(['boyko@mitev.bg'])->send(new ContractAdded($contract->id));
            } else if($changeType === 'remove_contract') {
                Mail::to($user)->cc(['boyko@mitev.bg'])->send(new ContractRemoved($contract->id));
            } else if($changeType === 'change_contract') {
                Mail::to($user)->cc(['boyko@mitev.bg'])->send(new ContractChanged($contract->id));
            }
        }   
    }
}
