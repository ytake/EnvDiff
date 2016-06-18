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

    /**
     * @param Event $event
     */
    public static function postUpdate(Event $event)
    {
        $basisEnv = self::basisEnv();
        $basis = new ArrayIterator($basisEnv);
        $basis->ksort();
        $holder = [];
        $iterator = [];
        foreach (glob(getcwd() . "/" . self::$filename . '.*') as $filename) {

            $comparison = new ArrayIterator(self::envParser($filename));
            $comparison->ksort();
            $iterator[basename($filename)] = $comparison;
        }
        foreach ($basis as $key => $item) {
            foreach ($iterator as $file => $i) {
                if (!isset($i[$key])) {
                    $holder[$file][] = "ENVIRONMENT-NAME:[{$key}] not found.";
                }
            }
        }
        foreach ($iterator as $item) {
            foreach($item as $key => $value) {
                if(!isset($basis[$key])) {
                    $holder[self::$filename][] = "ENVIRONMENT-NAME:[{$key}] not found.";
                }
            }
        }
        $io = $event->getIO();
        if(count($holder)) {
            foreach($holder as $key => $hold) {
                $io->write(sprintf("<error>%s</error>", "[$key]  " . implode(" ", $hold)));
            }
            $solved = array_search('--force', $event->getArguments());
            if($solved !== false) {
                exit;
            }
            throw new \RuntimeException('.env error');
        }
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    protected static function basisEnv()
    {
        $files = glob(getcwd() . "/" . self::$filename);
        if (count($files)) {
            return self::envParser(self::$filename);
        }

        return false;
    }

    /**
     * @param string $filename
     * s@return array
     */
    private static function envParser($filename)
    {
        $Loader = new Loader($filename);

        return $Loader->parse()->toArray();
    }
}
