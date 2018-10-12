<?php declare(strict_types=1);

namespace MHCGDev\Monolog\Stubs;

/**
 * Mock of the WP_CLI class for testing.
 *
 * @package MHCGDev\Monolog\Stubs
 */
class MockWPCLI
{
    //<editor-fold desc="Magic Methods">
    public function __call($name, $arguments)
    {
        // Note: value of $name is case sensitive.
        $message = "Mock object method not implemented '$name' "
            . implode(', ', $arguments). "\n";
        throw new \RuntimeException($message);
    }

    public static function __callStatic($name, $arguments)
    {
        // Note: value of $name is case sensitive.
        $message = "Mock static method not implemented '$name' "
            . implode(', ', $arguments). "\n";
        throw new \RuntimeException($message);
    }
    //</editor-fold>

    public static function log($message)
    {
        // do nothing
        return;
    }

    public static function success($message)
    {
        // do nothing
        return;
    }

    public static function debug($message, $group = false)
    {
        // do nothing
        return;
    }

    public static function warning($message)
    {
        // do nothing
        return;
    }

    public static function error($message, $exit = true)
    {
        // taken from class-wp-cli.php file
        $return_code = false;
        if (true === $exit) {
            $return_code = 1;
        } elseif (is_int($exit) && $exit >= 1) {
            $return_code = $exit;
        }

        if ($return_code) {
            throw new MockExitException('', $return_code);
        }
    }
}
