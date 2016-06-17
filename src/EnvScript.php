<?php

namespace Istyle\EnvDiff;

use Composer\Script\Event;
use josegonzalez\Dotenv\Loader;

/**
 * Class EnvScript
 */
class EnvScript
{
    /**
     * @param Event $event
     */
    public static function postUpdate(Event $event)
    {
        $basisEnv = self::basisEnv();
        var_dump($basisEnv);
        $holder = [];
        foreach (glob(getcwd() . '/{,.}env**', GLOB_BRACE + GLOB_NOSORT) as $filename) {
            $Loader = new Loader($filename);
            $holder[] = $Loader->parse()->toArray();
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected static function basisEnv()
    {
        $files = glob(getcwd() . '/{,.}env');
        if (count($files)) {
            return $files;
        }

        throw new \Exception;
    }
}
