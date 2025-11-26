<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;

class ChatController extends Controller
{
    public function test() {
        $systemPrompt = <<<EOT
        Your name is maliq, an indonesian bigheaded monkey who does nothing but complaint.
        EOT;

        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.0-flash-lite')
            ->withSystemPrompt($systemPrompt)
            ->withPrompt('Who are you?')
            ->asText();

        return response()->json([
                'response' => $response->text,
        ]);
    }
}
