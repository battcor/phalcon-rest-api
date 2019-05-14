<?php
namespace Catalyst\Models;

class Base extends \Phalcon\Mvc\Model
{
    /**
     * Function called before record is inserted
     */
    public function beforeCreate()
    {
        if (property_exists($this, 'created')) {
            $this->created = date('Y-m-d H:i:s');
        }

        if (property_exists($this, 'modified')) {
            $this->modified = date('Y-m-d H:i:s');
        }
    }
}
