<?php

namespace QuantaForge\Broadcasting;

use QuantaForge\Http\Request;
use QuantaForge\Routing\Controller;
use QuantaForge\Support\Facades\Broadcast;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BroadcastController extends Controller
{
    /**
     * Authenticate the request for channel access.
     *
     * @param  \QuantaForge\Http\Request  $request
     * @return \QuantaForge\Http\Response
     */
    public function authenticate(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->reflash();
        }

        return Broadcast::auth($request);
    }

    /**
     * Authenticate the current user.
     *
     * See: https://pusher.com/docs/channels/server_api/authenticating-users/#user-authentication.
     *
     * @param  \QuantaForge\Http\Request  $request
     * @return \QuantaForge\Http\Response
     */
    public function authenticateUser(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->reflash();
        }

        return Broadcast::resolveAuthenticatedUser($request)
                    ?? throw new AccessDeniedHttpException;
    }
}
