<?php

namespace ride\library\api\io;

use Composer\Autoload\ClassLoader;

use ride\library\Autoloader;

/**
 * Autoloader implementation to retrieve the include paths for the API browser
 */
class AutoloaderApiIO implements ApiIO {

    /**
     * Gets the include paths for the API browser
     * @return array Array with include paths of files and/or directories
     */
    public function getIncludePaths() {
        $includePaths = array();

        $functions = spl_autoload_functions();
        foreach ($functions as $function) {
            if (!is_array($function)) {
                // not supported
                continue;
            }

            if ($function[0] instanceof ClassLoader) {
                // composer autoloader
                $classMap = $function[0]->getClassMap();
                foreach ($classMap as $path) {
                    if ($path) {
                        $includePaths[$path] = true;
                    }
                }

                $prefixes = $function[0]->getPrefixes();
                foreach($prefixes as $namespaces) {
                    foreach ($namespaces as $path) {
                        $includePaths[$path] = true;
                    }
                }
            } elseif ($function[0] instanceof Autoloader) {
                // ride autoloader
                $phpIncludePaths = explode(PATH_SEPARATOR, get_include_path());

                $paths = $function[0]->getIncludePaths();
                foreach ($paths as $path) {
                    if (in_array($path, $phpIncludePaths)) {
                        continue;
                    }

                    $includePaths[$path] = true;
                }
            }
        }

        return array_keys($includePaths);
    }

}