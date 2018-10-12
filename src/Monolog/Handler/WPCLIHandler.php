<?php declare(strict_types=1);

/**
 * Handler for Monolog that uses WP-CLI methods to for logging.
 *
 * LICENSE: See LICENSE file included with this file.
 *
 * @version 1.0.0
 *
 * @since 1.0.0
 * @package MHCG\Monolog\Handler
 *
 * @link https://github.com/mhcg/monolog-wp-cli
 *
 * @author Mark Heydon <contact@mhcg.co.uk>
 * @license MIT
 */

namespace MHCG\Monolog\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Handler for Monolog that uses WP-CLI methods to for logging.
 *
 * @package MHCG\Monolog\Handler
 */
class WPCLIHandler extends AbstractProcessingHandler
{
    /** @var string Format used when WP_DEBUG disabled */
    const WPCLI_FORMAT_STANDARD = "%message%";
    /** @var string Format used when WP_DEBUG enabled */
    const WPCLI_FORMAT_VERBOSE = "%message% %context% %extra%";

    private $verbose = false;

    /**
     * WPCLIHandler constructor.
     *
     * @param int $level The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
     * @param Boolean $verbose Will use this or WP_DEBUG to include extra information in logging messages
     */
    public function __construct($level = Logger::WARNING, $bubble = true, $verbose = false)
    {
        $isInWPCLI = (defined('WP_CLI') && WP_CLI);
        if (! $isInWPCLI) {
            throw new \RuntimeException('');
        }

        parent::__construct($level, $bubble);

        $wp_debug_or_verbose = (defined('WP_DEBUG') ? WP_DEBUG : false) || $verbose;
        $this->verbose = $wp_debug_or_verbose;
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
     * @throws \ExitException Thrown by WP_CLI::error() method when critical.
     */
    protected function write(array $record)
    {
        // init vars for whatever being used
        //$message = (string)$record['message'] ?: '';
        $level      = (int) $record['level']; // no default as think it would be an error to be empty
        $level_name = (string) $record['level_name'] ?: '';
        //$context = (array)$record['context'] ?: array();
        //$channel = (string)$record['channel'] ?: ''; // don't think this can be empty either really
        //$datetime = (object)$record['datetime']; // no default as think it would be an error to be empty
        //$extra = (array)$record['extra'] ?: array();

        // formatted message
        $formatted = $this->getFormatter()->format($record);

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
        // bodge for debug level as needs to always call that;
        // WP_CLI deals with --debug command argument
        $level = (int) $record['level'];
        if ($level == Logger::DEBUG) {
            return true;
        }

        // check level is one we know how to handle as more could be added in the future
        // that would need mapping to the WP-CLI:: method.
        $supported    = array(
            Logger::DEBUG,
            Logger::INFO,
            Logger::NOTICE,
            Logger::WARNING,
            Logger::ERROR,
            Logger::CRITICAL,
            Logger::ALERT,
            Logger::EMERGENCY
        );
        $is_supported = in_array($level, $supported, true);

        return $is_supported && parent::isHandling($record);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormatter()
    {
        if ($this->verbose) {
            return new LineFormatter(self::WPCLI_FORMAT_VERBOSE);
        } else {
            return new LineFormatter(self::WPCLI_FORMAT_STANDARD);
        }
    }
}
