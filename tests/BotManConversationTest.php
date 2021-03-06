<?php

namespace BotMan\BotMan\tests;

use BotMan\BotMan\BotMan;
use PHPUnit_Framework_TestCase;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Drivers\Tests\FakeDriver;
use BotMan\BotMan\Drivers\Tests\ProxyDriver;
use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Tests\Fixtures\TestDataConversation;

class BotManConversationTest extends PHPUnit_Framework_TestCase
{
    /** @var BotMan */
    private $botman;
    /** @var FakeDriver */
    private $fakeDriver;

    public static function setUpBeforeClass()
    {
        DriverManager::loadDriver(ProxyDriver::class);
    }

    public static function tearDownAfterClass()
    {
        DriverManager::unloadDriver(ProxyDriver::class);
    }

    protected function setUp()
    {
        $this->fakeDriver = new FakeDriver();
        ProxyDriver::setInstance($this->fakeDriver);
        $this->botman = BotManFactory::create([]);
    }

    protected function tearDown()
    {
        ProxyDriver::setInstance(FakeDriver::createInactive());
    }

    /** @test */
    public function it_repeats_invalid_image_answers()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertCount(1, $this->fakeDriver->getBotMessages());
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('images');

        static::assertCount(2, $this->fakeDriver->getBotMessages());
        static::assertEquals('Please supply an image', $this->fakeDriver->getBotMessages()[1]->getText());

        $this->replyWithFakeMessage('not_an_image');

        static::assertCount(3, $this->fakeDriver->getBotMessages());
        static::assertEquals('Please supply an image', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_calls_custom_repeat_method_on_invalid_image_answers()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertCount(1, $this->fakeDriver->getBotMessages());
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('custom_image_repeat');
        static::assertCount(2, $this->fakeDriver->getBotMessages());
        static::assertEquals('Please supply an image', $this->fakeDriver->getBotMessages()[1]->getText());

        $this->replyWithFakeMessage('not_an_image');
        static::assertCount(3, $this->fakeDriver->getBotMessages());
        static::assertEquals('That is not an image...', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_returns_the_images()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('images');
        static::assertEquals('Please supply an image', $this->fakeDriver->getBotMessages()[1]->getText());

        $message = new IncomingMessage(Image::PATTERN, 'helloman', '#helloworld');
        $message->setImages(['http://foo.com/bar.png']);
        $this->replyWithFakeMessage($message);

        static::assertEquals('http://foo.com/bar.png', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_repeats_invalid_videos_answers()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('videos');
        static::assertEquals('Please supply a video', $this->fakeDriver->getBotMessages()[1]->getText());

        $this->replyWithFakeMessage('not_a_video');
        static::assertEquals('Please supply a video', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_calls_custom_repeat_method_on_invalid_videos_answers()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('custom_video_repeat');
        static::assertEquals('Please supply a video', $this->fakeDriver->getBotMessages()[1]->getText());

        $this->replyWithFakeMessage('not_a_video');
        static::assertEquals('That is not a video...', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_returns_the_videos()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('videos');
        static::assertEquals('Please supply a video', $this->fakeDriver->getBotMessages()[1]->getText());

        $message = new IncomingMessage(Video::PATTERN, 'helloman', '#helloworld');
        $message->setVideos(['http://foo.com/bar.mp4']);
        $this->replyWithFakeMessage($message);

        static::assertEquals('http://foo.com/bar.mp4', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_repeats_invalid_audio_answers()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('audio');
        static::assertEquals('Please supply an audio', $this->fakeDriver->getBotMessages()[1]->getText());

        $this->replyWithFakeMessage('not_an_audio');
        static::assertEquals('Please supply an audio', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_calls_custom_repeat_method_on_invalid_audio_answers()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('custom_audio_repeat');
        static::assertEquals('Please supply an audio', $this->fakeDriver->getBotMessages()[1]->getText());

        $this->replyWithFakeMessage('not_an_audio');
        static::assertEquals('That is not an audio...', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_returns_the_audio()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('audio');
        static::assertEquals('Please supply an audio', $this->fakeDriver->getBotMessages()[1]->getText());

        $message = new IncomingMessage(Audio::PATTERN, 'helloman', '#helloworld');
        $message->setAudio(['http://foo.com/bar.mp3']);
        $this->replyWithFakeMessage($message);

        static::assertEquals('http://foo.com/bar.mp3', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_repeats_invalid_location_answers()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('location');
        static::assertEquals('Please supply a location', $this->fakeDriver->getBotMessages()[1]->getText());

        $this->replyWithFakeMessage('not_a_location');
        static::assertEquals('Please supply a location', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_calls_custom_repeat_method_on_invalid_location_answers()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('custom_location_repeat');
        static::assertEquals('Please supply a location', $this->fakeDriver->getBotMessages()[1]->getText());

        $this->replyWithFakeMessage('not_a_location');
        static::assertEquals('That is not a location...', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    /** @test */
    public function it_returns_the_location()
    {
        $this->botman->hears('Hello', function (BotMan $bot) {
            $bot->startConversation(new TestDataConversation());
        });

        $this->replyWithFakeMessage('Hello');
        static::assertEquals('What do you want to test?', $this->fakeDriver->getBotMessages()[0]->getText());

        $this->replyWithFakeMessage('location');
        static::assertEquals('Please supply a location', $this->fakeDriver->getBotMessages()[1]->getText());

        $message = new IncomingMessage(Location::PATTERN, 'helloman', '#helloworld');
        $location = new Location(41.123, -12.123);
        $message->setLocation($location);
        $this->replyWithFakeMessage($message);

        static::assertEquals('41.123:-12.123', $this->fakeDriver->getBotMessages()[2]->getText());
    }

    private function replyWithFakeMessage($message, $username = 'helloman', $channel = '#helloworld')
    {
        if ($message instanceof IncomingMessage) {
            $this->fakeDriver->messages = [$message];
        } else {
            $this->fakeDriver->messages = [new IncomingMessage($message, $username, $channel)];
        }
        $this->botman->listen();
    }
}
