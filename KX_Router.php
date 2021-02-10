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
	 * @var int status code
	*/
	PUBLIC CONST INTERNAL_ERROR = 500;
	/**
	 * @var int status code
	*/
	PUBLIC CONST NOT_FOUND      = 404;
	/**
	 * @var int status code
	*/
	PUBLIC CONST OK							= 200;
	/**
	 * @var int status code
	*/
	PUBLIC CONST BAD_REQUEST		= 400;
	/**
	 * @var int status code
	*/
	PUBLIC CONST UNAUTHORIZED		= 401;
	/**
	 * @var int status code
	*/
	PUBLIC CONST FORBIDDEN			= 403;
	/**
	 * @var int status code
	*/
	PUBLIC CONST NOT_ALLOWED		= 405;
	/**
	 * @var int status code
	*/
	PUBLIC CONST NO_CONTENT		= 204;

	/**
	 * Run the Application with all acepted endpoints
	*/
	public function run(){

		$allow_methods = ["get", "post", "put", "delete"];
		$method = strtolower($_SERVER["REQUEST_METHOD"]);

		if( in_array( $method, $allow_methods ) ){
			
			if($this->methods[$method][0]){
				$this->implement($method);
			}else{
				self::sendJson([], self::NOT_ALLOWED);
			}

		}else{
			self::sendJson([], self::NOT_ALLOWED);
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
			}else{
				self::sendJson([], self::FORBIDDEN);
			}
		}
	}
	
	/**
	 * Get method
	 * @param Callback function that is called when method is executed
	 * @param Middleware function that is called before callback | optional | must return either true or false
	*/
	public function get($callback, $middleware = null)	{
		$this->methods["get"] = [true, $callback, $middleware ];
	}

	/**
	 * Post method
	 * @param Callback function that is called when method is executed
	 * @param Middleware function that is called before callback | optional | must return either true or false
	*/
	public function post($callback, $middleware = null)	{
		$this->methods["post"] = [true, $callback, $middleware ];
	}

	/**
	 * Delete method
	 * @param Callback function that is called when method is executed
	 * @param Middleware function that is called before callback | optional | must return either true or false
	*/
	public function delete($callback, $middleware = null)	{
		$this->methods["delete"] = [true, $callback, $middleware ];
	}

	/**
	 * Put method
	 * @param Callback function that is called when method is executed
	 * @param Middleware function that is called before callback | optional | must return either true or false
	*/
	public function put($callback, $middleware = null)	{
		$this->methods["put"] = [true, $callback, $middleware ];
	}

	/**
	 * Print Json
	*/
	public static function sendJson($data, $status){
		header('Content-type: application/json');
		header("HTTP/1.0 $status");
		echo json_encode($data);
	}

}