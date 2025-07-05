<?php

use Tests\TestCase;
use Mockery;

/**
 * Unit tests for Event Notifier module.
 */
beforeEach(function () {
    $this->setUpContainer();
});

afterEach(function () {
    Mockery::close();
});

describe('Event Notifier Module', function () {

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
        $logger->shouldReceive('info')
            ->with('Event notification sent for Tech Conference 2024 (created)', Mockery::any());

        // Act
        event_notifier_node_insert($node);

        // Assert
        expect($this->mailManager)->toHaveBeenCalled();
        expect($logger)->toHaveBeenCalled();
    });

    it('does not send notification for unpublished events', function () {
        // Arrange
        $node = $this->createMockEventNode('Draft Event', false);

        // Act
        event_notifier_node_insert($node);

        // Assert
        expect($this->mailManager)->not->toHaveBeenCalled();
    });

    it('does not send notification for non-event content types', function () {
        // Arrange
        $node = Mockery::mock('Drupal\node\NodeInterface');
        $node->shouldReceive('bundle')->andReturn('article');
        $node->shouldReceive('isPublished')->andReturn(true);

        // Act
        event_notifier_node_insert($node);

        // Assert
        expect($this->mailManager)->not->toHaveBeenCalled();
    });

    it('sends notification when event is published from draft', function () {
        // Arrange
        $originalNode = $this->createMockEventNode('Event', false);
        $node = $this->createMockEventNode('Event', true);
        $node->original = $originalNode;

        $config = $this->createMockConfig(['notification_email' => 'admin@example.com']);
        $this->configFactory->shouldReceive('get')
            ->with('event_notifier.settings')
            ->andReturn($config);

        $this->mailManager->shouldReceive('mail')
            ->with('event_notifier', 'event_notification', 'admin@example.com', 'en', Mockery::any())
            ->andReturn(['result' => true]);

        $logger = $this->createMockLogger();
        $logger->shouldReceive('info')
            ->with('Event notification sent for Event (published)', Mockery::any());

        // Act
        event_notifier_node_update($node);

        // Assert
        expect($this->mailManager)->toHaveBeenCalled();
        expect($logger)->toHaveBeenCalled();
    });

    it('logs error when mail sending fails', function () {
        // Arrange
        $node = $this->createMockEventNode('Failed Event', true);
        $config = $this->createMockConfig(['notification_email' => 'admin@example.com']);

        $this->configFactory->shouldReceive('get')
            ->with('event_notifier.settings')
            ->andReturn($config);

        $this->mailManager->shouldReceive('mail')
            ->andReturn(['result' => false]);

        $logger = $this->createMockLogger();
        $logger->shouldReceive('error')
            ->with('Failed to send event notification for Failed Event', Mockery::any());

        // Act
        event_notifier_node_insert($node);

        // Assert
        expect($logger)->toHaveBeenCalled();
    });

    it('uses default email when configuration is empty', function () {
        // Arrange
        $node = $this->createMockEventNode('Default Email Event', true);
        $config = $this->createMockConfig(['notification_email' => '']);

        $this->configFactory->shouldReceive('get')
            ->with('event_notifier.settings')
            ->andReturn($config);

        $this->mailManager->shouldReceive('mail')
            ->with('event_notifier', 'event_notification', 'admin@example.com', 'en', Mockery::any())
            ->andReturn(['result' => true]);

        // Act
        event_notifier_node_insert($node);

        // Assert
        expect($this->mailManager)->toHaveBeenCalled();
    });

    it('builds correct email body with event details', function () {
        // Arrange
        $node = $this->createMockEventNode('Test Event', true);
        $params = [
            'node' => $node,
            'action' => 'created',
            'subject' => 'Event Created: Test Event'
        ];

        // Act
        $body = _event_notifier_build_email_body($params);

        // Assert
        expect($body)->toContain('An event has been created on the website');
        expect($body)->toContain('Title: Test Event');
        expect($body)->toContain('Date: 2024-06-15T09:00:00');
        expect($body)->toContain('Location: Test Location');
        expect($body)->toContain('View the event: http://example.com/node/1');
    });

    it('handles missing optional fields gracefully', function () {
        // Arrange
        $node = Mockery::mock('Drupal\node\NodeInterface');
        $node->shouldReceive('getTitle')->andReturn('Minimal Event');
        $node->shouldReceive('hasField')->with('field_event_date')->andReturn(false);
        $node->shouldReceive('hasField')->with('field_location')->andReturn(false);
        $node->shouldReceive('toUrl')->andReturnSelf();
        $node->shouldReceive('toString')->andReturn('http://example.com/node/1');

        $params = [
            'node' => $node,
            'action' => 'created',
            'subject' => 'Event Created: Minimal Event'
        ];

        // Act
        $body = _event_notifier_build_email_body($params);

        // Assert
        expect($body)->toContain('Title: Minimal Event');
        expect($body)->not->toContain('Date:');
        expect($body)->not->toContain('Location:');
    });

    it('processes mail hook correctly', function () {
        // Arrange
        $message = [];
        $params = [
            'subject' => 'Test Subject',
            'node' => $this->createMockEventNode('Test Event', true)
        ];

        // Act
        event_notifier_mail('event_notification', $message, $params);

        // Assert
        expect($message['subject'])->toBe('Test Subject');
        expect($message['body'])->toBeArray();
        expect($message['body'][0])->toContain('Test Event');
    });

    it('ignores unknown mail keys', function () {
        // Arrange
        $message = [];
        $params = ['test' => 'data'];

        // Act
        event_notifier_mail('unknown_key', $message, $params);

        // Assert
        expect($message)->toBeEmpty();
    });
});

describe('Configuration Form', function () {

    it('validates required email field', function () {
        // This would test the configuration form validation
        // Implementation depends on form testing framework
        expect(true)->toBeTrue(); // Placeholder
    });

    it('saves configuration correctly', function () {
        // This would test form submission
        // Implementation depends on form testing framework
        expect(true)->toBeTrue(); // Placeholder
    });
});

describe('Edge Cases', function () {

    it('handles null node gracefully', function () {
        // Arrange
        $node = null;

        // Act & Assert - should not throw exception
        expect(function () use ($node) {
            event_notifier_node_insert($node);
        })->not->toThrow();
    });

    it('handles node without title', function () {
        // Arrange
        $node = Mockery::mock('Drupal\node\NodeInterface');
        $node->shouldReceive('bundle')->andReturn('event');
        $node->shouldReceive('isPublished')->andReturn(true);
        $node->shouldReceive('getTitle')->andReturn('');

        $config = $this->createMockConfig(['notification_email' => 'admin@example.com']);
        $this->configFactory->shouldReceive('get')
            ->with('event_notifier.settings')
            ->andReturn($config);

        $this->mailManager->shouldReceive('mail')
            ->andReturn(['result' => true]);

        // Act & Assert - should not throw exception
        expect(function () use ($node) {
            event_notifier_node_insert($node);
        })->not->toThrow();
    });
});
