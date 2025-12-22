<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Text\Response as TextResponse;
use Prism\Prism\ValueObjects\Usage;
use Prism\Prism\ValueObjects\Meta;
use Prism\Prism\Enums\FinishReason;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        Prism::fake([
            new TextResponse(
                steps: collect([]),
                text: 'This is a test response from the AI assistant.',
                finishReason: FinishReason::Stop,
                toolCalls: [],
                toolResults: [],
                usage: new Usage(10, 15),
                meta: new Meta('test-id', 'test-model'),
                messages: collect([]),
                additionalContent: [],
            ),
        ]);
    }
}
