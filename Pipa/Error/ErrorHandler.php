<?php

namespace Pipa\Error;
use Exception;

abstract class ErrorHandler {

    protected static $context;

    protected static $displays;

    static function addDisplay(ErrorDisplay $display, $context = "all") {
        self::$displays[$context][] = $display;
    }

    static function display(ErrorInfo $info) {
        self::displayForContext("all", $info);
        self::displayForContext(self::$context, $info);
    }

    static function displayForContext($context, ErrorInfo $info) {
        if (!isset(self::$displays[$context])) return;

        foreach (self::$displays[$context] as $display)
            $display->display($info);
    }

    static function handleError($code, $message, $file, $line) {
        if (error_reporting() === 0) return;
        
        self::display(new ErrorInfo($message, $code, $file, $line, array_slice(debug_backtrace(), 1)));
    }

    static function handleException(Exception $e) {
        self::display(new ExceptionInfo($e));
        return false;
    }

    static function register($errorTypes = E_ALL | E_STRICT) {
        set_error_handler(__CLASS__."::handleError", $errorTypes);
        set_exception_handler(__CLASS__."::handleException");
    }

    static function setContext($context) {
        self::$context = $context;
    }
}
