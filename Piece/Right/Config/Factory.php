<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
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

require_once 'Piece/Right/Config.php';
require_once 'Piece/Right/Error.php';
require_once 'Cache/Lite/File.php';
require_once 'PEAR.php';

if (version_compare(phpversion(), '5.0.0', '<')) {
    require_once 'spyc.php';
} else {
    require_once 'spyc.php5';
}

require_once 'Piece/Right/Env.php';

// {{{ GLOBALS

$GLOBALS['PIECE_RIGHT_Config_Factory_UseUnderscoreAsDirectorySeparator'] = false;

// }}}
// {{{ Piece_Right_Config_Factory

/**
 * A factory class for creating Piece_Right_Config objects.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_Config_Factory
{

    // {{{ properties

    /**#@+
     * @access public
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
     * Creates a Piece_Right_Config object from a configuration file or a
     * cache.
     *
     * @param string $validationSetName
     * @param string $configDirectory
     * @param string $cacheDirectory
     * @param string $TemplateName
     * @return Piece_Right_Config
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @throws PIECE_RIGHT_ERROR_NOT_READABLE
     */
    function &factory($validationSetName = null,
                      $configDirectory   = null,
                      $cacheDirectory    = null,
                      $templateName      = null
                      )
    {
        if (is_null($validationSetName) || is_null($configDirectory)) {
            $config = &new Piece_Right_Config();
            return $config;
        }

        if ($GLOBALS['PIECE_RIGHT_Config_Factory_UseUnderscoreAsDirectorySeparator']) {
            $validationSetName = str_replace('_', '/', $validationSetName);
        }

        $configFile = "$configDirectory/$validationSetName.yaml";

        if (!file_exists($configFile)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The configuration file [ $configFile ] is not found."
                                    );
            $return = null;
            return $return;
        }

        if (!is_readable($configFile)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_READABLE,
                                    "The configuration file [ $configFile ] is not readable."
                                    );
            $return = null;
            return $return;
        }

        while (true) {
            if (is_null($cacheDirectory)) {
                $config = &Piece_Right_Config_Factory::_getConfigurationFromFile($configFile);
                break;
            }

            if (!file_exists($cacheDirectory)) {
                trigger_error("The cache directory [ $cacheDirectory ] is not found.",
                              E_USER_WARNING
                              );
                $config = &Piece_Right_Config_Factory::_getConfigurationFromFile($configFile);
                break;
            }

            if (!is_readable($cacheDirectory) || !is_writable($cacheDirectory)) {
                trigger_error("The cache directory [ $cacheDirectory ] is not readable or writable.",
                              E_USER_WARNING
                              );
                $config = &Piece_Right_Config_Factory::_getConfigurationFromFile($configFile);
                break;
            }

            $config = &Piece_Right_Config_Factory::_getConfiguration($configFile, $cacheDirectory);
            break;
        }

        if (Piece_Right_Error::hasErrors()) {
            $return = null;
            return $return;
        }

        if (is_null($templateName)) {
            return $config;
        }

        $template = &Piece_Right_Config_Factory::factory($templateName,
                                                         $configDirectory,
                                                         $cacheDirectory,
                                                         null
                                                         );
        if (Piece_Right_Error::hasErrors()) {
            $return = null;
            return $return;
        }

        foreach ($config->getFieldNames() as $fieldName) {
            if (!$config->hasBasedOn($fieldName)) {
                continue;
            }

            $basedOn = $config->getBasedOn($fieldName);
            $config->inherit($fieldName, $basedOn, $template);
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
    function setUseUnderscoreAsDirectorySeparator($useUnderscoreAsDirectorySeparator)
    {
        $GLOBALS['PIECE_RIGHT_Config_Factory_UseUnderscoreAsDirectorySeparator'] = $useUnderscoreAsDirectorySeparator;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _getConfiguration()

    /**
     * Gets a Piece_Right_Config object from a configuration file or a cache.
     *
     * @param string $masterFile
     * @param string $cacheDirectory
     * @return Piece_Right_Config
     */
    function &_getConfiguration($masterFile, $cacheDirectory)
    {
        $masterFile = realpath($masterFile);
        $cache = &new Cache_Lite_File(array('cacheDir' => "$cacheDirectory/",
                                            'masterFile' => $masterFile,
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );

        if (!Piece_Right_Env::isProduction()) {
            $cache->remove($masterFile);
        }

        /*
         * The Cache_Lite class always specifies PEAR_ERROR_RETURN when
         * calling PEAR::raiseError in default.
         */
        $config = $cache->get($masterFile);
        if (PEAR::isError($config)) {
            trigger_error("Cannot read the cache file in the directory [ $cacheDirectory ].",
                          E_USER_WARNING
                          );
            return Piece_Right_Config_Factory::_getConfigurationFromFile($masterFile);
        }

        if (!$config) {
            $config = &Piece_Right_Config_Factory::_getConfigurationFromFile($masterFile);
            if (Piece_Right_Error::hasErrors()) {
                $return = null;
                return $return;
            }

            $result = $cache->save($config);
            if (PEAR::isError($result)) {
                trigger_error("Cannot write the Piece_Right_Config object to the cache file in the directory [ $cacheDirectory ].",
                              E_USER_WARNING
                              );
            }
        }

        return $config;
    }

    // }}}
    // {{{ _getConfigurationFromFile()

    /**
     * Parses the given file and returns a Piece_Right_Config object.
     *
     * @param string $file
     * @return Piece_Right_Config
     * @throws PIECE_RIGHT_ERROR_INVALID_CONFIGURATION
     */
    function &_getConfigurationFromFile($file)
    {
        $config = &new Piece_Right_Config();
        $yaml = Spyc::YAMLLoad($file);
        foreach ($yaml as $validation) {
            if (!array_key_exists('name', $validation)) {
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_INVALID_CONFIGURATION,
                                        "A configuration in the configuration file [ $file ] has no 'name' element."
                                        );
                $return = null;
                return $return;
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
