<?php
namespace Catalyst\Models;

class Invites extends Base
{
    public $id;
    public $token;
    public $used;
    public $void;
    public $expiry;
    public $created;
}
