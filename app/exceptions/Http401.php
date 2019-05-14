<?php
namespace Catalyst\Exceptions;

class Http401 extends Http
{
    /**
     * Response for general invalid record.
     */
    public static function invalidRecord($message = null)
    {
        return self::instance($message);
    }
}
