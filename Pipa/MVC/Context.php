<?php

namespace Pipa\MVC;
use Pipa\Config\Config;
use Pipa\Event\EventEmitter;
use Pipa\Pipeline\Pipeline;

abstract class Context extends EventEmitter {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var Config
	 */
	public $config;

	private $pipeline;

	abstract protected function initPipeline(Pipeline $pipeline);

	function __construct($name, Config $config) {
		$this->name = $name;
		$this->config = $config;
	}

	function getPipeline() {
		if (!$this->pipeline) {
			$this->pipeline = new Pipeline();
			$this->initPipeline($this->pipeline);
		}

		return $this->pipeline;
	}

	function hook($file) {
		$context = $this;
		$pipeline = $this->getPipeline();
		require "$file.php";
		return $this;
	}

	function run($offset = null) {
		$this->getPipeline()->run($offset);
	}

}
