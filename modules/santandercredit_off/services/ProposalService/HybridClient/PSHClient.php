<?php

include_once "Application.php";
include_once "AppStateDocument.php";
include_once "ClientIdentity.php";
include_once "FinancialDataResult.php";
include_once "IsActiveResult.php";
include_once "OperationStatus.php";
include_once "Simulation.php";


class PSHClient {
    
    private $client;
    private $login;
    private $pass;
    private $pemCert;
    private $serviceLocation;
    private $shopNumber;
    public $applications;
    public $operationStatus;
    public $lastException;
    public $isCorrect;
    public $simulationResult;
    

    public function __construct($login, $pass, $serviceLocation, $pemCert, $shopNumber) {
        $this->applications = new ArrayObject();
        $this->operationStatus = new OperationStatus();
        $this->login = $login;
        $this->pass = $pass;
        $this->serviceLocation = $serviceLocation;
        $this->pemCert = $pemCert;
        $this->shopNumber = $shopNumber;
        $wsdl = $serviceLocation . "?wsdl";
        $this->client = new SoapClient(
            $wsdl,
            array(
                'local_cert' => $pemCert,
                'verify_peer' => false,
                'exceptions' => 0,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'passphrase' => $pass,
                'location' => $serviceLocation
            )
        );    
    }

    public function checkAppStatus($arrayOfOrderReference){        
        $this->lastException = null;
        $this->operationStatus = new OperationStatus();
        $this->isCorrect = false;
        $this->applications = new ArrayObject();
        $identyfikacja = new ClientIdentity($this->login, $this->pass,$this->shopNumber);
        try {   
                $resp = $this->client->GetApplicationState(
                    new AppStateDocument($identyfikacja, $arrayOfOrderReference)
                );
                if ($this->validateResponse($resp)) {
                    $ap = $resp->GetApplicationStateResult->Applications->ApplicationData;
                    if (is_array($ap)){
                        $this->applications = new ArrayObject($ap);
                    } elseif (is_object($ap)){                    
                        $this->applications->append($ap);
                    } else {
                        $this->isCorrect = false;
                        $this->lastException = "Bad response structure: !no Application object!";
                    }
                }
            }            
        catch(Exception $e) {
            $this->lastException = $e;         
        }
    }

    private function validateResponse($response) : bool{
        $isOk = true;
        // var_dump($response);
        $msg = "Bad response structure: ";
        if(!is_object($response)){
            $isOk = false;
            $msg= $msg . " !response is not object!";
        };
        if($isOk and !is_object($response->GetApplicationStateResult)){
            $isOk = false;
            $msg= $msg . " !GetApplicationStateResult is not object!";
        };
        if($isOk and !is_object($response->GetApplicationStateResult->OperationStatus)){
            $isOk = false;
            $msg= $msg . " !response->GetApplicationStateResult->OperationStatus is not object!";
        };
        if($isOk and !isset($response->GetApplicationStateResult->OperationStatus->IsCorrect)){
            $isOk = false;
            $msg= $msg . " !response->GetApplicationStateResult->OperationStatus->IsCorrect is not set!";
        };
        $this->isCorrect = $isOk;
        if($isOk) {
            $this->operationStatus = $response->GetApplicationStateResult->OperationStatus;
            $this->isCorrect = $response->GetApplicationStateResult->OperationStatus->IsCorrect;
            $this->lastException = $response->GetApplicationStateResult->OperationStatus->Message;            
            if($this->isCorrect and !is_object($response->GetApplicationStateResult->Applications)){
                $this->isCorrect = false;                
                $msg= $msg . " !response->GetApplicationStateResult->Applications is not object!";
                $this->lastException = $msg;
            };                    
        } else{
            $this->lastException = $msg;
        }        
        return $this->isCorrect;
    }

    /**
     * $dnp downpayment
     * $inr installment number
     * $prd product/credit line
     * $price
     */
    public function calculateCredit($dnp, $inr, $prd, $price){        
        $this->lastException = null;        
        $this->simulationResult = null;
        $this->isCorrect = false;
        $identyfikacja = new ClientIdentity($this->login, $this->pass,$this->shopNumber);
        try {   
            
            $resp = $this->client->GetFinancialData(
                new FinancialDataDocument(
                    new Identyfikacja(), new Simulation($dnp, $inr, $prd, $price)));
            $this->operationStatus = $resp->GetFinancialDataResult->OperationStatus;
            $this->simulationResult = $resp->GetFinancialDataResult->SimulationResult;
            $this->isCorrect = $this->operationStatus->IsCorrect;
        }            
        catch(Exception $e) {
            $this->lastException = $e;
        }
    }

    public function isActive(){ 
        $this->lastException = null;                
        try {   
            
            $resp = $this->client->IsActive();
            $this->isActive = $resp->IsActiveResult;
            $this->isCorrect = true;
        }            
        catch(Exception $e) {
            $this->lastException = $e;
            $this->isCorrect = false;
        }
        return $this->isCorrect;
    }

}