<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 1.8.0
 */

// {{{ Piece_Right_Config_Field

/**
 * A class representing configuration of a field.
 *
 * @package    Piece_Right
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.8.0
 */
class Piece_Right_Config_Field
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_required = array('enabled' => null, 'message' => null);
    var $_filters = array();
    var $_validations = array();
    var $_watcher;
    var $_pseudo;
    var $_messageVariables = array();
    var $_forceValidation;
    var $_basedOn;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ setRequired()

    /**
     * Sets a required definition.
     *
     * @param array $required
     */
    function setRequired($required)
    {
        if (array_key_exists('enabled', $required)) {
            if (!is_null($required['enabled'])) {
                $this->_required['enabled'] = $required['enabled'];
            }
        } else {
            $this->_required['enabled'] = true;
        }

        if (array_key_exists('message', $required)) {
            if (!is_null($required['message'])) {
                $this->_required['message'] = $required['message'];
            }
        }
    }

    // }}}
    // {{{ getRequired()

    /**
     * Sets the required definition.
     *
     * @return array
     */
    function getRequired()
    {
        return $this->_required;
    }

    // }}}
    // {{{ isRequired()

    /**
     * Checks whether this field is required or not.
     *
     * @return boolean
     */
    function isRequired()
    {
        if (is_null($this->_required)) {
            return false;
        }

        return $this->_required['enabled'];
    }

    // }}}
    // {{{ getRequiredMessage()

    /**
     * Gets the required message.
     *
     * @return string
     */
    function getRequiredMessage()
    {
        return $this->_required['message'];
    }

    // }}}
    // {{{ merge()

    /**
     * Merges a given configuretion into the this configuration.
     *
     * @param Piece_Right_Config_Field &$field
     */
    function merge(&$field)
    {
        $required = $field->getRequired();
        if (!is_null($required)) {
            $this->setRequired($required);
        }

        $filters = $field->getFilters();
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        $validations = $field->getValidations();
        foreach ($validations as $validation) {
            $this->addValidation($validation['validator'],
                                 $validation['rules'],
                                 $validation['message'],
                                 $validation['useInFinals']
                                 );
        }

        $watcher = $field->getWatcher();
        if (!is_null($watcher)) {
            $this->setWatcher($watcher);
        }

        $pseudo = $field->getPseudo();
        if (!is_null($pseudo)) {
            $this->setPseudo($pseudo);
        }

        $messageVariables = $field->getMessageVariables();
        foreach ($messageVariables as $variableName => $value) {
            $this->addMessageVariable($variableName, $value);
        }

        $forceValidation = $field->forceValidation();
        if (!is_null($forceValidation)) {
            $this->setForceValidation($forceValidation);
        }

        $basedOn = $field->getBasedOn();
        if (!is_null($basedOn)) {
            $this->setBasedOn($basedOn);
        }
    }

    // }}}
    // {{{ addFilter()

    /**
     * Adds a filter.
     *
     * @param string $filterName
     */
    function addFilter($filterName)
    {
        $this->_filters[] = $filterName;
    }

    // }}}
    // {{{ getFilters()

    /**
     * Gets the filters.
     *
     * @return array
     */
    function getFilters()
    {
        return $this->_filters;
    }

    // }}}
    // {{{ addValidation()

    /**
     * Adds a validation with a given rules.
     *
     * @param string  $validatorName
     * @param array   $rules
     * @param string  $message
     * @param boolean $useInFinals
     */
    function addValidation($validatorName, $rules, $message, $useInFinals)
    {
        $this->_validations[] = array('validator'   => $validatorName,
                                      'rules'       => $rules,
                                      'message'     => $message,
                                      'useInFinals' => $useInFinals
                                      );
    }

    // }}}
    // {{{ getValidations()

    /**
     * Gets the validations.
     *
     * @return array
     */
    function getValidations()
    {
        return $this->_validations;
    }

    // }}}
    // {{{ setWatcher()

    /**
     * Sets a watcher definition.
     *
     * @param array $watcher
     */
    function setWatcher($watcher)
    {
        $this->_watcher = $watcher;
    }

    // }}}
    // {{{ getWatcher()

    /**
     * Gets the watcher definition.
     *
     * @return array
     */
    function getWatcher()
    {
        return $this->_watcher;
    }

    // }}}
    // {{{ setPseudo()

    /**
     * Sets a pseudo definition.
     *
     * @param array $pseudo
     */
    function setPseudo($pseudo)
    {
        $this->_pseudo = $pseudo;
    }

    // }}}
    // {{{ getPseudo()

    /**
     * Gets the pseudo definition.
     *
     * @return array
     */
    function getPseudo()
    {
        return $this->_pseudo;
    }

    // }}}
    // {{{ isPseudo()

    /**
     * Checks whether this field is pseudo or not.
     *
     * @return boolean
     */
    function isPseudo()
    {
        if (is_null($this->_pseudo)) {
            return false;
        }

        return true;
    }

    // }}}
    // {{{ hasMessageVariable()

    /**
     * Checks whether or not this field has the message variable for a given
     * variable name.
     *
     * @param string $variableName
     * @return boolean
     */
    function hasMessageVariable($variableName)
    {
        return array_key_exists($variableName, $this->_messageVariables);
    }

    // }}}
    // {{{ addMessageVariable()

    /**
     * Adds a message variable.
     *
     * @param string $variableName
     * @param string $value
     */
    function addMessageVariable($variableName, $value)
    {
        $this->_messageVariables[$variableName] = $value;
    }

    // }}}
    // {{{ getMessageVariables()

    /**
     * Gets the message variables.
     *
     * @return array
     */
    function getMessageVariables()
    {
        return $this->_messageVariables;
    }

    // }}}
    // {{{ setForceValidation()

    /**
     * Turns force validation on/off.
     *
     * @param boolean $forceValidation
     */
    function setForceValidation($forceValidation)
    {
        $this->_forceValidation = $forceValidation;
    }

    // }}}
    // {{{ forceValidation()

    /**
     * Checks whether or not forcing invocation of all validations.
     *
     * @return boolean
     */
    function forceValidation()
    {
        if (is_null($this->_forceValidation)) {
            return false;
        }

        return $this->_forceValidation;
    }

    // }}}
    // {{{ setBasedOn()

    /**
     * Sets the field which this field based on.
     *
     * @param string $basedOn
     */
    function setBasedOn($basedOn)
    {
        $this->_basedOn = $basedOn;
    }

    // }}}
    // {{{ getBasedOn()

    /**
     * Gets the field which this field based on.
     *
     * @return string
     */
    function getBasedOn()
    {
        return $this->_basedOn;
    }

    // }}}
    // {{{ hasBasedOn()

    /**
     * Returns whether this field is based on any field or not.
     *
     * @return boolean
     */
    function hasBasedOn()
    {
        return !is_null($this->_basedOn);
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
