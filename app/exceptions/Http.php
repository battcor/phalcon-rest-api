<?php
namespace Catalyst\Exceptions;

use Phalcon\Exception;

class Http extends Exception
{
    /**
     * Assigns message and code into Exception object 
     * 
     * @return self
     */
    public static function instance($message = null)
    {
        $last = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $class = get_called_class();
        $pos = strrpos($class, '\\');

        if (!empty($pos)) {
            $class = substr($class, $pos + 1);
        }

        $message = $message ? $message : $last[1]['function'];
        $code = (int)str_replace('Http', '', $class);

        return new self($message, $code);
    }
}
