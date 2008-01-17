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

namespace Piece::Right::Validator;

// {{{ Common

/**
 * The base class for Piece_Right validators.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
abstract class Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    protected $_results;
    protected $_isArrayable = false;
    protected $_payload;

    /**#@-*/

    /**#@+
     * @access private
     */

    private $_rules = array();
    private $_message;
    private $_messages = array();

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ __construct()

    /**
     * Initializes properties.
     *
     * @since Method available since Release 0.3.0
     */
    public function __construct()
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
    public function setRules($rules = array())
    {
        foreach ($rules as $rule => $value) {
            $this->_setRule($rule, $value);
        }
    }

    // }}}
    // {{{ clear()

    /**
     * Clears some properties for the next use.
     */
    public function clear()
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
    public function setResults($results)
    {
        $this->_results = $results;
    }

    // }}}
    // {{{ setMessage()

    /**
     * Sets an error message to the current validation.
     *
     * @param string $message
     */
    public function setMessage($message)
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
    public function getMessage()
    {
        return $this->_replaceMessage($this->_message);
    }

    // }}}
    // {{{ isArrayable()

    /**
     * Returns whether the validator is arrayable or not.
     *
     * @return boolean
     */
    public function isArrayable()
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
    public function setPayload(&$payload)
    {
        $this->_payload = &$payload;
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    // }}}
    // {{{ validate()

    /**
     * Checks whether a value is valid.
     *
     * @param mixed $value
     * @return boolean
     * @abstract
     */
    abstract protected function validate($value);

    // }}}
    // {{{ _initialize()

    /**
     * Defines and initializes the validation rules.
     *
     * @since Method available since Release 0.3.0
     */
    protected function _initialize() {}

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
    protected function _addRule($rule, $default = null, $message = null)
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
    protected function _setMessage($rule)
    {
        if (array_key_exists($rule, $this->_messages)
            && !is_null($this->_messages[$rule])
            && strlen($this->_messages[$rule])
            ) {
            $this->setMessage($this->_messages[$rule]);
        }
    }

    // }}}
    // {{{ _getRule()

    /**
     * Gets the validation rule of the given rule name.
     *
     * @param string $rule
     * @return mixed
     * @since Method available since Release 1.6.0
     */
    protected function _getRule($rule)
    {
        return $this->_rules[$rule];
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _replaceMessage()

    /**
     * Replaces the message with each rule value.
     *
     * @param string $message
     * @return string
     * @since Method available since Release 1.3.0
     */
    private function _replaceMessage($message)
    {
        foreach ($this->_rules as $name => $value) {
            if (!is_array($value)) {
                $message = str_replace("%$name%", $value, $message);
            }
        }

        return $message;
    }

    // }}}
    // {{{ _setRule()

    /**
     * Sets a validation rule to the given rule name.
     *
     * @param string $rule
     * @param mixed  $value
     * @since Method available since Release 1.6.0
     */
    private function _setRule($rule, $value)
    {
        if (array_key_exists($rule, $this->_rules)) {
            $this->_rules[$rule] = $value;
            return;
        }

        if (preg_match('/^(.+)_message$/', $rule, $matches)
            && array_key_exists($matches[1], $this->_messages)
            ) {
            $this->_setRuleMessage($matches[1], $value);
        }
    }

    // }}}
    // {{{ _setRuleMessage()

    /**
     * Sets an error message for the given rule name.
     *
     * @param string $rule
     * @param string $message
     * @since Method available since Release 1.6.0
     */
    private function _setRuleMessage($rule, $message)
    {
        if (array_key_exists($rule, $this->_messages)) {
            $this->_messages[$rule] = $message;
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
