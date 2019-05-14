<?php
namespace Catalyst\Resources;

use Catalyst\Exceptions;
use Catalyst\Helpers;
use Catalyst\Models;

class InviteList extends Base
{
    public $private = true;

    public function actionGet()
    {
        $this->initialize();

        $isValid = $this->validation->validate([
            "userId" => "PresenceOf",
            "clientId" => "PresenceOf",
            "appKey" => "PresenceOf",
            "appUrl" => "PresenceOf",
        ], $this->data);

        if (!$isValid) {
            Helpers\Log::add(['message' => $this->validation->firstErrorMessage, 'request' => $this->data], Helpers\Log::LEVEL_ERROR);
            $this->response->setJsonContent([
                "error_code" => "-101",
                "error_message" => $this->validation->firstErrorMessage,
                "trace_id" => "b30d919c-6491-491e-a5e4-6222ef0b32e4"
            ]);
            throw Exceptions\Http400::invalidRequest();
        }

        try {
            $result = Models\Invites::find([
                'columns' => 'token, used, void, expiry, created'
            ]);
        } catch (\Exception $e) {
            Helpers\Log::add(['message' => 'Unable to fetch invite list ' . $e->getMessage(), 'request' => $this->data], Helpers\Log::LEVEL_CRITICAL);
            $response = [
                "error_code" => "-101",
                "error_message" => "Failed to load data",
                "trace_id" => "b30d919c-6491-491e-a5e4-6222ef0b32e4"
            ];
            $this->response->setJsonContent($response);
            throw Exceptions\Http500::applicationFailure('Internal Server Error');
        }

        return $this->respondSuccess($result);
    }
}
