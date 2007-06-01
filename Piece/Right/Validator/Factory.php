<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://piece-framework.com/piece-right/
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Right/Error.php';

// {{{ GLOBALS

$GLOBALS['PIECE_RIGHT_Validator_Instances'] = array();
$GLOBALS['PIECE_RIGHT_Validator_Directories'] = array(dirname(__FILE__) . '/../../..');
$GLOBALS['PIECE_RIGHT_Validator_Prefixes'] = array('Piece_Right_Validator');

// }}}
// {{{ Piece_Right_Validator_Factory

/**
 * A factory class for creating validator objects.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_Validator_Factory
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
     * Creates a validator object from the validator directories.
     *
     * @param string $validatorName
     * @return mixed
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @throws PIECE_RIGHT_ERROR_INVALID_VALIDATOR
     */
    function &factory($validatorName)
    {
        if (!array_key_exists($validatorName, $GLOBALS['PIECE_RIGHT_Validator_Instances'])) {
            $validatorClass = Piece_Right_Validator_Factory::_findValidatorClass($validatorName);
            if (is_null($validatorClass)) {
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                        "The validator [ $validatorName ] not found in the following directories:\n" .
                                        implode("\n", $GLOBALS['PIECE_RIGHT_Validator_Directories'])
                                        );
                $return = null;
                return $return;
            }

            $validator = &new $validatorClass();
            if (!is_subclass_of($validator, 'Piece_Right_Validator_Common')) {
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_INVALID_VALIDATOR,
                                        "The validator [ $validatorName ] is invalid."
                                        );
                $return = null;
                return $return;
            }

            $GLOBALS['PIECE_RIGHT_Validator_Instances'][$validatorName] = &$validator;
        } else {
            $GLOBALS['PIECE_RIGHT_Validator_Instances'][$validatorName]->clear();
        }

        return $GLOBALS['PIECE_RIGHT_Validator_Instances'][$validatorName];
    }

    // }}}
    // {{{ addValidatorDirectory()

    /**
     * Adds a validator directory.
     *
     * @param string $validatorDirectory
     */
    function addValidatorDirectory($validatorDirectory)
    {
        array_unshift($GLOBALS['PIECE_RIGHT_Validator_Directories'], $validatorDirectory);
    }

    // }}}
    // {{{ clearInstances()

    /**
     * Clears the validator instances.
     */
    function clearInstances()
    {
        $GLOBALS['PIECE_RIGHT_Validator_Instances'] = array();
    }

    // }}}
    // {{{ addValidatorPrefix()

    /**
     * Adds a prefix for a validator.
     *
     * @param string $validatorPrefix
     */
    function addValidatorPrefix($validatorPrefix)
    {
        array_unshift($GLOBALS['PIECE_RIGHT_Validator_Prefixes'], $validatorPrefix);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _loadFromDirectory()

    /**
     * Loads a validator from the given directory.
     *
     * @param string $validatorClass
     * @param string $validatorDirectory
     * @return boolean
     * @static
     */
    function _loadFromDirectory($validatorClass, $validatorDirectory)
    {
        $file = "$validatorDirectory/" . str_replace('_', '/', $validatorClass) . '.php';

        if (!file_exists($file)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The validator file [ $file ] for the class [ $validatorClass ] not found.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();
            return false;
        }

        if (!is_readable($file)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_READABLE,
                                    "The validator file [ $file ] is not readable.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();
            return false;
        }

        if (!include_once $file) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The validator file [ $file ] not found or is not readable.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();
            return false;
        }

        return Piece_Right_Validator_Factory::_loaded($validatorClass);
    }

    // }}}
    // {{{ _loaded()

    /**
     * Returns whether the given validator has already been loaded or not.
     *
     * @param string $validatorClass
     * @return boolean
     * @static
     */
    function _loaded($validatorClass)
    {
        if (version_compare(phpversion(), '5.0.0', '<')) {
            return class_exists($validatorClass);
        } else {
            return class_exists($validatorClass, false);
        }
    }

    // }}}
    // {{{ _findValidatorClass()

    /**
     * Finds a validator class from the validator directories and the prefixes.
     *
     * @param string $validatorName
     * @return string
     */
    function _findValidatorClass($validatorName)
    {
        foreach ($GLOBALS['PIECE_RIGHT_Validator_Prefixes'] as $prefixAlias) {
            $validatorClass = Piece_Right_Validator_Factory::_getValidatorClass($validatorName, $prefixAlias);
            if (Piece_Right_Validator_Factory::_loaded($validatorClass)) {
                return $validatorClass;
            }
        }

        foreach ($GLOBALS['PIECE_RIGHT_Validator_Directories'] as $validatorDirectory) {
            foreach ($GLOBALS['PIECE_RIGHT_Validator_Prefixes'] as $prefixAlias) {
                $validatorClass = Piece_Right_Validator_Factory::_getValidatorClass($validatorName, $prefixAlias);
                if (Piece_Right_Validator_Factory::_loadFromDirectory($validatorClass, $validatorDirectory)) {
                    return $validatorClass;
                }
            }
        }
    }

    // }}}
    // {{{ _getValidatorClass()

    /**
     * Gets the class name for a given validator name with a prefix alias.
     *
     * @param string $validatorName
     * @param string $prefixAlias
     * @return string
     */
    function _getValidatorClass($validatorName, $prefixAlias)
    {
        if ($prefixAlias) {
            return "{$prefixAlias}_{$validatorName}";
        } else {
            return $validatorName;
        }
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
