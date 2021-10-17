<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use Boolean;

use Carbon\Carbon;

use App\Models\Contract;
use App\Models\ContractLastStatus;

use App\Exceptions\BadRequestException;

use Illuminate\Http\Request;

class RequestController extends ApiController
{
    public function contract(Request $GET) 
    {
        
        $this->validateHttpRequest($GET);
        
        // if($this->isTesting === false) {
        //     Log::channel('slack')->critical('New report from '.Auth::user()->name.': EGN '.$GET['egn'].', case number: '.$GET['case_number']);
        // }

        $contractId = $GET['contract_id'];
        $result = $this->getContract($GET['egn'],$GET['case_number'], Boolean::parse($GET['only_active']));

        if($contractId) {
            $contract = Contract::find($contractId);
            $contract->last_check_on = Carbon::now();
            $contract->change = null;
            $contract->acknowledged_by = null;
            $contract->save();

            // save contracts
            $contract->status()->delete();
            $contract->status()->saveMany($result['contracts']); // 'contracts' are taken and parsed as models from api

            // save last 
            $contract->last()->delete();
            $contract->last()->saveMany(array_map(function($c) {
                return new ContractLastStatus($c->toArray());
            },$result['contracts']));

            // get relations and return
            $contract->status;
            $contract->createdBy;
            
            return $contract;
        }

        return $result;

    }

    public function obligation(Request $GET)
    {
        $this->validateHttpRequest($GET);

        if(!$GET['threshold'] || $GET['threshold'] < 0 || $GET['threshold'] > 1000) {
            throw new BadRequestException('Прага за проверка на задължения трябва да бъде цяло число между 0 и 1000.');
        }

        return $this->getObligations($GET['egn'],$GET['case_number'],$GET['threshold']);
    }

    private function validateHttpRequest(Request $GET)
    {
        if(!$GET['egn'] || !$GET['case_number']) {
            throw new BadRequestException('ЕГН и номер на дело са задължителни');
        }

        $this->isTesting = Boolean::parse(env('TESTING_MODE'));
        $this->callContextUser = Auth::user();
    }
}
