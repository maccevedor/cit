<?php

namespace Tests;

use Mockery;

require_once __DIR__ . '/../../modules/custom/event_notifier/event_notifier.module';

uses(TestCase::class);

it('runs a basic Pest test', function () {
    expect(true)->toBeTrue();
});

it('adds two numbers', function () {
    expect(1 + 1)->toBe(2);
});

it('sends notification when event is created and published', function () {
    // Arrange
    $node = $this->createMockEventNode('Tech Conference 2024', true);
    $config = $this->createMockConfig(['notification_email' => 'admin@example.com']);

    $this->configFactory->shouldReceive('get')
        ->with('event_notifier.settings')
        ->andReturn($config);

    $this->mailManager->shouldReceive('mail')
        ->with('event_notifier', 'event_notification', 'admin@example.com', 'en', Mockery::any())
        ->andReturn(['result' => true]);

    $logger = $this->createMockLogger();
    $this->loggerFactory->shouldReceive('get')
        ->with('event_notifier')
        ->andReturn($logger);

    // Act
    event_notifier_node_insert($node);

    // Assert
    $this->mailManager->shouldHaveReceived('mail');
});

/*
// ... rest of the file is commented out for debugging Pest test discovery ...
*/
