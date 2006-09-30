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

// {{{ Piece_Right_Validator_Common

/**
 * The base class for Piece_Right validators.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_Validator_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_results;
    var $_rules = array();
    var $_message;
    var $_messages = array();
    var $_isArrayable = false;
    var $_payload;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * Initializes properties.
     *
     * @since Method available since Release 0.3.0
     */
    function Piece_Right_Validator_Common()
    {
        $this->_initialize();
    }

    // }}}
    // {{{ setRules()

    /**
     * Sets the validation rules to the validator.
     *
     * @param array $rules
     */
    function setRules($rules = array())
    {
        foreach ($rules as $rule => $value) {
            $this->setRule($rule, $value);
        }
    }

    // }}}
    // {{{ setRule()

    /**
     * Sets a validation rule to the given rule name.
     *
     * @param string $rule
     * @param mixed  $value
     */
    function setRule($rule, $value)
    {
        if (array_key_exists($rule, $this->_rules)) {
            $this->_rules[$rule] = $value;
            return;
        }

        if (preg_match('/^(.+)_message$/', $rule, $matches)
            && array_key_exists($matches[1], $this->_messages)
            ) {
            $this->setRuleMessage($matches[1], $value);
        }
    }

    // }}}
    // {{{ validate()

    /**
     * Checks whether a value is valid.
     *
     * @param string $value
     * @param mixed  &$payload
     * @return boolean
     * @abstract
     */
    function validate($value, &$payload) {}

    // }}}
    // {{{ clear()

    /**
     * Clears some properties for the next use.
     */
    function clear()
    {
        $this->_message = null;
        $this->_messages = array();
        $this->_initialize();
    }

    // }}}
    // {{{ setResults()

    /**
     * Sets a Piece_Right_Results object.
     *
     * @param Piece_Right_Results $results
     */
    function setResults(&$results)
    {
        $this->_results = &$results;
    }

    // }}}
    // {{{ getRule()

    /**
     * Gets the validation rule of the given rule name.
     *
     * @param string $rule
     * @return mixed
     */
    function getRule($rule)
    {
        return $this->_rules[$rule];
    }

    // }}}
    // {{{ setRuleMessage()

    /**
     * Sets an error message for the given rule name.
     *
     * @param string $rule
     * @param string $message
     */
    function setRuleMessage($rule, $message)
    {
        if (array_key_exists($rule, $this->_messages)) {
            $this->_messages[$rule] = $message;
        }
    }

    // }}}
    // {{{ setMessage()

    /**
     * Sets an error message to the current validation.
     *
     * @param string $message
     */
    function setMessage($message)
    {
        $this->_message = $message;
    }

    // }}}
    // {{{ getMessage()

    /**
     * Gets an error message for the current validation.
     *
     * @return string
     */
    function getMessage()
    {
        return $this->_message;
    }

    // }}}
    // {{{ isArrayable()

    /**
     * Returns whether the validator is arrayable or not.
     *
     * @return boolean
     */
    function isArrayable()
    {
        return $this->_isArrayable;
    }

    // }}}
    // {{{ setPayload()

    /**
     * Sets the given payload.
     *
     * @param mixed &$payload
     * @since Method available since Release 1.1.0
     */
    function setPayload(&$payload)
    {
        $this->_payload = &$payload;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _initialize()

    /**
     * Defines and initializes the validation rules.
     *
     * @since Method available since Release 0.3.0
     */
    function _initialize() {}

    // }}}
    // {{{ _addRule()

    /**
     * Adds the validation rule to the validator, and sets the default value
     * for the rule to the given value.
     *
     * @param string $rule
     * @param mixed $default
     * @param string $message
     */
    function _addRule($rule, $default = null, $message = null)
    {
        $this->_rules[$rule] = $default;
        $this->_messages[$rule] = $message;
    }

    // }}}
    // {{{ _setMessage()

    /**
     * Sets the error message of the given rule name to the current
     * validation.
     *
     * @param string $rule
     */
    function _setMessage($rule)
    {
        if (array_key_exists($rule, $this->_messages)
            && !is_null($this->_messages[$rule])
            && strlen($this->_messages[$rule])
            ) {
            $this->setMessage($this->_messages[$rule]);
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
