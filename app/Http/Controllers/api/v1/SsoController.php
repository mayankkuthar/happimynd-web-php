<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Services\Sso\SsoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Website → Quizzard / Games Hub single sign-on (config/sso.php).
 */
class SsoController extends Controller
{
    /** @var SsoService */
    private $sso;

    public function __construct(SsoService $sso)
    {
        $this->sso = $sso;
    }

    /**
     * POST /api/v1/sso/code   (Bearer JWT, auth:api)
     * Body: { app: "quizzard" | "hub", return_to: "https://<app>/..." }
     * 200 { status: "success", redirect_url } — send the browser there.
     */
    public function code(Request $request)
    {
        $app = (string) $request->input('app');
        if (!$this->sso->isApp($app)) {
            return response()->json(['status' => 'error', 'message' => 'Unknown app.'], 400);
        }

        $returnTo = $this->sso->allowedReturnTo($app, $request->input('return_to'));
        if ($returnTo === null) {
            return response()->json(['status' => 'error', 'message' => 'That return address is not allowed.'], 400);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Please log in.'], 401);
        }

        $code = $this->sso->createCode($user, $app);

        return response()->json([
            'status' => 'success',
            'redirect_url' => $this->sso->withParam($returnTo, 'sso_code', $code),
            'expires_in' => (int) config('sso.code_ttl_seconds'),
        ]);
    }

    /**
     * POST /api/v1/sso/exchange   (server to server, X-SSO-Secret header)
     * Body: { app: "quizzard", code }
     * 200 { status: "success", user: {...identity} }, 401 on a bad secret or code.
     */
    public function exchange(Request $request)
    {
        $app = (string) $request->input('app');
        $secret = $this->sso->isApp($app) ? (string) config("sso.apps.$app.secret") : '';

        if ($secret === '' || !hash_equals($secret, (string) $request->header('X-SSO-Secret'))) {
            return response()->json(['status' => 'error', 'message' => 'Not allowed.'], 401);
        }

        $user = $this->sso->consume($request->input('code'), $app);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'This sign-in link has expired. Please try again.'], 401);
        }

        return response()->json(['status' => 'success', 'user' => $this->sso->identity($user)]);
    }
}
