<?php

require_once("NSAPICommons.php");
class MDFeAPI extends NSAPICommons{
	private $urlEmissao;
	private $urlStatusProcessamento;
	private $urlDownloadDoc;
	private $urlCancelamento;
	private $urlEncerramento;
	private $urlIncCondutor;
	private $urlIncDFe;
	private $urlDownloadEvento;
	private $urlConsultaSituacao;
	private $urlConsultaNaoEncerrados;
	private $urlListarNSNRecs;
	private $urlPrevia;
	

	public function __construct(){
		$this->urlEmissao = "https://mdfe.ns.eti.br/mdfe/issue";
		$this->urlStatusProcessamento = "https://mdfe.ns.eti.br/mdfe/issue/status";
		$this->urlDownloadDoc = "https://mdfe.ns.eti.br/mdfe/get";
		$this->urlCancelamento = "https://mdfe.ns.eti.br/mdfe/cancel";
		$this->urlEncerramento = "https://mdfe.ns.eti.br/mdfe/closure";
		$this->urlIncCondutor = "https://mdfe.ns.eti.br/mdfe/adddriver";
		$this->urlIncDFe = "https://mdfe.ns.eti.br/mdfe/get/event";
		$this->urlDownloadEvento = "https://mdfe.ns.eti.br/mdfe/inut";
		$this->urlConsultaSituacao = "https://mdfe.ns.eti.br/util/conscad";
		$this->urlConsultaNaoEncerrados = "https://mdfe.ns.eti.br/util/wssefazstatus";
		$this->urlListarNSNRecs = "https://mdfe.ns.eti.br/util/list/nsnrecs";
		$this->urlPrevia = "https://mdfe.ns.eti.br/util/preview/MDFe";
	}

	public function emitirMDFeSincrono($CNPJEmit, $mdfe, $tpDown = 'XP'){

		$result = $this->emitirMDFe($mdfe);

		$retornoEmissao = $result;
		print_r($retornoEmissao);

		if(!($this->isStatusOK($retornoEmissao['status']))){
			return $retornoEmissao;
		}

		$nsNRec = $retornoEmissao['nsNRec'];
		$counter = 0;
		do {
			if ($counter == 0){
				sleep(.25);
			} else {
				sleep(3);
			}

			$counter++;

			$retornoConsulta = $this->consultarStatusProcessamento($CNPJEmit, $nsNRec);
			
			if($this->isStatusOK($retornoConsulta['status'])){ 
				break;
			}

			if(isset($retornoConsulta['cStat'])) {
				if (!$this->isCStatLoteEmProcessamento($retornoConsulta['cStat'])){
					return $retornoConsulta;
				}

			} else {
				return $retornoConsulta;
			}

		} while ($counter < 3);

		if($this->isCStatMDFeAutorizada($retornoConsulta['cStat'])){
			$json = json_decode($mdfe);
			$tpAmb = $json->MDFe->infMDFe->ide->tpAmb;
			$retornoXml = $this->downloadMDFe($retornoConsulta['chMDFe'], $tpAmb, $tpDown);
			
			if (stripos($tpDown, 'x') !== false){
				$retornoConsulta['xml'] = $retornoXml['xml'];
			}
			if (stripos($tpDown, 'p') !== false){
				$retornoConsulta['pdf'] = $retornoXml['pdf'];
			}
		}

		return $retornoConsulta;
	}

	public function emitirMDFe($conteudo){
		$result = $this->enviaJsonParaAPI($conteudo, $this->urlEmissao);
		return $result;
	}

	public function consultarStatusProcessamento($CNPJ, $nsNRec){
		$conteudo['CNPJ'] = $CNPJ;
		$conteudo['nsNRec'] = $nsNRec;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlStatusProcessamento);
		return $result;
	}

	public function downloadMDFe($chMDFe, $tpAmb = "2", $tpDown){
		$conteudo['chMDFe'] = $chMDFe;
		$conteudo['tpAmb'] = $tpAmb;
		$conteudo['tpDown'] = $tpDown;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlDownloadDoc);
		return $result;
	}
	
	public function cancelarMDFe($chMDFe, $tpAmb, $dhEvento, $nProt, $xJust){
		$conteudo['chMDFe'] = $chMDFe;
		$conteudo['tpAmb'] = $tpAmb;
		$conteudo['dhEvento'] = $dhEvento;
		$conteudo['nProt'] = $nProt;
		$conteudo['xJust'] = $xJust;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlCancelamento);
		return $result;
	}

	public function encerrarMDFe($chMDFe, $tpAmb, $nProt, $dhEvento, $dtEnc, $cUF, $cMun){
		$conteudo['chMDFe'] = $chMDFe;
		$conteudo['tpAmb'] = $tpAmb;
		$conteudo['nProt'] = $nProt;
		$conteudo['dhEvento'] = $dhEvento;
		$conteudo['dtEnc'] = $dtEnc;
		$conteudo['cUF'] = $cUF;
		$conteudo['cMun'] = $cMun;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlEncerramento);
		return $result;
	}

	public function incluirCondutor($cUF, $tpAmb, $dhEvento, $xNome, $CPF, $nSeqEvento){
		$conteudo['cUF'] = $cUF;
		$conteudo['tpAmb'] = $tpAmb;
		$conteudo['dhEvento'] = $ano;
		$conteudo['xNome'] = $CNPJ;
		$conteudo['CPF'] = $serie;
		$conteudo['nSeqEvento'] = $nMDFIni;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlIncCondutor);
		return $result;
	}

	public function incluirDFe($chMDFe, $tpAmb, $nProt, $dhEvento, $xMunCar, $cMunCar, $infDocsArray){
		$conteudo['chMDFe'] = $chMDFe;
		$conteudo['tpAmb'] = $tpAmb;
		$conteudo['dhEvento'] = $ano;
		$conteudo['nProt'] = $nProt;
		$conteudo['xMun'] = $xMunCar;
		$conteudo['cMun'] = $cMunCar;
		$conteudo['infDocs'] = $infDocsArray;
		//infDocs é um array, onde cada elemento precisa conter os seguintes atributos: cMun, xMun e chNFe
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlIncDFe);
		return $result;
	}

	public function downloadEvento($chMDFe, $tpDown, $tpEvento, $nSeqEvento){
		$conteudo['chMDFe'] = $chMDFe;
		$conteudo['tpDown'] = $tpDown;
		$conteudo['tpEvento'] = $tpEvento;
		$conteudo['nSeqEvento'] = $nSeqEvento;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlDownloadEvento);
		return $result;
	}

	public function consultarSituacaoMDFe($chMDFe, $tpAmb, $licencaCnpj){
		$conteudo['chMDFe'] = $chMDFe;
		$conteudo['licencaCnpj'] = $licencaCnpj;
		$conteudo['tpAmb'] = $tpAmb;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlConsultaSituacao);
		return $result;
	}

	public function consultarNaoEncerrados($CNPJ, $tpAmb, $cUF){
		$conteudo['CNPJ'] = $CNPJ;
		$conteudo['tpAmb'] = $tpAmb;
		$conteudo['cUF'] = $cUF;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlConsultaNaoEncerrados);
		return $result;
	}

	public function listarNSNRecs($chMDFe, $tpAmb){
		$conteudo['chMDFe'] = $chMDFe;
		$conteudo['tpAmb'] = $tpAmb;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlListarNSNRecs);
		return $result;
	}

	public function previa($conteudo){
		$result = $this->enviaJsonParaAPI($conteudo, $this->urlPrevia);
		return $result;
	}
	
}
?>