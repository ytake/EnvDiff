<?php

namespace Ytake\EnvDiff;

use ArrayIterator;
use Composer\Script\Event;
use josegonzalez\Dotenv\Loader;

/**
 * Class EnvScript
 */
class EnvScript
{
    /** @var string */
    protected static $filename = '.env';

    /** @var string  */
    protected static $errorMessage = "ENVIRONMENT-NAME:[%s] not found.";

    /**
     * @param \Composer\Script\Event $event
     */
    public static function envDiff(Event $event)
    {
        $basisEnv = self::basisEnv();
        $basis = new ArrayIterator($basisEnv);
        $basis->ksort();
        $holder = [];
        $iterator = [];
        foreach (glob(getcwd() . "/" . self::$filename . '.*') as $filename) {
            $comparison = new ArrayIterator(self::envParse($filename));
            $comparison->ksort();
            $iterator[basename($filename)] = $comparison;
        }
        foreach ($basis as $basisKey => $basisItem) {
            foreach ($iterator as $file => $item) {
                if (!isset($item[$basisKey])) {
                    $holder[$file][] = sprintf(self::$errorMessage, $basisKey);
                }
            }
        }
        foreach ($iterator as $item) {
            foreach ($item as $comparisonKey => $comparisonValue) {
                if (!isset($basis[$comparisonKey])) {
                    $holder[self::$filename][] = sprintf(self::$errorMessage, $comparisonKey);
                }
            }
        }
        $io = $event->getIO();
        if (count($holder)) {
            $solved = array_search('--force', $event->getArguments(), 1);
            if ($solved !== false) {
                return;
            }
            foreach ($holder as $key => $hold) {
                $io->write(sprintf("<error>%s</error>", "[$key]  " . implode(" ", $hold)));
            }
            throw new \RuntimeException('.env error');
        }
    }

    /**
     * @return array
     */
    protected static function basisEnv()
    {
        $filename = self::$filename;
        $files = glob(getcwd() . "/{$filename}");
        if (count($files)) {
            return self::envParse($filename);
        }

        throw new \RuntimeException(getcwd() . "/{$filename} not found.");
    }

    /**
     * @param string $filename
     *
     * @return array
     */
    private static function envParse($filename)
    {
        $Loader = new Loader($filename);

        return $Loader->parse()->toArray();
    }
}
