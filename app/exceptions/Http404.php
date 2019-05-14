<?php
namespace Catalyst\Exceptions;

class Http404 extends Http
{
    /**
     * Response for invalid API resource.
     */
    public static function resourceNotFound($message = null)
    {
        return self::instance($message);
    }
}
