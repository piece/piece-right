<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006, KUBO Atsuhiro <iteman@users.sourceforge.net>
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
 * @link       http://iteman.typepad.jp/piece/
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Right/ValidationError.php';

// {{{ Piece_Right_Results

/**
 * The validation results which include errors and field values.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://iteman.typepad.jp/piece/
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_Results
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_fieldValues = array();
    var $_errors = array();

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ setFieldValue()

    /**
     * Sets the value of a field.
     *
     * @param string $name
     * @param string $value
     */
    function setFieldValue($name, $value)
    {
        $this->_fieldValues[$name] = $value;
    }

    // }}}
    // {{{ countErrors()

    /**
     * Returns the number of the error fields.
     *
     * @return integer
     */
    function countErrors()
    {
        return count($this->_errors);
    }

    // }}}
    // {{{ addError()

    /**
     * Adds an error to the given field.
     *
     * @param string $field
     * @param string $validator
     * @param string $message
     */
    function addError($field, $validator, $message = null)
    {
        if ($this->isError($field)) {
            $error = &$this->_errors[$field];
        } else {
            $error = &new Piece_Right_ValidationError();
            $this->_errors[$field] = &$error;
        }

        $error->add($validator, $message);
    }

    // }}}
    // {{{ getErrorFields()

    /**
     * Gets an array of the field names which have errors.
     *
     * @return array
     */
    function getErrorFields()
    {
        return array_keys($this->_errors);
    }

    // }}}
    // {{{ isError()

    /**
     * Returns whether the given field has errors or not.
     *
     * @param string $field
     * @return boolean
     */
    function isError($field)
    {
        return array_key_exists($field, $this->_errors);
    }

    // }}}
    // {{{ getErrorMessage()

    /**
     * Gets an error message of the given field.
     *
     * @param string $field
     * @return string
     */
    function getErrorMessage($field)
    {
        if ($this->isError($field)) {
            return $this->_errors[$field]->getMessage();
        }
    }

    // }}}
    // {{{ getErrorMessages()

    /**
     * Gets an array of all error messages for the given field.
     *
     * @param string $field
     * @return array
     */
    function getErrorMessages($field)
    {
        if ($this->isError($field)) {
            return $this->_errors[$field]->getMessages();
        }
    }

    // }}}
    // {{{ getFieldValue()

    /**
     * Gets the value of a field.
     *
     * @param string $name
     * @return string
     * @since Method available since Release 0.2.0
     */
    function getFieldValue($name)
    {
        return $this->_fieldValues[$name];
    }

    // }}}
    // {{{ getFieldNames()

    /**
     * Gets all field names of the current validation as an array.
     *
     * @return array
     * @since Method available since Release 0.3.0
     */
    function getFieldNames()
    {
        return array_keys($this->_fieldValues);
    }

    // }}}
    // {{{ getValidFields()

    /**
     * Gets an array of the field names which have no errors.
     *
     * @return array
     */
    function getValidFields()
    {
        return array_keys(array_diff_assoc($this->_fieldValues, $this->_errors));
    }

    /**#@-*/

    /**#@+
     * @access private
     */

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
