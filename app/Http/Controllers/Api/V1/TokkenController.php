<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TokkenController extends Controller
{
    public static function generate()
    {
        $tokenData = Token::query()->first();

        if ($tokenData && Carbon::now()->lessThan($tokenData->expires_at)) {
            return response()->json([
                'success' => true,
                'token' => $tokenData->token
            ]);
        }

        $token = Str::random(60);

        Token::query()->create([
            'token' => $token,
            'expires_at' => Carbon::now()->addMinutes(40),
        ]);

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);

    }
}
