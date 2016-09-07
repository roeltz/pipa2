<?php

namespace Pipa\HTTP;
use Pipa\Config\Config;
use Pipa\MVC\Action;
use Pipa\MVC\Context as BaseContext;
use Pipa\MVC\View as MVCView;
use Pipa\Pipeline\Pipeline;

class Context extends BaseContext {

	const NAME = "HTTP";

	public $action;

	public $protocol;

	public $request;

	public $response;

	public $result;

	public $router;

	public $view;

	function __construct(Config $config, Router $router, MVCView $view) {
		parent::__construct(self::NAME, $config);
		$this->protocol = $_SERVER['SERVER_PROTOCOL'];
		$this->config = $config;
		$this->router = $router;
		$this->view = $view;
		$this->request = $this->createRequest();
		$this->response = $this->createResponse();
	}

	function initPipeline(Pipeline $pipeline) {
		$pipeline
			->add("routing", function($next){
				$this->action = $this->router->resolve($this->request);
				$next();
			})
			->add("processing", function($next){
				$this->result = $this->action->execute($this->request, $this->response);
				$next();
			})
			->add("rendering", function($next){
				$this->view->render($this->request, $this->response, $this->result);
				$next();
			})
		;
	}

	function createRequest() {
		return new Request(
			$this,
			$this->createSession(),
			$this->compileRequestData(),
			$_SERVER["REQUEST_METHOD"],
			$_SERVER["REQUEST_URI"],
			$this->getRequestHeaders(),
			$_SERVER["HTTP_HOST"],
			@$_SERVER["HTTPS"] == "on"
		);
	}

	function createResponse() {
		return new Response($this);
	}

	function createSession() {
		return new Session();
	}

	function getRequestHeaders() {
		$headers = [];
		foreach(getallheaders() as $k=>$v)
			$headers[strtolower($k)] = $v;
		return $headers;
	}

	function compileRequestData() {
		$data = $_REQUEST;

		foreach($data as $paramName=>&$value) {
			if (empty($value) && $value !== "0")
				$value = null;
		}

		foreach($_FILES as $paramName=>$file) {
			$fileValue = null;

			if (is_array($file["name"])) {
				$items = [];
				foreach($file["name"] as $i=>$fileName) {
					if (!$file["error"][$i])
						$items[] = new UploadedFile($fileName, $file["tmp_name"][$i], $file["type"][$i]);
				}
				$fileValue = $items;
			} elseif (!$file["error"]) {
				$fileValue = new UploadedFile($file["name"], $file["tmp_name"], $file["type"]);
			}

			if ($fileValue !== null) {
				$data[$paramName] = (@$data->$paramName) ? array_merge((array) $data->$paramName, $fileValue) : $fileValue;
			}
		}

		return $data;
	}

	function reprocess($callable, $data = []) {
		$this->action = new Action($this, $callable);
		$this->request->data = $data;
		$this->run("processing");
	}
}
