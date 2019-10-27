<?php
class NSAPICommons{
    private $token = "";

    private function enviaJsonParaAPI($conteudoAEnviar, $url){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $conteudoAEnviar);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-AUTH-TOKEN: ' . $this->token));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($ch);
		curl_close($ch);

		return $this->wsResultToArray($result);
	}
    
    public function salvarDocumento($conteudo, $caminhoEnomeArquivo, $isBase64 = false){
		if(isset($conteudo) and isset($caminhoEnomeArquivo)){
			if($isBase64 == true){
				$this->salvarDocumentoBase64($conteudo, $caminhoEnomeArquivo);
			}
			else{
				$this->salvarDocumentoTodos($conteudo, $caminhoEnomeArquivo);
			}
		}
	}

	public function salvarDocumentoTodos($conteudo, $caminhoEnomeArquivo){
		$fp = fopen($caminhoEnomeArquivo, 'w+');
		fwrite($fp, $conteudo);
		fclose($fp);
	}

	public function salvarDocumentoBase64($conteudo, $caminhoEnomeArquivo){
		$fp = fopen($caminhoEnomeArquivo, 'w+');
		fwrite($fp, base64_decode($conteudo));
		fclose($fp);
	}

	private function wsResultToArray($result){
		return (array)json_decode(($result));
	}

	private function isStatusOK($status){
		return $status == 200;
	}

	private function isCStatNFeAutorizada($cStat){
		return $cStat == 100;
	}

	private function isCStatLoteEmProcessamento($cStat){
		return $cStat == 105;
	}

}