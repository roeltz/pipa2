<?php

namespace Pipa\HTTP\Error;
use Pipa\Error\ErrorDisplay;
use Pipa\Error\ErrorInfo;

class HTMLErrorDisplay implements ErrorDisplay {

	function display(ErrorInfo $info) {
		echo "<div><strong>{$info->message}</strong><br>{$info->file}:{$info->line}</div>\n";
		foreach ($info->stack as $frame) {
			if (isset($frame['file'])) {
				echo "<div>{$frame['file']}:{$frame['line']}</div>\n";
			} else {
				echo "<div>{$frame['class']}::{$frame['function']}</div>\n";
			}
		}
	}

}
