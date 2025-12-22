<?php

use App\Jobs\ProcessContactForm;
use Illuminate\Support\Facades\Queue;

test('contact form renders successfully', function () {
    $response = $this->get('/contact');

    $response->assertSuccessful();
});

test('guest can submit contact form', function () {
    Queue::fake();

    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'This is a test message.',
    ]);

    $response->assertSessionHas('success');

    Queue::assertPushed(ProcessContactForm::class, function ($job) {
        return $job->name === 'John Doe'
            && $job->email === 'john@example.com'
            && $job->message === 'This is a test message.';
    });
});

test('contact form validates required fields', function () {
    $response = $this->post('/contact', []);

    $response->assertSessionHasErrors(['name', 'email', 'message']);
});

test('contact form validates email format', function () {
    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'message' => 'Test message',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('contact form validates minimum message length', function () {
    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Short',
    ]);

    $response->assertSessionHasErrors(['message']);
});

test('contact form accepts optional company field', function () {
    Queue::fake();

    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'company' => 'Acme Inc.',
        'message' => 'This is a test message.',
    ]);

    $response->assertSessionHas('success');

    Queue::assertPushed(ProcessContactForm::class, function ($job) {
        return $job->company === 'Acme Inc.';
    });
});

test('contact form rate limits submissions', function () {
    Queue::fake();

    for ($i = 0; $i < 3; $i++) {
        $response = $this->post('/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is test message number ' . ($i + 1),
        ]);

        $response->assertSessionHas('success');
    }

    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'This should be rate limited',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('contact form validates maximum field lengths', function () {
    $response = $this->post('/contact', [
        'name' => str_repeat('a', 256),
        'email' => 'john@example.com',
        'message' => 'Test message',
    ]);

    $response->assertSessionHasErrors(['name']);

    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => str_repeat('a', 245) . '@example.com', // 257+ chars
        'message' => 'Test message',
    ]);

    $response->assertSessionHasErrors(['email']);

    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => str_repeat('a', 1001),
    ]);

    $response->assertSessionHasErrors(['message']);
});

