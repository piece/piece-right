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

// {{{ Piece_Right_Config

/**
 * The configuration container for Piece_Right validation sets.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://iteman.typepad.jp/piece/
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_Config
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_requiredFields = array();
    var $_validationSet = array();

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ addValidation()

    /**
     * Adds a validation to a field with the given rules.
     *
     * @param string $field
     * @param string $validator
     * @param array  $rules
     * @param string $message
     */
    function addValidation($field, $validator, $rules = array(),
                           $message = null
                           )
    {
        if (!array_key_exists($field, $this->_validationSet)) {
            $this->_validationSet[$field] = array();
        }

        array_push($this->_validationSet[$field],
                   array('validator' => $validator,
                         'rules'     => $rules,
                         'message'   => $message)
                   );
    }

    // }}}
    // {{{ getValidationSet()

    /**
     * Gets the validation set as an array.
     *
     * @return array
     */
    function getValidationSet()
    {
        return $this->_validationSet;
    }

    // }}}
    // {{{ merge()

    /**
     * Merges the given configuretion into the existing configuration.
     *
     * @param Piece_Right_Config &$config
     */
    function merge(&$config)
    {
        $validationSet = $config->getValidationSet();
        array_walk($validationSet, array(&$this, 'mergeValidations'));
    }

    // }}}
    // {{{ mergeExtensions()

    /**
     * A callback that will be called by array_walk() function in merge()
     * method.
     *
     * @param string $validations
     * @param string $field
     */
    function mergeValidations($validations, $field)
    {
        foreach ($validations as $validation) {
            $this->addValidation($field, $validation['validator'], $validation['rules']);
        }
    }

    // }}}
    // {{{ setRequired()

    /**
     * Sets a field as required.
     *
     * @param string $field
     * @param string $message
     * @since Method available since Release 0.3.0
     */
    function setRequired($field, $message = null)
    {
        if (!array_key_exists($field, $this->_validationSet)) {
            $this->_validationSet[$field] = array();
        }

        $this->_requiredFields[$field]['message'] = $message;
    }

    // }}}
    // {{{ isRequired()

    /**
     * Returns whether the given field is required or not.
     *
     * @return boolean
     * @since Method available since Release 0.3.0
     */
    function isRequired($field)
    {
        return array_key_exists($field, $this->_requiredFields);
    }

    // }}}
    // {{{ getRequiredMessage()

    /**
     * Gets the message when a field is required.
     *
     * @param string $field
     * @return string
     * @since Method available since Release 0.3.0
     */
    function getRequiredMessage($field)
    {
        return $this->_requiredFields[$field]['message'];
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
