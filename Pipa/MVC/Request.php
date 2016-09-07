<?php

namespace Pipa\MVC;
use Pipa\Event\EventEmitter;
use Pipa\Match\Comparable;

class Request extends EventEmitter implements Comparable {
		
	public $context;
	
	public $session;
	
	public $data;
	
	function __construct(Context $context, Session $session, array $data) {
		$this->context = $context;
		$this->session = $session;
		$this->data = $data;
	}
	
	function getComparableState() {
		$state = [];
		$state["context"] = $this->context->name;
		foreach($this->data as $k=>$v)
			$state["data:$k"] = $v;
		return $state;
	}
	
}
