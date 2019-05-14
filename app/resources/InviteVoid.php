<?php
namespace Catalyst\Resources;

use Catalyst\Exceptions;
use Catalyst\Helpers;
use Catalyst\Models;

class InviteVoid extends Base
{
    public $private = true;

    public function actionPost()
    {
        $this->initialize();

        $isValid = $this->validation->validate([
            "inviteToken" => "PresenceOf"
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

        $inviteToken = $this->data->inviteToken;

        try {
            $result = Models\Invites::findFirst("token = '$inviteToken' AND void = 0");
        } catch (\Exception $e) {
            Helpers\Log::add(['message' => 'Unable to fetch invite token ' . $e->getMessage(), 'request' => $this->data], Helpers\Log::LEVEL_CRITICAL);
            $response = [
                "error_code" => "-101",
                "error_message" => "Failed to load data",
                "trace_id" => "b30d919c-6491-491e-a5e4-6222ef0b32e4"
            ];
            $this->response->setJsonContent($response);
            throw Exceptions\Http500::applicationFailure('Internal Server Error');
        }

        if (empty($result->token)) {
            Helpers\Log::add(['message' => 'Invite is either invalid, used, void, or expired', 'request' => $this->data], Helpers\Log::LEVEL_ERROR);
            $response = [
                "error_code" => "-101",
                "error_message" => "Failed to load data",
                "trace_id" => "b30d919c-6491-491e-a5e4-6222ef0b32e4"
            ];
            $this->response->setJsonContent($response);
            throw Exceptions\Http400::invalidRequest('Invite Not Found');
        }

        $result->void = 1;
        $result->save();

        $response = [
            "appKey" => "4d4f434841-373836313836303830-3430-616e64726f6964",
            "appUrl" => "https://test.pulseid.com/2.1"
        ];

        return $this->respondSuccess($response);
    }
}
