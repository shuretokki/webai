<?php

use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\ValueObjects\Usage;
use Prism\Prism\Testing\TextResponseFake;

it('can generate text', function () {
    $fakeResponse = TextResponseFake::make()
        ->withText('Hello, I am Gemini!')
        ->withUsage(new Usage(10, 20));

    $fake = Prism::fake([$fakeResponse]);

    $response = Prism::text()
        ->using(Provider::Gemini, 'gemini-2.0-flash')
        ->withPrompt('Who are you?')
        ->asText();

    expect($response->text)->toBe('Hello, I am Gemini!');
});
