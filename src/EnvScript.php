<?php

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * Copyright (c) 2014-2016 Yuuki Takezawa
 */
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
