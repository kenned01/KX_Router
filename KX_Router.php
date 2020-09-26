<?php

require_once "KXI_Request_Methods.php";

class KX_Router implements Request_Methods {

	// 1- is method used
	// 2- callback
	// 3- middleware
	private $methods = [
		"get" => [false, null, null],
		"post" => [false, null, null],
		"put" => [false, null, null],
		"delete" => [false, null, null],
	];

	/**
	* Solo el metodo POST acepta form-data como body
	* los otros metodos solo aceptan json en el body
	*/
	public function run(){
		switch ($_SERVER["REQUEST_METHOD"]) {
			case 'GET':
				if($this->methods["get"][0]){
					$this->implement("get");
				}else{
					echo $this->notAllowed();
				}
				break;
			
			case 'POST':
				if($this->methods["post"][0]){
					$this->implement("post");
				}else{
					echo $this->notAllowed();
				}
				break;
			
			case 'PUT':
				if($this->methods["put"][0]){
					$this->implement("put");
				}else{
					echo $this->notAllowed();
				}
				break;
			
			case 'DELETE':
				if($this->methods["delete"][0]){
					$this->implement("delete");
				}else{
					echo $this->notAllowed();
				}
				break;
			
			default:
				echo $this->notAllowed();
				break;
		}
	}

	private function Response(){
		if( empty($_POST) ){

			$data = json_decode(
				file_get_contents('php://input'),
				true
			);

			if( is_null($data)){
				return $this->getFormdata();
			}else{
				return $data;
			}
		}

		return $_POST;
	}

	private function getFormdata(){

		$data = file_get_contents('php://input');
		$parametros = [];

		if(stripos($data,"Content-Disposition: form-data;")){

			
			$patron = "/----------------------------[0-9]*/";
	   		$sustitucion = "";

		   	$valor = preg_replace($patron, $sustitucion, $data);
		   	$valor = explode("Content-Disposition: form-data;", $valor);

		   	for ($i=0; $i < count($valor); $i++) {

		   		$valor[$i] = trim($valor[$i]);

		   		if(!empty( $valor[$i]) && isset( $valor[$i] ) )	{
		   			
		   			$patron = ["/name=\"/", "/\n*/", "/\r--/", "/\r/"];
		   			$sustitucion = "";
				    $valorI = preg_replace($patron, $sustitucion, $valor[$i]);
				    
				    $hash = '"';
				    $valorK = substr($valorI, 0, strpos($valorI,$hash));
				    $valorV = substr($valorI, strpos($valorI,$hash)+1);

				    $parametros[$valorK] = $valorV;
		   		}
		   	}

		}

		return $parametros;

	}

	private function implement($method)	{
		$call = $this->methods[$method][1];
		$midd = $this->methods[$method][2];
		$res = $this->Response();
		
		if( is_null($midd) ){
			$call($res);
		}else{
			if ( $midd() ) {
				$call($res);
			}
		}
	}

	private function notAllowed()	{
		return "method not allowed";
	}
	
	public function get($callback, $middleware = null)	{
		$this->methods["get"] = [true, $callback, $middleware ];
	}

	public function post($callback, $middleware = null)	{
		$this->methods["post"] = [true, $callback, $middleware ];
	}

	public function delete($callback, $middleware = null)	{
		$this->methods["delete"] = [true, $callback, $middleware ];
	}

	public function put($callback, $middleware = null)	{
		$this->methods["put"] = [true, $callback, $middleware ];
	}

}