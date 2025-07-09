<?php

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

    // Set up a mock Drupal container for string_translation BEFORE creating the form
    $container = new \Drupal\Core\DependencyInjection\ContainerBuilder();
    $container->set('string_translation', \Mockery::mock('Drupal\\Core\\StringTranslation\\TranslationInterface'));
    $container->set('config.typed', \Mockery::mock('Drupal\\Core\\Config\\TypedConfigManagerInterface'));
    \Drupal::setContainer($container);

    $form = new EventNotifierSettingsForm($config_factory);

    // Mock the FormStateInterface
    $form_state = \Mockery::mock('Drupal\\Core\\Form\\FormStateInterface');

    $form_array = $form->buildForm([], $form_state);

    expect($form_array)->toBeArray();
    expect($form_array)->toHaveKey('notification_email');
    expect($form_array['notification_email'])->toHaveKey('#type');
    expect($form_array['notification_email']['#type'])->toBe('email');
});

// it('validates the notification email field', function () {
//     $config_factory = \Mockery::mock('Drupal\\Core\\Config\\ConfigFactoryInterface');
//     $config = \Mockery::mock('Drupal\\Core\\Config\\Config');
//     $config->shouldReceive('get')->andReturn('');
//     $config->shouldReceive('getRawData')->andReturn([]);
//     $config_factory->shouldReceive('getEditable')->andReturn($config);

//     $container = new \Drupal\Core\DependencyInjection\ContainerBuilder();
//     $container->set('string_translation', \Mockery::mock('Drupal\\Core\\StringTranslation\\TranslationInterface'));
//     $typedConfig = \Mockery::mock();
//     $typedConfig->shouldReceive('validate')->andReturn([]);
//     $typedConfigManager = \Mockery::mock('Drupal\\Core\\Config\\TypedConfigManagerInterface');
//     $typedConfigManager->shouldReceive('createFromNameAndData')->andReturn($typedConfig);
//     $container->set('config.typed', $typedConfigManager);
//     $emailValidator = \Mockery::mock('Drupal\\Core\\Validator\\EmailValidatorInterface');
//     $emailValidator->shouldReceive('isValid')->andReturn(false);
//     $container->set('email.validator', $emailValidator);
//     \Drupal::setContainer($container);

//     $formObj = new \Drupal\event_notifier\Form\EventNotifierSettingsForm($config_factory);

//     $form_state = \Mockery::mock('Drupal\\Core\\Form\\FormStateInterface');
//     $form_state->shouldReceive('getValue')->with('notification_email')->andReturn('');
//     $form_state->shouldReceive('setErrorByName')->with('notification_email', \Mockery::any())->once();
//     $form_state->shouldReceive('get')->andReturn([]);

//     $form = [];
//     $formObj->validateForm($form, $form_state);
// });

it('submits the form and saves config', function () {
    $config = \Mockery::mock('Drupal\\Core\\Config\\Config');
    $config->shouldReceive('set')->with('notification_email', 'admin@example.com')->andReturnSelf();
    $config->shouldReceive('set')->with('enable_notifications', true)->andReturnSelf();
    $config->shouldReceive('set')->with('notification_types', ['type1', 'type2'])->andReturnSelf();
    $config->shouldReceive('save')->once();

    $config_factory = \Mockery::mock('Drupal\\Core\\Config\\ConfigFactoryInterface');
    $config_factory->shouldReceive('getEditable')->with('event_notifier.settings')->andReturn($config);

    $container = new \Drupal\Core\DependencyInjection\ContainerBuilder();
    $container->set('string_translation', \Mockery::mock('Drupal\\Core\\StringTranslation\\TranslationInterface'));
    $container->set('config.typed', \Mockery::mock('Drupal\\Core\\Config\\TypedConfigManagerInterface'));
    $messenger = \Mockery::mock('Drupal\\Core\\Messenger\\MessengerInterface');
    $messenger->shouldReceive('addStatus')->andReturnNull();
    $container->set('messenger', $messenger);
    \Drupal::setContainer($container);

    $formObj = new \Drupal\event_notifier\Form\EventNotifierSettingsForm($config_factory);

    $form_state = \Mockery::mock('Drupal\\Core\\Form\\FormStateInterface');
    $form_state->shouldReceive('getValue')->with('notification_email')->andReturn('admin@example.com');
    $form_state->shouldReceive('getValue')->with('enable_notifications')->andReturn(true);
    $form_state->shouldReceive('getValue')->with('notification_types')->andReturn(['type1', 'type2']);

    $form = [];
    $formObj->submitForm($form, $form_state);
});

it('handles missing config gracefully', function () {
    $config_factory = \Mockery::mock('Drupal\\Core\\Config\\ConfigFactoryInterface');
    $config = \Mockery::mock('Drupal\\Core\\Config\\Config');
    $config->shouldReceive('get')->andReturn(null);
    $config_factory->shouldReceive('getEditable')->andReturn($config);

    $container = new \Drupal\Core\DependencyInjection\ContainerBuilder();
    $container->set('string_translation', \Mockery::mock('Drupal\\Core\\StringTranslation\\TranslationInterface'));
    $container->set('config.typed', \Mockery::mock('Drupal\\Core\\Config\\TypedConfigManagerInterface'));
    \Drupal::setContainer($container);

    $formObj = new \Drupal\event_notifier\Form\EventNotifierSettingsForm($config_factory);

    $form_state = \Mockery::mock('Drupal\\Core\\Form\\FormStateInterface');
    $form_array = $formObj->buildForm([], $form_state);

    expect($form_array)->toBeArray();
});
