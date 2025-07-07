<?php

namespace Tests;

use Drupal\event_notifier\Form\EventNotifierSettingsForm;
use PHPUnit\Framework\TestCase;

uses(TestCase::class);

it('builds the event notifier settings form', function () {
    $config_factory = \Mockery::mock('Drupal\\Core\\Config\\ConfigFactoryInterface');
    $config = \Mockery::mock('Drupal\\Core\\Config\\Config');
    $config->shouldReceive('get')
        ->with('notification_email')
        ->andReturn('');
    $config->shouldReceive('get')
        ->with('enable_notifications')
        ->andReturn(true);
    $config->shouldReceive('get')
        ->with('notification_types')
        ->andReturn([]);
    $config_factory->shouldReceive('getEditable')
        ->with('event_notifier.settings')
        ->andReturn($config);
    $form = new EventNotifierSettingsForm($config_factory);

    // Set up a mock Drupal container for string_translation
    $container = new \Drupal\Core\DependencyInjection\ContainerBuilder();
    $container->set('string_translation', \Mockery::mock('Drupal\\Core\\StringTranslation\\TranslationInterface'));
    \Drupal::setContainer($container);

    // Mock the FormStateInterface
    $form_state = \Mockery::mock('Drupal\\Core\\Form\\FormStateInterface');

    $form_array = $form->buildForm([], $form_state);

    expect($form_array)->toBeArray();
    expect($form_array)->toHaveKey('notification_email');
    expect($form_array['notification_email'])->toHaveKey('#type');
    expect($form_array['notification_email']['#type'])->toBe('email');
});
