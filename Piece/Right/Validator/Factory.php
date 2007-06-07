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
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Right/Error.php';
require_once 'Piece/Right/ClassLoader.php';

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
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
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
     * @throws PIECE_RIGHT_ERROR_CANNOT_READ
     */
    function &factory($validatorName)
    {
        if (!array_key_exists($validatorName, $GLOBALS['PIECE_RIGHT_Validator_Instances'])) {
            $found = false;
            foreach ($GLOBALS['PIECE_RIGHT_Validator_Prefixes'] as $prefixAlias) {
                $validatorClass = Piece_Right_Validator_Factory::_getValidatorClass($validatorName, $prefixAlias);
                if (Piece_Right_ClassLoader::loaded($validatorClass)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                foreach ($GLOBALS['PIECE_RIGHT_Validator_Directories'] as $validatorDirectory) {
                    foreach ($GLOBALS['PIECE_RIGHT_Validator_Prefixes'] as $prefixAlias) {
                        $validatorClass = Piece_Right_Validator_Factory::_getValidatorClass($validatorName, $prefixAlias);

                        Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
                        Piece_Right_ClassLoader::load($validatorClass, $validatorDirectory);
                        Piece_Right_Error::popCallback();

                        if (Piece_Right_Error::hasErrors('exception')) {
                            $error = Piece_Right_Error::pop();
                            if ($error['code'] == PIECE_RIGHT_ERROR_NOT_FOUND) {
                                continue;
                            }

                            Piece_Right_Error::push(PIECE_RIGHT_ERROR_CANNOT_READ,
                                                    "Failed to read the validator [ $validatorName ] for any reasons.",
                                                    'exception',
                                                    array(),
                                                    $error
                                                    );
                            $return = null;
                            return $return;
                        }

                        if (Piece_Right_ClassLoader::loaded($validatorClass)) {
                            $found = true;
                            break 2;
                        }
                    }
                }

                if (!$found) {
                    Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                            "The validator [ $validatorName ] not found in the following directories:\n" .
                                            implode("\n", $GLOBALS['PIECE_RIGHT_Validator_Directories'])
                                            );
                    $return = null;
                    return $return;
                }
            }

            $validator = &new $validatorClass($prefixAlias);
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
