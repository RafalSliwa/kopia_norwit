<?php
use SoapClient;
/*
Prosty przykład wykorzystania usługi ProposalServiceHybrid.
Dokładną strukturę obiektów wysyłanych i odbieranych najlepiej obejrzeć w narzędziu SOAPUI (lub analogicznym),
Można to zrobić generując projekt na bazie definicji WSDL dostępnej pod adresem:
https://api.santanderconsumer.pl/ProposalServiceHybrid?wsdl

Uwaga, aby obejrzeć powyższą definicję w przeglądarce konieczna jest instalacja certyfikatu "Finanse dla domu"
(dostarczany przez Opiekuna Sklepu).
W SOAPUI instaluje się certyfikat niezależnie (np. File->Preferences->SSL Settings)

W kodzie wykorzystany jest certyfikat w formacie *.pem
*/
$wsdl = "https://api.santanderconsumer.pl/ProposalServiceHybrid?wsdl";
//$pem = dirname(__FILE__) . '/cert/cert/public_key.pem';
$pem = dirname(__FILE__) . '/newfile.crt.pem';
$pass = 'ycUb!#6.!';
$client = new SoapClient(
		$wsdl,
		array(
			'local_cert' => $pem,
			'verify_peer' => false,
			'exceptions' => 0,
			'cache_wsdl' => WSDL_CACHE_NONE,
			'passphrase' => $pass,
			'location' => 'https://api.santanderconsumer.pl/ProposalServiceHybrid'
		)
	);


class Identyfikacja {
    //public $Login = 'WS_ONLINE_64803';
    //public $Password = 'Q4nQD6Sh';
    public $Login = 'WS_online_test1';
    public $Password = 'npRWb6C8';	
	/*sklep testowy 99995 mozna wykorzystać jeśli jest przypisany do loginu sklepu.
		Jeśli nie - testy można wykonywać na własnym sklepie.*/
    public $ShopNumber = 99995;
};

class AppStateDocument {
	public $Identity;
	public $ShopApplicationNumbers;
	public function __construct($i, $a){
		$this->Identity = $i;
		$this->ShopApplicationNumbers = $a;
	}
};

class Simulation {
	public $Downpayment;
    public $InstalmentsNumber;
    public $ProductNumber;
    public $TotalPrice;
			public function __construct($dnp, $inr, $prd, $price){
				$this->Downpayment = $dnp;
				$this->InstalmentsNumber = $inr;
				$this->ProductNumber = $prd;
				$this->TotalPrice = $price;
			}
};

class FinancialDataDocument {
	public $Identity;
	public $Simulation;
	public function __construct($i, $s){
		$this->Identity = $i;
		$this->Simulation = $s;
	}

};

try {
	/*Pobieranie statusu wniosku*/
	$method = 'GetApplicationState';
//	$resp = $client->$method(new AppStateDocument(new Identyfikacja(), ['11537599','1019945']));
	$resp = $client->GetApplicationState(new AppStateDocument(new Identyfikacja(), ['YUYVJOGNY', '1019945']));
	echo "----------------------------------GetApplicationState---------------------------------";
    echo "var_dump: ";
	echo var_dump($resp);
	echo var_dump($resp->GetApplicationStateResult->Applications->ApplicationData[0]);
       	echo $resp->GetApplicationStateResult->Applications->ApplicationData[0]->ApplicationNumber;
        echo "\n";
        echo $resp->GetApplicationStateResult->Applications->ApplicationData[0]->ShopApplicationNumber;
//	echo "Dynamic method\n";

	/*symulacja kredytu*/
	echo "----------------------------------GetFinancialData-------------------------------------\n";
	$resp = $client->GetFinancialData(new FinancialDataDocument(new Identyfikacja(), new Simulation(3, 10, '101', 1280)));
	echo "var_dump: ";
	echo var_dump($resp);
	echo "wartość APR: ".$resp->GetFinancialDataResult->SimulationResult->APR;

} catch (Exception $ex) {
    echo $ex->getMessage();
};
