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

namespace Piece::Right;
use Piece::Right::Validation::Error;

// {{{ Results

/**
 * The validation results which include errors and field values.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Results
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

    private $_fieldValues = array();
    private $_errors = array();
    private $_messageVariables = array();

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
    public function setFieldValue($name, $value)
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
    public function countErrors()
    {
        return count($this->_errors);
    }

    // }}}
    // {{{ addError()

    /**
     * Adds an error to the given field.
     *
     * @param string $fieldName
     * @param string $validator
     * @param string $message
     */
    public function addError($fieldName, $validator, $message = null)
    {
        if ($this->isError($fieldName)) {
            $error = $this->_errors[$fieldName];
        } else {
            $error = new Error();
            $this->_errors[$fieldName] = $error;
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
    public function getErrorFields()
    {
        return array_keys($this->_errors);
    }

    // }}}
    // {{{ isError()

    /**
     * Returns whether the given field has errors or not.
     *
     * @param string $fieldName
     * @return boolean
     */
    public function isError($fieldName)
    {
        return array_key_exists($fieldName, $this->_errors);
    }

    // }}}
    // {{{ getErrorMessage()

    /**
     * Gets an error message of the given field.
     *
     * @param string $fieldName
     * @return string
     */
    public function getErrorMessage($fieldName)
    {
        if ($this->isError($fieldName)) {
            return $this->_replaceMessage($fieldName,
                                          $this->_errors[$fieldName]->getMessage()
                                          );
        }
    }

    // }}}
    // {{{ getErrorMessages()

    /**
     * Gets an array of all error messages for the given field.
     *
     * @param string $fieldName
     * @return array
     */
    public function getErrorMessages($fieldName)
    {
        if ($this->isError($fieldName)) {
            return $this->_errors[$fieldName]->getMessages();
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
    public function getFieldValue($name)
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
    public function getFieldNames()
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
    public function getValidFields()
    {
        return array_diff(array_keys($this->_fieldValues), array_keys($this->_errors));
    }

    // }}}
    // {{{ setMessageVariables()

    /**
     * Sets the message variables of the current validation.
     *
     * @param array $messageVariables
     */
    public function setMessageVariables($messageVariables)
    {
        $this->_messageVariables = $messageVariables;
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _replaceMessage()

    /**
     * Replaces the message with the message variables of the given field.
     *
     * @param string $fieldName
     * @param string $message
     * @return string
     * @since Method available since Release 1.0.0
     */
    private function _replaceMessage($fieldName, $message)
    {
        foreach ($this->_messageVariables[$fieldName] as $name => $value) {
            $message = str_replace("%$name%", $value, $message);
        }

        return $message;
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
