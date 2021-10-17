<?php 

namespace App\Http\ApiResponses;

use App\Exceptions\BadRequestException;
use App\Models\ContractCurrentStatus;

class ContractResponse { 

    protected $response;
    protected $parsedResponse;

    // 01 - безсрочен трудов договор по чл. 67, ал. 1, т. 1 КТ;
    // 02 - срочен трудов договор по чл. 68, ал. 1, т. 1 КТ;
    // 03 - трудов договор по чл. 68, ал. 1, т. 2 КТ;
    // 04 - трудов договор по чл. 68, ал. 1, т. 3 КТ;
    // 05 - трудов договор по чл. 68, ал. 1, т. 4 КТ;
    // 06 - трудов договор по чл. 68, ал. 1, т. 5 КТ;
    // 08 - споразумение по чл. 107 във връзка с чл. 83 КТ;
    // 09 - споразумение по чл. 107 във връзка с чл. 89 КТ;
    // 10 - допълнителен трудов договор по чл. 110 КТ;
    // 11 - допълнителен трудов договор по чл. 111 КТ;
    // 12 - трудов договор по чл. 114 КТ;
    // 13 - постановление по чл. 405а КТ;
    // 14 - трудов договор за ученичество по чл. 230 КТ;
    // 15 - трудов договор за вътрешно заместване по чл. 259 КТ;
    // 16 - трудов договор по чл. 233б КТ.

    public function __construct($response)
    {
        $this->response = $response;    
    }

    public function validate() {
        if($this->response->ExecuteSynchronousResult->HasError === true) {
            throw new BadRequestException('Грешка от Regix: '.$this->response->ExecuteSynchronousResult->Error.' (ServiceCallID:'.$this->response->ExecuteSynchronousResult->ServiceCallID.')');
        }

        $this->parsedResponse = simplexml_load_string($this->response->ExecuteSynchronousResult->Data->Response->any);
        if ($this->parsedResponse === false) {
            throw new BadRequestException('Неуспешна обработка на отговор от Regix (вероятно невалиден XML)');
        }
    }

    public function getContracts()
    {
        return [
            'count' => $this->getContractCount(),
            'contracts' => $this->parseContracts()
        ];
    }

    public function getContractCount() {
        return count($this->parsedResponse->EContracts->EContract);
    }

    private function parseContracts() {
        $contracts = [];
        
        foreach($this->parsedResponse->EContracts->EContract as $contract) {
            $con = [];
            foreach($contract as $k => $v) {
                $con[$k] = (string)$v;
            }
            if(!empty($con)) {
                $contract = new ContractCurrentStatus();
                $contracts[] = $contract->parseFromApi($con);
            }
        }
        return $contracts;
    }
}