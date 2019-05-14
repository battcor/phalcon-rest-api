<?php
namespace Catalyst\Resources;

use Phalcon\Mvc\Controller;
use Catalyst\Exceptions;
use Catalyst\Helpers;
use \Firebase\JWT\JWT;

class Base extends Controller
{
    public $private;
    protected $validation;

    /**
     * Initializes objects needed by the controllers
     */
    public function initialize()
    {
        $this->validation = new Helpers\Validator();

        $this->data = [];

        switch ($this->request->getMethod()) {
            case 'POST':
                $this->data = $this->request->getPost();
                break;
            case 'PUT':
            case 'DELETE':
                $this->data = $this->request->getPut();
                break;
            default:
                $this->data = $this->request->get();
                break;
        }

        if ($this->request->getContentType() == 'application/json') {
            $this->data = $this->request->getJsonRawBody();
        }

        if ($this->private === true) {
            // $key = $this->config->jwt->key;
            // $token = array(
            //     "iss" => "http://test.pulseid.com",
            //     "aud" => "http://test.pulseid.com",
            //     "iat" => 1556696650,
            //     "nbf" => 1556698027,
            //     'data' => [
            //         "userId" => "aleks@pulseid.com",
            //         "clientId" => 50,
            //         "appUrl" => "https://test.pulseid.com/2.1"
            //     ]
            // );
            // $jwt = JWT::encode($token, $key);
            // echo $jwt . "\n";

            $jwt = str_replace('Bearer ', '', $this->request->getHeader("Authorization"));
            $key = $this->config->jwt->key;

            try {
                JWT::decode($jwt, $key, array('HS256'));
            } catch (\Exception $e) {
                Helpers\Log::add(
                    [
                        'message' => 'Unauthorized request. ' . $e->getMessage(),
                        'request' => $this->data,
                        'header' => $this->request->getHeaders()
                    ],
                    Helpers\Log::LEVEL_ERROR
                );

                throw Exceptions\Http403::invalidJwt('Invalid JWT Token');
            }
        }
    }

    /**
     * Generates random alphanumeric string
     * 
     * @param integer $min Minimum length of string
     * @param integer $max Maximum length of string
     * 
     * @return string
     */
    public function randomString($min = 6, $max = 12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_string_length = rand($min, $max);
        $string = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $random_string_length; $i++) {
            $string .= $characters[mt_rand(0, $max)];
        }
        return $string;
    }

    /**
     * Default success API response format
     * 
     * @param  array   $data Data to return
     * @param  integer $code Status code to return
     * 
     * @return json
     */
    public function respondSuccess($data = [], $code = 200)
    {
        $data = !empty($data) ? $data : ['success' => true];

        $this->response->setStatusCode($code)
            ->setJsonContent($data);

        return $this->response;
    }
}
