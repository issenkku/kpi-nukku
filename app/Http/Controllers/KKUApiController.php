<?php

namespace App\Http\Controllers;

use App\Services\KKUApiService;
use Illuminate\Http\Request;

class KKUApiController extends Controller
{
    public function __construct(private KKUApiService $kku)
    {
    }

    // GET /kku/token (auth only)
    public function getKKUToken(Request $request)
    {
        $force = (bool) $request->boolean('refresh', false);
        try {
            $token = $this->kku->getToken($force);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Cannot get KKU Token',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'type' => 'bearer',
            'token' => $token,
        ]);
    }

    // POST /kku/mail-test (auth only)
    public function sendTestMail(Request $request)
    {
        $validated = $request->validate([
            'from' => ['required','email'],
            'fromName' => ['required','string'],
            'to' => ['required','email'],
            'subject' => ['required','string'],
            'message' => ['required','string'],
            'cc' => ['nullable','string'],
            'bcc' => ['nullable','string'],
        ]);

        $result = $this->kku->sendMail($validated);

        $status = $result['ok'] ? 200 : ($result['status'] ?? 500);
        return response()->json($result, $status);
    }
}

