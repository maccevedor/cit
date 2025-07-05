<?php

use Pest\TestSuite;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(
    Tests\TestCase::class,
    // Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('tests');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createEventNode($title = 'Test Event', $date = '2024-06-15T09:00:00', $location = 'Test Location', $body = 'Test event description')
{
    return \Drupal::entityTypeManager()
        ->getStorage('node')
        ->create([
            'type' => 'event',
            'title' => $title,
            'field_event_date' => [
                'value' => $date,
            ],
            'field_location' => $location,
            'body' => [
                'value' => $body,
                'format' => 'basic_html',
            ],
            'status' => 1,
        ]);
}

function createUnpublishedEventNode($title = 'Draft Event')
{
    return \Drupal::entityTypeManager()
        ->getStorage('node')
        ->create([
            'type' => 'event',
            'title' => $title,
            'field_event_date' => [
                'value' => '2024-06-15T09:00:00',
            ],
            'field_location' => 'Test Location',
            'body' => [
                'value' => 'Draft event description',
                'format' => 'basic_html',
            ],
            'status' => 0,
        ]);
}

function mockMailService()
{
    $mockMailManager = Mockery::mock('Drupal\Core\Mail\MailManagerInterface');
    $mockMailManager->shouldReceive('mail')
        ->andReturn(['result' => true]);

    \Drupal::getContainer()->set('plugin.manager.mail', $mockMailManager);

    return $mockMailManager;
}

function mockLoggerService()
{
    $mockLogger = Mockery::mock('Drupal\Core\Logger\LoggerChannelInterface');
    $mockLogger->shouldReceive('info')->andReturnNull();
    $mockLogger->shouldReceive('error')->andReturnNull();

    \Drupal::getContainer()->set('logger.channel.event_notifier', $mockLogger);

    return $mockLogger;
}
