<?php declare(strict_types=1);

namespace MHCG\Monolog\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class WPCLIHandler extends AbstractProcessingHandler
{
    const WPCLI_FORMAT_STANDARD = "%message%";
    //const WPCLI_FORMAT_DEBUG = "[%datetime%] %message% %context% %extra%";
    //const WPCLI_FORMAT_VERBOSE = "%message% %context% %extra%";

    /**
     * WPCLIHandler constructor.
     *
     * @param int $level The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($level = Logger::CRITICAL, $bubble = true)
    {
        $isInWPCLI = (defined('WP_CLI') && WP_CLI);
        if (!$isInWPCLI) {
            throw new \RuntimeException('');
        }

        parent::__construct($level, $bubble);
    }


    /**
     * Writes the record down to the log of the implementing handler
     *
     * @see https://seldaek.github.io/monolog/doc/message-structure.html
     *
     * @param  array $record
     *
     * @return void
     * @throws \RuntimeException If a level is passed that is not currently mapped to a WP_CLI:: method.
     * @throws \WP_CLI\ExitException Thrown by WP_CLI::error() method when critical.
     */
    protected function write(array $record)
    {
        // init vars for whatever being used
        //$message = (string)$record['message'] ?: '';
        $level = (int)$record['level']; // no default as think it would be an error to be empty
        $level_name = (string)$record['level_name'] ?: '';
        //$context = (array)$record['context'] ?: array();
        //$channel = (string)$record['channel'] ?: ''; // don't think this can be empty either really
        //$datetime = (object)$record['datetime']; // no default as think it would be an error to be empty
        //$extra = (array)$record['extra'] ?: array();

        // formatted message
        $formatter = new LineFormatter(self::WPCLI_FORMAT_STANDARD);
        $formatted = $formatter->format($record);

        switch ($level) {
            case Logger::DEBUG:
                \WP_CLI::debug($formatted);
                break;
            case Logger::INFO:
                \WP_CLI::log($formatted);
                break;
            case Logger::NOTICE:
                \WP_CLI::warning('(' . $level_name . ') ' . $formatted);
                break;
            case Logger::WARNING:
                \WP_CLI::warning('(' . $level_name . ') ' . $formatted);
                break;
            case Logger::ERROR:
                \WP_CLI::error('(' . $level_name . ') ' . $formatted, false);
                break;
            case Logger::CRITICAL:
                \WP_CLI::error('(' . $level_name . ') ' . $formatted, true);
                break;
            case Logger::ALERT:
                \WP_CLI::error('(' . $level_name . ') ' . $formatted, true);
                break;
            case Logger::EMERGENCY:
                \WP_CLI::error('(' . $level_name . ') ' . $formatted, true);
                break;
            default:
                throw new \RuntimeException(
                    'NotImplementedException: Unsupported level: ' . $level_name . '(' . $level . ')'
                );
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        // Going to delegate if the message should be displayed or not to the WP-CLI methods;
        // WP-CLI already has it's own way of deciding if the message should show or not.

        // could probably just return true or the levels from Logger, but more could be added in the future
        // that would need mapping to the WP-CLI:: method.
        $supported = array(
            Logger::DEBUG,
            Logger::INFO,
            Logger::NOTICE,
            Logger::WARNING,
            Logger::ERROR,
            Logger::CRITICAL,
            Logger::ALERT,
            Logger::EMERGENCY
        );
        $level = (int)$record['level'];
        return in_array($level, $supported, true);
    }
}
