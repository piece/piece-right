<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://piece-framework.com/piece-right/
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Right/Error.php';

// {{{ GLOBALS

$GLOBALS['PIECE_RIGHT_Validator_Instances'] = array();
$GLOBALS['PIECE_RIGHT_Validator_Directories'] = array(dirname(__FILE__) . '/../../..');

// }}}
// {{{ Piece_Right_Validator_Factory

/**
 * A factory class for creating validator objects.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
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
     * @param string $validator
     * @return mixed
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @throws PIECE_RIGHT_ERROR_INVALID_VALIDATOR
     */
    function &factory($validator)
    {
        $validator = "Piece_Right_Validator_$validator";
        if (!array_key_exists($validator, $GLOBALS['PIECE_RIGHT_Validator_Instances'])) {
            $found = false;
            foreach ($GLOBALS['PIECE_RIGHT_Validator_Directories'] as $validatorDirectory) {
                $found = Piece_Right_Validator_Factory::_load($validator, $validatorDirectory);
                if ($found) {
                    break;
                }
            }

            if (!$found) {
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                        "The validator [ $validator ] not found in the following directories:\n" .
                                        implode("\n", $GLOBALS['PIECE_RIGHT_Validator_Directories'])
                                        );
                $return = null;
                return $return;
            }

            $instance = &new $validator();
            if (!is_a($instance, 'Piece_Right_Validator_Common')) {
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_INVALID_VALIDATOR,
                                        "The validator [ $validator ] is invalid."
                                        );
                $return = null;
                return $return;
            }

            $GLOBALS['PIECE_RIGHT_Validator_Instances'][$validator] = &$instance;
        } else {
            $GLOBALS['PIECE_RIGHT_Validator_Instances'][$validator]->clear();
        }

        return $GLOBALS['PIECE_RIGHT_Validator_Instances'][$validator];
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

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _load()

    /**
     * Loads a validator corresponding to the given validator name.
     *
     * @param string $validator
     * @param string $validatorDirectory
     * @return boolean
     * @static
     */
    function _load($validator, $validatorDirectory)
    {
        $file = "$validatorDirectory/" . str_replace('_', '/', $validator) . '.php';

        if (!file_exists($file)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The validator file [ $file ] for the class [ $validator ] not found.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();
            return false;
        }

        if (!is_readable($file)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_READABLE,
                                    "The validator file [ $file ] was not readable.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();
            return false;
        }

        if (!include_once $file) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The validator file [ $file ] not found or was not readable.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();
            return false;
        }

        if (version_compare(phpversion(), '5.0.0', '<')) {
            $found = class_exists($validator);
        } else {
            $found = class_exists($validator, false);
        }

        return $found;
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
