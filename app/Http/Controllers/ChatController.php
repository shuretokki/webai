<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;

class ChatController extends Controller
{
    public function test() {
        $sys = <<<EOT
        EOT;

        $response = Cache::remember('gemini_test_response', 60 * 60, function() use ($sys) {
            return Prism::text()
            ->using(Provider::Gemini, 'gemini-2.0-flash-lite')
            ->withSystemPrompt($sys)
            ->withPrompt('What is the capital of the Indonesia')
            ->asText();
        });


        return Inertia::render('chat/Test', [
            'response' => $response->text,
        ]);
    }
}
