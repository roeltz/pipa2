<?php

namespace Pipa\Error;

class StdoutErrorDisplay implements ErrorDisplay {

	function display(ErrorInfo $info) {
		echo "{$info->message}\n({$info->file}:{$info->line})\n";
		foreach($info->stack as $i=>$call) {
			echo str_pad($i, 5, " ", STR_PAD_LEFT) . ".";
			if (isset($call['class']))
				echo $call['class'].'::';
			echo @"{$call['function']} ({$call['file']}:{$call['line']})\n";
		}
		echo "\n";
	}
	
}
