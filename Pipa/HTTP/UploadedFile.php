<?php

namespace Pipa\HTTP;
use Pipa\FS\File;

class UploadedFile extends File {
	
	public $originalName;
	
	function __construct($originalName, $tmpName, $type) {
		parent::__construct($tmpName, $type);
		$this->originalName = $originalName;
	}

	function getExtensionlessOriginalName() {
		$parts = explode(".", $this->originalName);
		array_pop($parts);
		return join(".", $parts);
	}
	
	function getOriginalExtension() {
		return end(explode(".", $this->originalName));
	}
	
	function move($path) {
		if (@move_uploaded_file($this->path, $path) === false)
			throw new \Exception("Could not move uploaded file");
		return new File($path);
	}
}
