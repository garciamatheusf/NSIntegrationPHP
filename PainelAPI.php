<?php

require_once("NSAPICommons.php");
class PainelAPI extends NSAPICommons{
	private $urlSalvarDados;

	public function __construct(){
		$this->urlSalvarDados = "http://painelapi.ns.eti.br/licenca/salvarDados";
	}

	public function criarLicenca($licencas, $pessoa){
		$conteudo['licencas'] = $chNFe;
		$conteudo['pessoa'] = $pessoa;
		$result = $this->enviaJsonParaAPI(json_encode($conteudo), $this->urlSalvarDados);
		return $result;
	}
}
?>