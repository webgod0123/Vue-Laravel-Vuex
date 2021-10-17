<?php

namespace App\Http\Controllers;

use Ramsey\Uuid\Uuid;

class ApiBaseController extends Controller {

    const OID = '2.16.100.1.1.113.1.1';
    const ADMINISTRATOR_NAME = 'ЧСИ 841 Неделчо Митев';
    const LAW_REASON = 'Тестове RegiX';

    const URL_TEST = 'https://regix-service-test.egov.bg/regix/RegiXEntryPoint.svc';
    
    const WSDL_TEST = 'https://regix-service-test.egov.bg/regix/RegiXEntryPoint.svc?singleWsdl';
    const WSDL_PRODUCTION = 'https://service-regix.egov.bg/RegiXEntryPoint.svc.singlewsdl.xml';

    public $isTesting = false;
    public $callContextUser = null;

    protected function prepareHeader($apiAction) 
    {
        $uuid = Uuid::uuid4();
        $nameSpace = 'http://www.w3.org/2005/08/addressing';
        
        if($this->isTesting) {
            $toSoap = 'https://regix-service-test.egov.bg/regix/RegiXEntryPoint.svc';
        } else {
            $toSoap = 'https://service-regix.egov.bg/RegiXEntryPoint.svc'; 
        }

        $action = new \SoapHeader($nameSpace, 'Action', $apiAction, true);
        $to = new \SoapHeader($nameSpace, 'To', $toSoap, true);
        $messageId = new \SoapHeader($nameSpace, 'MessageID', "urn:uuid:{$uuid}", false);
   
        return [
            'Action' => $action,
            'To' => $to, 
            'MessageID' => $messageId
        ];
    }

    protected function getTestCert() {
        return stream_context_create([
            'ssl' => [
                'local_cert' => storage_path('test.pem'),
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
    }

    protected function getProductionCert() {        
        return stream_context_create([
            'ssl' => [
                'local_cert' => storage_path('csi841.pem'),
                'local_pk' => storage_path('csi841.key'),
                'passphrase' => '25wjUcGdsV9AxGYpk!7fE5%^',
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);   
    }

    protected function getOptions() {
            
        $options = [
            'keep_alive' => true,
            'trace' => true,
            'exceptions' => true,
            'soap_version' => SOAP_1_2,
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'use' => SOAP_LITERAL,
            'style' => SOAP_DOCUMENT
        ];

        if($this->isTesting) {
            $options['stream_context'] = $this->getTestCert();
        } else {
            $options['stream_context'] = $this->getProductionCert();    
        }

        return $options;
    }

    
    protected function getUrl()
    {
        if($this->isTesting) {
            return self::WSDL_TEST;
        } 

        return self::WSDL_PRODUCTION;
    }

    protected function callContextFromLoggedInUser($caseNumber = '')
    {
        $callContext = new \stdClass;
        $callContext->ServiceURI = $caseNumber;
        $callContext->ServiceType = 'За проверовъчна дейност';
        $callContext->EmployeeIdentifier = $this->callContextUser->email;
        $callContext->EmployeeNames = $this->callContextUser->name;
        $callContext->EmployeePosition = $this->callContextUser->position;
        $callContext->AdministrationOId = self::OID;
        $callContext->AdministrationName = self::ADMINISTRATOR_NAME;
        $callContext->LawReason = 'Дело номер '.$caseNumber;
        
        return $callContext;
    }
}