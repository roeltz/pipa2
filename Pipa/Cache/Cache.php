<?php

namespace Pipa\Cache;

interface Cache {

	function destroy();

	function get($key);

	function has($key);

	function remove($key);

	function set($key, $value);
	
}
