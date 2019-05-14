<?php
namespace Catalyst\Exceptions;

class Http400 extends Http
{
    /**
     * Response for general invalid request.
     */
    public static function invalidRequest($message = null)
    {
        return self::instance($message);
    }
}
