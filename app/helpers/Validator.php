<?php
namespace Catalyst\Helpers;

use Phalcon\Validation;

class Validator
{
    protected $validation;
    protected $phalconDefaults, $phalconDefaultMessages;
    public $firstErrorMessage = null;

    function __construct()
    {
        $this->validation = new Validation();

        $this->phalconDefaults = [
            "PresenceOf" => "Phalcon\Validation\Validator\PresenceOf",
        ];
    }

    /**
     * Performs validation of parameters
     * 
     * @param array $rules array of fields and rules
     * @param array $data array of parameters to be validated
     * 
     * @return boolean
     */
    function validate($rules, $data)
    {
        foreach ($rules as $field => $rule) {
            $this->validation->add($field, new $this->phalconDefaults[$rule](array(
                'message' => "The $field is required"
            )));
        }

        $messages = $this->validation->validate($data);

        if (count($messages)) {
            list($message) = $messages;
            $this->firstErrorMessage = $message->getMessage();
            return false;
        }

        return true;
    }
}
