<?php
namespace Catalyst\Exceptions;

class Http500 extends Http
{
    /**
     * General application failure
     */
    public static function applicationFailure($message = null)
    {
        return self::instance($message);
    }
}
