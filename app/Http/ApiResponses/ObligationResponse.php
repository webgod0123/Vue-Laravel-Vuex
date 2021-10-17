<?php 

namespace App\Http\ApiResponses;

use App\Exceptions\BadRequestException;

class ObligationResponse { 

    protected $response;
    protected $parsedResponse;

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

    public function getObligations()
    {
        return [
            'identity' => (string)$this->parsedResponse->Identity->ID,
            'identityType' => (string)$this->parsedResponse->Identity->TYPE,
            'name' => (string)$this->parsedResponse->Name,
            'hasObligations' => (string)$this->parsedResponse->ObligationStatus === 'PRESENT',
            'code' => (string)$this->parsedResponse->Status->Code
        ];
    }
}