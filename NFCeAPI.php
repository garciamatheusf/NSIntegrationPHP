<?php

require_once("NSAPICommons.php");
class NFCeAPI extends NSAPICommons{
	private $urlEnvio;
	private $urlDownloadDoc;
	private $urlCancelamento;
	private $urlConsultaSituacao;
	private $urlInutiliza;
	private $urlSendMail;
	

	public function __construct(){
		$this->urlEnvio = "https://nfce.ns.eti.br/v1/nfce/issue";
		$this->urlDownloadDoc = "https://nfce.ns.eti.br/v1/nfce/get";
		$this->urlCancelamento = "https://nfce.ns.eti.br/v1/nfce/cancel";
		$this->urlConsultaSituacao = "https://nfce.ns.eti.br/v1/nfce/status";
		$this->urlInutiliza = "https://nfce.ns.eti.br/v1/nfce/inut";
		$this->urlSendMail = "https://nfe.ns.eti.br/util/resendemail";
	}

	public function emitirNFCe($conteudo){
		$result = $this->enviaJsonParaAPI($conteudo, $this->urlEnvio);
		return $result;
	}

	public function downloadNFCe($chNFe, $tpAmb = "2"){
		$conteudo['chNFe'] = $chNFe;
		$conteudo['tpAmb'] = $tpAmb;
		$conteudoImpressao['tipo'] = "PDF";
		$conteudo['impressao'] = $conteudoImpressao;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlDownloadDoc);
		return $result;
	}
	
	public function cancelarNFCe($chNFe, $tpAmb, $dhEvento, $nProt, $xJust){
		$conteudo['chNFe'] = $chNFe;
		$conteudo['tpAmb'] = $tpAmb;
		$conteudo['dhEvento'] = $dhEvento;
		$conteudo['nProt'] = $nProt;
		$conteudo['xJust'] = $xJust;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlCancelamento);
		return $result;
	}

	public function inutilizarNFCe($cUF, $tpAmb, $ano, $CNPJ, $serie, $nNFIni, $nNFFin, $xJust){
		$conteudo['cUF'] = $cUF;
		$conteudo['tpAmb'] = $tpAmb;
		$conteudo['ano'] = $ano;
		$conteudo['CNPJ'] = $CNPJ;
		$conteudo['serie'] = $serie;
		$conteudo['nNFIni'] = $nNFIni;
		$conteudo['nNFFin'] = $nNFFin;
		$conteudo['xJust'] = $xJust;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlInutiliza);
		return $result;
	}

	public function consultarSituacaoNFCe($chNFe, $tpAmb){
		$conteudo['chNFe'] = $chNFe;
		$conteudo['tpAmb'] = $tpAmb;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlConsultaSituacao);
		return $result;
	}

	public function envioNFCeEmail($chNFe, $enviaEmailDoc = NULL, $email){
		$conteudo['chNFe'] = $chNFe;
		if(is_null($email)){
			$conteudo['enviaEmailDoc'] = $enviaEmailDoc;
		}else{
			$conteudo['email'] = $email;
		}
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlSendMail);
		return $result;
	}
}
?>