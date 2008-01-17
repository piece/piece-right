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
 * @since      File available since Release 0.3.0
 */

namespace Piece::Right::Validator;
use Piece::Right::Validator::Common;
use Piece::Right::Exception;

// {{{ WithMethod

/**
 * A validator which is used to validate the value of a field with
 * an arbitrary method.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.3.0
 */
class WithMethod extends Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    protected $_isArrayable = true;

    /**#@-*/

    /**#@+
     * @access private
     */

    private $_class;
    private $_method;
    private $_instance;

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
     * @throws Piece::Right::Exception
     */
    public function validate($value)
    {
        $class = $this->_getRule('class');
        $method = $this->_getRule('method');
        if (is_null($class) || is_null($method)) {
            return false;
        }

        if (!class_exists($class)) {
            throw new Exception("Unknown class $class, be sure the class exists and is loaded prior to use.");
        }

        if ($this->_getRule('isStatic')) {
            $this->_class = $class;
            $this->_method = $method;
            $this->_instance = null;
        } else {
            $instance = new $class();
            $this->_class = null;
            $this->_method = $method;
            $this->_instance = $instance;
        }

        if (!is_array($value)) {
            return $this->_invokeCallback($value);
        }

        foreach ($value as $target) {
            $result = $this->_invokeCallback($target);
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    // }}}
    // {{{ _initialize()

    /**
     * Initializes properties.
     *
     * @since Method available since Release 0.3.0
     */
    protected function _initialize()
    {
        $this->_addRule('class');
        $this->_addRule('method');
        $this->_addRule('isStatic', true);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _invokeCallback()

    /**
     * Invokes a callback and returns the result.
     *
     * @param mixed $value
     * @return boolean
     * @since Method available since Release 2.0.0
     */
    private function _invokeCallback($value)
    {
        if (is_null($this->_instance)) {
            $class = $this->_class;
            $method = $this->_method;
            return $class::$method($value, $this->_payload, $this->_results);
        } else {
            return $this->_instance->{ $this->_method }($value, $this->_payload, $this->_results);
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
