<?php

namespace App\Http\Controllers;

use App\Http\ApiResponses\ContractResponse;
use App\Http\ApiResponses\ObligationResponse;

use App\Exceptions\BadRequestException;

class ApiController extends ApiBaseController
{
   
    public function getContract($egn, $caseNumber = '', $onlyActive = true)
    {
        $url = $this->getUrl();
        $options = $this->getOptions();
        $header = $this->prepareHeader('http://tempuri.org/IRegiXEntryPoint/ExecuteSynchronous');
        $filterString = $onlyActive ? 'Active' : 'All';

        $soapClient = new \SoapClient($url, $options);
        $soapClient->__setSoapHeaders($header);
        $isEGN = strlen($egn) === 10;
        if($isEGN) {
            $type = 'EGN';
        } else {
            $type = 'Bulstat';
        }

        $xml = "<EmploymentContractsRequest xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns=\"http://egov.bg/RegiX/NRA/EmploymentContracts/Request\">
                <Identity>
                    <ID>$egn</ID>
                    <TYPE>$type</TYPE>
                </Identity>
                <ContractsFilter>$filterString</ContractsFilter>
            </EmploymentContractsRequest>";

        $params = new \stdClass;
        $params->request = new \stdClass;
        $params->request->Argument = new \stdClass;
        $params->request->CallContext = new \stdClass;

        $params->request->Operation = 'TechnoLogica.RegiX.NRAEmploymentContractsAdapter.APIService.INRAEmploymentContractsAPI.GetEmploymentContracts';
        $params->request->Argument->any = $xml;
        $params->request->CallContext = $this->callContextFromLoggedInUser($caseNumber);
        $params->request->SignResult = true;
        $params->request->ReturnAccessMatrix = true;

        try {
            
            $request = $soapClient->ExecuteSynchronous($params);
            
            $response = new ContractResponse($request);
            $response->validate();

        } catch(\Exception $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $response->getContracts();
    }

    public function getObligations($egn, $caseNumber = '', $threshold = "0")
    {
        $url = $this->getUrl();
        $options = $this->getOptions();
        $header = $this->prepareHeader('http://tempuri.org/IRegiXEntryPoint/ExecuteSynchronous');

        $soapClient = new \SoapClient($url, $options);
        $soapClient->__setSoapHeaders($header);
        $isEGN = strlen($egn) === 10;
        if($isEGN) {
            $type = 'EGN';
        } else {
            $type = 'Bulstat';
        }

        $xml = "<ObligationRequest xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns=\"http://egov.bg/RegiX/NRA/Obligations/Request\">
                <Identity>
                    <ID>$egn</ID>
                    <TYPE>$type</TYPE>
                </Identity>
                <Threshold>$threshold</Threshold>
            </ObligationRequest>";

        $params = new \stdClass;
        $params->request = new \stdClass;
        $params->request->Argument = new \stdClass;
        $params->request->CallContext = new \stdClass;

        $params->request->Operation = 'TechnoLogica.RegiX.NRAObligatedPersonsAdapter.APIService.INRAObligatedPersonsAPI.GetObligatedPersons';
        $params->request->Argument->any = $xml;
        $params->request->CallContext = $this->callContextFromLoggedInUser($caseNumber);
        $params->request->SignResult = true;
        $params->request->ReturnAccessMatrix = true;

        try {
            
            $request = $soapClient->ExecuteSynchronous($params);
            
            $response = new ObligationResponse($request);
            $response->validate();

        } catch(\Exception $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $response->getObligations();
    }
}
