<?php

namespace Pipa\Cache;

class FileCache extends MemoryCache {

	protected $path;

	function __construct($path) {
		$this->path = $path;
	}

	function destroy() {
		foreach(glob($this->getFilename("*")) as $filename)
			unlink($filename);
		return parent::destroy();
	}

	function get($key) {
		if (parent::has($key)){
			return parent::get($key);
		} elseif ($this->hasInFilesystem($key)) {
			parent::set($key, $this->retrieve($this->getFilename($key)));
			return parent::get($key);
		}
	}

	function has($key) {
		return parent::has($key) || $this->hasInFilesystem($key);
	}

	function remove($key) {
		unlink($this->getFilename($key));
		return parent::remove($key);
	}

	function set($key, $value) {
		$this->save($this->getFilename($key), $value);
		return parent::set($key, $value);
	}

	protected function getFilename($key) {
		return "{$this->path}/{$key}";
	}

	protected function hasInFilesystem($key) {
		return file_exists($this->getFilename($key));
	}

	protected function retrieve($filename) {
		return unserialize(file_get_contents($filename));
	}

	protected function save($filename, $value) {
		file_put_contents($filename, serialize($value));
	}

}
