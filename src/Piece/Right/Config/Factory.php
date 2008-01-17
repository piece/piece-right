<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 0.1.0
 */

namespace Piece::Right::Config;
use Piece::Right::Config;
use Piece::Right::Exception;
use Piece::Right::Env;

require_once 'Cache/Lite/File.php';
require_once 'spyc.php5';

// {{{ GLOBALS

$GLOBALS['PIECE_RIGHT_Config_Factory_UseUnderscoreAsDirectorySeparator'] = false;

// }}}
// {{{ Factory

/**
 * The configuration reader.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Factory
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     * @static
     */

    // }}}
    // {{{ factory()

    /**
     * Creates a Config object from a configuration file or a cache.
     *
     * @param string $validationSetName
     * @param string $configDirectory
     * @param string $cacheDirectory
     * @param string $templateName
     * @return Piece::Right::Config
     * @throws Piece::Right::Exception
     */
    public static function factory($validationSetName = null,
                                   $configDirectory   = null,
                                   $cacheDirectory    = null,
                                   $templateName      = null
                                   )
    {
        if (is_null($validationSetName) || is_null($configDirectory)) {
            return new Config();
        }

        if (!file_exists($configDirectory)) {
            throw new Exception("The configuration directory [ $configDirectory ] is not found.");
        }

        if ($GLOBALS['PIECE_RIGHT_Config_Factory_UseUnderscoreAsDirectorySeparator']) {
            $validationSetName = str_replace('_', '/', $validationSetName);
        }

        $configFile = "$configDirectory/$validationSetName.yaml";
        if (!file_exists($configFile)) {
            throw new Exception("The configuration file [ $configFile ] is not found.");
        }

        if (!is_readable($configFile)) {
            throw new Exception("The configuration file [ $configFile ] is not readable.");
        }

        while (true) {
            if (!file_exists($cacheDirectory)) {
                throw new Exception("The cache directory [ $cacheDirectory ] is not found.");
            }

            if (!is_readable($cacheDirectory) || !is_writable($cacheDirectory)) {
                throw new Exception("The cache directory [ $cacheDirectory ] is not readable or writable.");
            }

            $config = self::_getConfiguration($configFile, $cacheDirectory);
            break;
        }

        if (is_null($templateName)) {
            return $config;
        }

        $template = self::factory($templateName,
                                  $configDirectory,
                                  $cacheDirectory,
                                  null
                                  );

        foreach ($config->getFieldNames() as $fieldName) {
            if (!$config->hasBasedOn($fieldName)) {
                continue;
            }

            $config->inherit($fieldName,
                             $config->getBasedOn($fieldName),
                             $template
                             );
        }

        return $config;
    }

    // }}}
    // {{{ setUseUnderscoreAsDirectorySeparator()

    /**
     * Sets whether or not the factory uses underscores in validation set
     * names as directory separators.
     *
     * @param boolean $treatUnderscoreAsDirectorySeparator
     */
    public static function setUseUnderscoreAsDirectorySeparator($useUnderscoreAsDirectorySeparator)
    {
        $GLOBALS['PIECE_RIGHT_Config_Factory_UseUnderscoreAsDirectorySeparator'] = $useUnderscoreAsDirectorySeparator;
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     * @static
     */

    // }}}
    // {{{ _getConfiguration()

    /**
     * Gets a Config object from a configuration file or a cache.
     *
     * @param string $masterFile
     * @param string $cacheDirectory
     * @return Piece::Right::Config
     * @throws Piece::Right::Exception
     */
    private static function _getConfiguration($masterFile, $cacheDirectory)
    {
        $masterFile = realpath($masterFile);
        $cache = new ::Cache_Lite_File(array('cacheDir' => "$cacheDirectory/",
                                             'masterFile' => $masterFile,
                                             'automaticSerialization' => true,
                                             'errorHandlingAPIBreak' => true)
                                       );

        if (!Env::isProduction()) {
            $cache->remove($masterFile);
        }

        /*
         * The Cache_Lite class always specifies PEAR_ERROR_RETURN when
         * calling PEAR::raiseError in default.
         */
        $config = $cache->get($masterFile);
        if (::PEAR::isError($config)) {
            throw new Exception("Cannot read the cache file in the directory [ $cacheDirectory ].");
        }

        if (!$config) {
            $config = self::_getConfigurationFromFile($masterFile);
            $result = $cache->save($config);
            if (::PEAR::isError($result)) {
                throw new Exception("Cannot write the Config object to the cache file in the directory [ $cacheDirectory ].");
            }
        }

        return $config;
    }

    // }}}
    // {{{ _getConfigurationFromFile()

    /**
     * Parses the given file and returns a Config object.
     *
     * @param string $file
     * @return Piece::Right::Config
     * @throws Piece::Right::Exception
     */
    private static function _getConfigurationFromFile($file)
    {
        $config = new Config();
        $yaml = ::Spyc::YAMLLoad($file);

        foreach ($yaml as $validation) {
            if (!array_key_exists('name', $validation)) {
                throw new Exception("The \"name\" element is required in the validation definition file [ $file ].");
            }

            $config->addField($validation['name']);

            if (array_key_exists('required', $validation)) {
                $config->setRequired($validation['name'], (array)$validation['required']);
            }

            if (array_key_exists('filter', $validation)) {
                foreach ((array)$validation['filter'] as $filter) {
                    $config->addFilter($validation['name'], $filter);
                }
            }

            if (array_key_exists('validator', $validation)) {
                foreach ((array)$validation['validator'] as $validator) {
                    $config->addValidation($validation['name'],
                                           $validator['name'],
                                           (array)@$validator['rule'],
                                           @$validator['message']
                                           );
                }
            }

            if (array_key_exists('watcher', $validation)) {
                $config->setWatcher($validation['name'], (array)$validation['watcher']);
            }

            if (array_key_exists('pseudo', $validation)) {
                $config->setPseudo($validation['name'], (array)$validation['pseudo']);
            }

            if (array_key_exists('description', $validation)) {
                $config->setDescription($validation['name'],
                                        $validation['description']
                                        );
            }

            if (array_key_exists('forceValidation', $validation)) {
                $config->setForceValidation($validation['name'],
                                            $validation['forceValidation']
                                            );
            }

            if (array_key_exists('finals', $validation)) {
                foreach ((array)$validation['finals'] as $validator) {
                    $config->addValidation($validation['name'],
                                           $validator['name'],
                                           (array)@$validator['rule'],
                                           @$validator['message'],
                                           true
                                           );
                }
            }

            if (array_key_exists('basedOn', $validation)) {
                $config->setBasedOn($validation['name'], $validation['basedOn']);
            }
        }

        return $config;
    }

    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
?>
