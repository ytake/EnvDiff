<?php

/**
 * Class EnvScriptTest
 *
 * @see \Ytake\EnvDiff\EnvScript
 */
class EnvScriptTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Prophecy\Prophecy\ObjectProphecy */
    private $event;

    protected function setUp()
    {
        $this->event = $this->prophesize('Composer\Script\Event');
    }

    protected function tearDown()
    {
        if (file_exists('.env')) {
            unlink('.env');
        }

        if (file_exists('.env.testing')) {
            unlink('.env.testing');
        }
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testShouldThrowExceptionNotFoundDotEnv()
    {
        \Ytake\EnvDiff\EnvScript::postUpdate($this->event->reveal());
    }

    /**
     * @test
     */
    public function testShouldNoReturn()
    {
$document = <<< EOD
APP_MESSAGE=testing
EOD;
        file_put_contents('.env', $document);
        $this->assertNull(\Ytake\EnvDiff\EnvScript::postUpdate($this->event->reveal()));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testShouldReturnEnvironmentNotMatch()
    {
        $document = <<< EOD
APP_MESSAGE=testing
EOD;
        file_put_contents('.env', $document);
        file_put_contents('.env.testing', <<< EOD
APP_MESSAGE=testing
VARIABLE1=testing
EOD
        );
        $buffer = new Composer\IO\BufferIO();
        $this->event->getArguments()->willReturn([]);
        $this->event->getIO()->willReturn($buffer);
        try {
            \Ytake\EnvDiff\EnvScript::postUpdate($this->event->reveal());
        } catch (\RuntimeException $e) {
            $this->assertSame('[.env]  ENVIRONMENT-NAME:[VARIABLE1] not found.', trim($buffer->getOutput()));
            throw $e;
        }
    }

    public function testShouldReturnNullForForceArgument()
    {
        $document = <<< EOD
APP_MESSAGE=testing
EOD;
        file_put_contents('.env', $document);
        file_put_contents('.env.testing', <<< EOD
APP_MESSAGE=testing
VARIABLE1=testing
EOD
        );
        $buffer = new Composer\IO\BufferIO();
        $this->event->getArguments()->willReturn(['--force']);
        $this->event->getIO()->willReturn($buffer);
        \Ytake\EnvDiff\EnvScript::postUpdate($this->event->reveal());
    }
}
