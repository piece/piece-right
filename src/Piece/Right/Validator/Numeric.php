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
 * @since      File available since Release 1.3.0
 */

namespace Piece::Right::Validator;
use Piece::Right::Validator::Common;

// {{{ Numeric

/**
 * A validator which is used to check whether a value is a numeric.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.3.0
 */
class Numeric extends Common
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
     */

    // }}}
    // {{{ validate()

    /**
     * Checks whether a value is a numeric.
     *
     * @param string $value
     * @return boolean
     */
    public function validate($value)
    {
        $allowDecimal     = $this->_getRule('allowDecimal');
        $allowOctal       = $this->_getRule('allowOctal');
        $allowHexadecimal = $this->_getRule('allowHexadecimal');

        $allowExponent = $this->_getRule('allowExponent');

        $useInteger = $this->_getRule('useInteger');
        $useFloat = $this->_getRule('useFloat');

        if ($allowDecimal) {
            $useInteger = true;
        }

        if ($allowOctal) {
            $useInteger = true;
        }

        if ($allowHexadecimal) {
            $useInteger = true;
        }

        if ($allowExponent) {
            $useInteger = true;
            $useFloat = true;
        }

        while (true) {
            if ($this->_isDecimal($value)) {
                if (!$useInteger) {
                    return false;
                }

                break;
            }

            if ($this->_isFloat($value)) {
                if (!$useFloat) {
                    return false;
                }

                break;
            }

            if ($this->_isExponent($value)) {
                if (!$allowExponent) {
                    return false;
                }

                break;
            }

            if ($this->_isOctal($value)) {
                if (!$allowOctal) {
                    return false;
                }

                break;
            }

            if ($this->_isHexadecimal($value)) {
                if (!$allowHexadecimal) {
                    return false;
                }

                break;
            }

            return false;
        }

        if (($value + 0.0) != ($value + 0)) {
            $convertedValue = $value + 0.0;
        } else {
            $convertedValue = $value + 0;
        }

        while (true) {
            if (is_int($convertedValue)) {
                if (!$useInteger) {
                    return false;
                }

                break;
            }

            if (is_float($convertedValue)) {
                if (!$useFloat) {
                    return false;
                }

                break;
            }

            break;
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
     */
    protected function _initialize()
    {
        $this->_addRule('allowDecimal', true);
        $this->_addRule('allowOctal', false);
        $this->_addRule('allowHexadecimal', false);
        $this->_addRule('allowExponent', false);
        $this->_addRule('useInteger', true);
        $this->_addRule('useFloat', false);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _isDecimal()

    /**
     * Checks whether a value is a decimal or not.
     *
     * @param mixed $value
     * @return boolean
     */
    private function _isDecimal($value)
    {
        return preg_match('/^[+-]?(?:[1-9][0-9]*|0)$/D', $value);
    }

    // }}}
    // {{{ _isOctal()

    /**
     * Checks whether a value is a octal or not.
     *
     * @param mixed $value
     * @return boolean
     */
    private function _isOctal($value)
    {
        return preg_match('/^[+-]?0[0-7]+$/D', $value);
    }

    // }}}
    // {{{ _isHexadecimal()

    /**
     * Checks whether a value is a hexadecimal or not.
     *
     * @param mixed $value
     * @return boolean
     */
    private function _isHexadecimal($value)
    {
        return preg_match('/^[+-]?0[xX][0-9a-fA-F]+$/D', $value);
    }

    // }}}
    // {{{ _isFloat()

    /**
     * Checks whether a value is a floating point number or not.
     *
     * @param mixed $value
     * @return boolean
     */
    private function _isFloat($value)
    {
        return preg_match('/^[+-]?(?:[0-9]*\.[0-9]+|[0-9]+\.[0-9]*)$/D', $value);
    }

    // }}}
    // {{{ _isExponent()

    /**
     * Checks whether a value is an exponent or not.
     *
     * @param mixed $value
     * @return boolean
     */
    private function _isExponent($value)
    {
        return preg_match('/^(?:[0-9]+|(?:[0-9]*\.[0-9]+|[0-9]+\.[0-9]*))[eE][+-]?[0-9]+$/D', $value);
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
