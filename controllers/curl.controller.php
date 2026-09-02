<?php 

class CurlController{

	/*=============================================
	Peticiones a la API
	=============================================*/	

	static public function request($url,$method,$fields){

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://api.restaurante.com:8084/'.$url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $fields,
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Restaurante CMS)',
			CURLOPT_HTTPHEADER => array(
				'Authorization: asdfghjuytrewq123xcv987564qwedfghjfduy'
			),
		));

		$response = curl_exec($curl);

		if($response === false || $response === ''){

			return (object) array(
				'status' => 500,
				'results' => 'Error de conexión con la API'
			);
		}

		$response = json_decode($response);

		if($response === null){

			return (object) array(
				'status' => 500,
				'results' => 'Respuesta inválida de la API'
			);
		}

		return $response;

	}

	/*=============================================
	Peticiones a la API de ChatGPT
	=============================================*/	

	static public function chatGPT($content,$token,$org){

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{
		    "model": "gpt-4-0613",
		    "messages":[{"role": "user", "content": "'.$content.'"}]
		}',
		  CURLOPT_HTTPHEADER => array(
		    'Authorization: Bearer '.$token,
		    'OpenAI-Organization: '.$org,
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		$response = json_decode($response);
		return $response->choices[0]->message->content;

	}

}
