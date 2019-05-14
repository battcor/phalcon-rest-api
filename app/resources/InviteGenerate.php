<?php
namespace Catalyst\Resources;

use Catalyst\Exceptions;
use Catalyst\Helpers;
use Catalyst\Models;

class InviteGenerate extends Base
{
    public $private = true;

    public function actionPost()
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

        $appConfig = $this->config->application;

        $inviteToken = $this->randomString($appConfig->inviteMinLength, $appConfig->inviteMaxLength);
        $inviteExpiry = date('Y-m-d H:i:s', strtotime('+' . $appConfig->inviteExpiry));

        $invite = new Models\Invites;
        $data = [
            'token' => $inviteToken,
            'used' => 0,
            'void' => 0,
            'expiry' => $inviteExpiry
        ];

        try {
            $invite->assign($data);
            $invite->save();
        } catch (\Exception $e) {
            Helpers\Log::add(
                ['message' => 'Unable to create invite token ' . $e->getMessage(), 'request' => $this->data],
                Helpers\Log::LEVEL_CRITICAL
            );
            throw Exceptions\Http500::applicationFailure();
        }

        $response = [
            "inviteToken" => $invite->token,
            "validTo" => $invite->expiry
        ];

        return $this->respondSuccess($response);
    }
}
