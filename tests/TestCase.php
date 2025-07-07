<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Mockery;
use Drupal\Tests\UnitTestCase;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Language\Language;

/**
 * Base test case for Event Notifier module tests.
 */
class TestCase extends BaseTestCase
{
    protected $container;
    protected $configFactory;
    protected $mailManager;
    protected $loggerFactory;
    protected $languageManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpContainer();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Set up the Drupal container with mocked services.
     */
    protected function setUpContainer()
    {
        $this->container = new ContainerBuilder();

        // Mock configuration factory
        $this->configFactory = Mockery::mock(ConfigFactoryInterface::class);
        $this->container->set('config.factory', $this->configFactory);

        // Mock mail manager
        $this->mailManager = Mockery::mock(MailManagerInterface::class);
        $this->container->set('plugin.manager.mail', $this->mailManager);

        // Mock logger factory
        $this->loggerFactory = Mockery::mock(LoggerChannelFactoryInterface::class);
        $this->container->set('logger.factory', $this->loggerFactory);

        // Mock language manager
        $this->languageManager = Mockery::mock(LanguageManagerInterface::class);
        $language = Mockery::mock(Language::class);
        $language->shouldReceive('getId')->andReturn('en');
        $this->languageManager->shouldReceive('getDefaultLanguage')->andReturn($language);
        $this->container->set('language_manager', $this->languageManager);

        \Drupal::setContainer($this->container);
    }

    /**
     * Create a mock configuration object.
     */
    protected function createMockConfig($values = [])
    {
        $config = Mockery::mock(ImmutableConfig::class);

        foreach ($values as $key => $value) {
            $config->shouldReceive('get')->with($key)->andReturn($value);
        }

        return $config;
    }

    /**
     * Create a mock logger channel.
     */
    protected function createMockLogger()
    {
        $logger = Mockery::mock(LoggerChannelInterface::class);
        $logger->shouldReceive('info')->andReturnNull();
        $logger->shouldReceive('error')->andReturnNull();

        $this->loggerFactory->shouldReceive('get')
            ->with('event_notifier')
            ->andReturn($logger);

        return $logger;
    }

    /**
     * Create a mock event node.
     */
    protected function createMockEventNode($title = 'Test Event', $published = true)
    {
        $node = Mockery::mock('Drupal\node\NodeInterface');
        $node->shouldReceive('bundle')->andReturn('event');
        $node->shouldReceive('isPublished')->andReturn($published);
        $node->shouldReceive('getTitle')->andReturn($title);
        $node->shouldReceive('toUrl')->andReturnSelf();
        $node->shouldReceive('toString')->andReturn('http://example.com/node/1');

        // Mock field access
        $node->shouldReceive('hasField')->with('field_event_date')->andReturn(true);
        $node->shouldReceive('hasField')->with('field_location')->andReturn(true);
        $node->shouldReceive('get')->with('field_event_date')->andReturnSelf();
        $node->shouldReceive('get')->with('field_location')->andReturnSelf();
        $node->shouldReceive('isEmpty')->andReturn(false);
        $node->shouldReceive('first')->andReturnSelf();
        $node->shouldReceive('getValue')->andReturn(['value' => '2024-06-15T09:00:00']);
        $node->shouldReceive('value')->andReturn('Test Location');

        return $node;
    }
}
