<?php
namespace Catalyst\Exceptions;

class Http403 extends Http
{
    /**
     * Response for invalid Jwt
     */
    public static function invalidJwt($message = null)
    {
        return self::instance($message);
    }
}
