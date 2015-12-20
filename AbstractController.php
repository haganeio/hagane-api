<?php
namespace Hagane\Controller;

//el abastracto del controller va a dar de alta todas las variables y servicios necesarios para
//esconder esta funcionalidad del uso cotidiano

abstract class AbstractController {
	protected $config;
	protected $view;
	protected $db;
	protected $auth;
	protected $user;

	protected $_init;
	protected $_action;

	public function __construct($config = null){
		$this->config = $config;

		$this->db = new \Hagane\Database($this->config);
		if ($this->db->isActive()) {
			$this->auth = new \Hagane\Authentication($this->config, $this->db);
			$this->user = new \Hagane\Model\User($this->auth, $this->db);
		}

		$this->view = '';
		$this->_init = '';
		$this->_action = '';
	}

	public function executeAction($action){
		if (method_exists($this, '_init')) {
			ob_start();
			$this->_init();
			$this->init = ob_get_clean();
		}

		//ejecucion de accion
		ob_start();
		$this->$action();
		$this->_action = ob_get_clean();

		if ($this->sendHtml) {
			header('Content-type: text/html; charset=utf-8');
		} else {
			header("Content-type: application/json; charset=utf-8");
		}

		return $this->linkInitAction($action);
	}

	public function linkInitAction($action){
		$class = explode("\\", get_class($this));

		$this->view .= $this->_init;
		$this->view .= $this->_action;
		return $this->view;
	}

	public function redirect($routeName) {
		if (substr($routeName, 0, 1) == '/') {
			$routeName = substr($routeName, 1);
		}
		header("Location: ".$this->config['document_root'].$routeName);
	}


	//vamos a ver si si tiene futuro estas funciones en la api
	private function secureImageParse($path){
		//Number to Content Type
		$file = $this->config['appPath'].'SecureImages/'.$path;
		$ntct = Array( "1" => "image/gif",
			"2" => "image/jpeg",
			"3" => "image/png",
			"6" => "image/bmp",
			"17" => "image/ico");

		return  Array(
			'image' => base64_encode(file_get_contents($file)),
			'mime' => $ntct[exif_imagetype($file)]);
	}

	public function getSecureImage($path){
		$img = $this->secureImageParse($path);
		return  'data:'.$img['mime'].';base64,'.$img['image'];
	}
}

?>