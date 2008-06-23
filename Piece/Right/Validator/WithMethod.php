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
 * @since      File available since Release 0.3.0
 */

require_once 'Piece/Right/Validator/Common.php';
require_once 'Piece/Right/ClassLoader.php';
require_once 'Piece/Right/Error.php';

// {{{ Piece_Right_Validator_WithMethod

/**
 * A validator which is used to validate the value of a field with an
 * arbitrary method.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.3.0
 */
class Piece_Right_Validator_WithMethod extends Piece_Right_Validator_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_isArrayable = true;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ validate()

    /**
     * Validates the value of a field with an arbitrary method.
     *
     * @param mixed $value
     * @return boolean
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     */
    function validate($value)
    {
        $class = $this->_getRule('class');
        $method = $this->_getRule('method');
        $isStatic = $this->_getRule('isStatic');
        if (is_null($class) || is_null($method)) {
            return false;
        }

        if (!Piece_Right_ClassLoader::loaded($class)) {
            Piece_Right_ClassLoader::load($class, $this->_getRule('directory'));
            if (Piece_Right_Error::hasErrors()) {
                return;
            }

            if (!Piece_Right_ClassLoader::loaded($class)) {
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                        "The class [ $class ] not found in the loaded file.",
                                        'exception',
                                        array('validator' => __CLASS__)
                                        );
                return;
            }
        }

        if ($isStatic) {
            $callback = array($class, $method);
        } else {
            $instance = &new $class();
            $callback = array(&$instance, $method);
        }

        if (!is_array($value)) {
            return call_user_func_array($callback,
                                        array($value, &$this->_payload, &$this->_results)
                                        );
        }

        foreach ($value as $target) {
            if (!call_user_func_array($callback,
                                      array($target, &$this->_payload, &$this->_results)
                                      )
                ) {
                return false;
            }
        }

        return true;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _initialize()

    /**
     * Initializes properties.
     *
     * @since Method available since Release 0.3.0
     */
    function _initialize()
    {
        $this->_addRule('class');
        $this->_addRule('method');
        $this->_addRule('isStatic', true);
        $this->_addRule('directory');
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
