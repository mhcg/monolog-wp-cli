<?php declare(strict_types=1);

/**
 * Unit tests for WPCLIHandler
 */

namespace MHCGDev\Monolog\Handler;

use MHCG\Monolog\Handler\WPCLIHandler;
use PHPUnit\Framework\TestCase;
use Monolog\Logger;

/**
 * Class WPCLIHandlerTest
 *
 * @covers MHCG\Monolog\Handler\WPCLIHandler
 *
 * @package MHCGDev\Monolog\Handler
 */
class WPCLIHandlerTest extends TestCase
{
    /** @var string Constant for bodging sanity check */
    const RUNNING_IN_TEST = 'RunningInTest_RunningInTest';

    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('ExitException')) {
            class_alias('MHCGDev\Monolog\Stubs\MockExitException', 'ExitException');
        }
        if (!class_exists('WP_CLI')) {
            class_alias('MHCGDev\Monolog\Stubs\MockWPCLI', 'WP_CLI');
        }
    }


    //<editor-fold desc="Private helper methods">

    /**
     * Sanity check for all (well most at least) tests.
     *
     * Basically it's around checking if running in WP-CLI or not as unit tests should not be ran in there.
     */
    private function sanityCheck()
    {
        $message = 'Unit tests should not be ran from within WP-CLI environment';
        if (defined('WP_CLI')) {
            $this->assertTrue(WP_CLI == self::RUNNING_IN_TEST, $message);
        } else {
            $this->assertFalse(defined('WP_CLI'), $message);
        }
    }

    /**
     * Will need to pretend to be running under WP-CLI for most tests.
     */
    private function pretendToBeInWPCLI()
    {
        defined('WP_CLI') || define('WP_CLI', self::RUNNING_IN_TEST);
    }

    /**
     * Fully usable WPCLIHandler object.
     *
     * @return WPCLIHandler
     */
    private static function getHandleObjectForStandardTest()
    {
        return new WPCLIHandler(Logger::DEBUG);
    }

    /**
     * Fully usable Logger object.
     *
     * @return Logger
     */
    private static function getLoggerObjectForStandardTest()
    {
        return new Logger(self::RUNNING_IN_TEST);
    }

    /**
     * Partial record array with level.
     *
     * @param int $level
     * @return array
     */
    private static function getLoggerRecordArrayWithLevel(int $level = Logger::DEBUG)
    {
        $array = array(
            'level' => $level
        );
        return $array;
    }

    //</editor-fold>

    //<editor-fold desc="Constructor Tests">

    /**
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::__construct
     */
    public function testConstructorNotInWPCLI()
    {
        $this->sanityCheck();

        $this->expectException(\RuntimeException::class);
        $var = self::getHandleObjectForStandardTest();
        $this->assertTrue(is_object($var));
        unset($var);
    }

    /**
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::__construct
     */
    public function testConstructorInWPCLI()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $var = self::getHandleObjectForStandardTest();
        $this->assertTrue(is_object($var));
        $this->isInstanceOf('\MHCG\Monolog\Handler\WPCLIHandler');
        unset($var);
    }

    //</editor-fold>


    //<editor-fold desc="Main Logger Method Tests">

    /**
     * Tests the handler can actually be added to a Logger ok.
     */
    public function testPushHandler()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $logger = self::getLoggerObjectForStandardTest();
        $logger->pushHandler(self::getHandleObjectForStandardTest());

        unset($logger);
        $this->assertTrue(true);
    }

    //</editor-fold>

    //<editor-fold desc="IsHandling Tests">
    /**
     * Tests isHandling of WPCLIHandler returns false for an unsupported logging level.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::isHandling
     */
    public function testIsHandlingInvalid()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $handler = self::getLoggerObjectForStandardTest();
        $this->assertFalse($handler->isHandling(self::getLoggerRecordArrayWithLevel(999)));
    }

    /**
     * Tests isHandling of WPCLIHandler returns true for support logging level DEBUG.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::isHandling
     */
    public function testIsHandlingValidDebug()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $handler = self::getHandleObjectForStandardTest();
        $this->assertTrue($handler->isHandling(self::getLoggerRecordArrayWithLevel(Logger::DEBUG)));
    }

    /**
     * Tests isHandling of WPCLIHandler returns true for support logging level INFO.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::isHandling
     */
    public function testIsHandlingValidInfo()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $handler = self::getHandleObjectForStandardTest();
        $this->assertTrue($handler->isHandling(self::getLoggerRecordArrayWithLevel(Logger::INFO)));
    }

    /**
     * Tests isHandling of WPCLIHandler returns true for support logging level NOTICE.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::isHandling
     */
    public function testIsHandlingValidNotice()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $handler = self::getHandleObjectForStandardTest();
        $this->assertTrue($handler->isHandling(self::getLoggerRecordArrayWithLevel(Logger::NOTICE)));
    }

    /**
     * Tests isHandling of WPCLIHandler returns true for support logging level WARNING.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::isHandling
     */
    public function testIsHandlingValidWarning()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $handler = self::getHandleObjectForStandardTest();
        $this->assertTrue($handler->isHandling(self::getLoggerRecordArrayWithLevel(Logger::WARNING)));
    }

    /**
     * Tests isHandling of WPCLIHandler returns true for support logging level ERROR.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::isHandling
     */
    public function testIsHandlingValidError()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $handler = self::getHandleObjectForStandardTest();
        $this->assertTrue($handler->isHandling(self::getLoggerRecordArrayWithLevel(Logger::ERROR)));
    }

    /**
     * Tests isHandling of WPCLIHandler returns true for support logging level CRITICAL.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::isHandling
     */
    public function testIsHandlingValidCritical()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $handler = self::getHandleObjectForStandardTest();
        $this->assertTrue($handler->isHandling(self::getLoggerRecordArrayWithLevel(Logger::CRITICAL)));
    }

    /**
     * Tests isHandling of WPCLIHandler returns true for support logging level ALERT.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::isHandling
     */
    public function testIsHandlingValidAlert()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $handler = self::getHandleObjectForStandardTest();
        $this->assertTrue($handler->isHandling(self::getLoggerRecordArrayWithLevel(Logger::ALERT)));
    }

    /**
     * Tests isHandling of WPCLIHandler returns true for support logging level EMERGENCY.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::isHandling
     */
    public function testIsHandlingValidEmergency()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $handler = self::getHandleObjectForStandardTest();
        $this->assertTrue($handler->isHandling(self::getLoggerRecordArrayWithLevel(Logger::EMERGENCY)));
    }

    //</editor-fold>

    //<editor-fold desc="Logging method tests">
    /**
     * Test that Logger::debug() doesn't throw an error using WPCLIHander.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::write
     */
    public function testHandlerOkForDebug()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $logger = self::getLoggerObjectForStandardTest();
        $logger->pushHandler(self::getHandleObjectForStandardTest());

        $logger->debug('This is the end...');

        unset($logger);
        $this->assertTrue(true);
    }

    /**
     * Test that Logger::info() doesn't throw an error using WPCLIHander.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::write
     */
    public function testHandlerOkForInfo()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $logger = self::getLoggerObjectForStandardTest();
        $logger->pushHandler(self::getHandleObjectForStandardTest());

        $logger->info('This is the end...');

        unset($logger);
        $this->assertTrue(true);
    }

    /**
     * Test that Logger::notice() doesn't throw an error using WPCLIHander.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::write
     */
    public function testHandlerOkForNotice()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $logger = self::getLoggerObjectForStandardTest();
        $logger->pushHandler(self::getHandleObjectForStandardTest());

        $logger->notice('This is the end...');

        unset($logger);
        $this->assertTrue(true);
    }

    /**
     * Test that Logger::warning() doesn't throw an error using WPCLIHander.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::write
     */
    public function testHandlerOkForWarning()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $logger = self::getLoggerObjectForStandardTest();
        $logger->pushHandler(self::getHandleObjectForStandardTest());

        $logger->warning('This is the end...');

        unset($logger);
        $this->assertTrue(true);
    }

    /**
     * Test that Logger::error() doesn't throw an error using WPCLIHander.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::write
     */
    public function testHandlerOkForError()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $logger = self::getLoggerObjectForStandardTest();
        $logger->pushHandler(self::getHandleObjectForStandardTest());

        $logger->error('This is the end...');

        unset($logger);
        $this->assertTrue(true);
    }

    /**
     * Test that Logger::critical() DOES throw an error using WPCLIHander.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::write
     */
    public function testHandlerOkForCritical()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $logger = self::getLoggerObjectForStandardTest();
        $logger->pushHandler(self::getHandleObjectForStandardTest());

        $this->expectException('ExitException');
        $logger->critical('This is the end...');

        unset($logger);
        $this->assertTrue(true);
    }

    /**
     * Test that Logger::alert() DOES throw an error using WPCLIHander.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::write
     */
    public function testHandlerOkForAlert()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $logger = self::getLoggerObjectForStandardTest();
        $logger->pushHandler(self::getHandleObjectForStandardTest());

        $this->expectException('ExitException');
        $logger->alert('This is the end...');

        unset($logger);
        $this->assertTrue(true);
    }

    /**
     * Test that Logger::emergency() DOES throw an error using WPCLIHander.
     *
     * @covers \MHCG\Monolog\Handler\WPCLIHandler::write
     */
    public function testHandlerOkForEmergency()
    {
        $this->sanityCheck();

        $this->pretendToBeInWPCLI();
        $logger = self::getLoggerObjectForStandardTest();
        $logger->pushHandler(self::getHandleObjectForStandardTest());

        $this->expectException('ExitException');
        $logger->emergency('This is the end...');

        unset($logger);
        $this->assertTrue(true);
    }
    //</editor-fold>
}
