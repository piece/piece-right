<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 1.8.0
 */

// {{{ Piece_Right_Config_Field

/**
 * @package    Piece_Right
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
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

    function getRequired()
    {
        return $this->_required;
    }

    function isRequired()
    {
        if (!is_null($this->_required)) {
            return $this->_required['enabled'];
        } else {
            return false;
        }
    }

    function getRequiredMessage()
    {
        return $this->_required['message'];
    }

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
            $field->setForceValidation($forceValidation);
        }

        $basedOn = $field->getBasedOn();
        if (!is_null($basedOn)) {
            $this->setBasedOn($basedOn);
        }
    }

    function addFilter($filterName)
    {
        $this->_filters[] = $filterName;
    }

    function getFilters()
    {
        return $this->_filters;
    }

    function addValidation($validatorName, $rules, $message, $useInFinals)
    {
        $this->_validations[] = array('validator'   => $validatorName,
                                      'rules'       => $rules,
                                      'message'     => $message,
                                      'useInFinals' => $useInFinals
                                      );
    }

    function getValidations()
    {
        return $this->_validations;
    }

    function setWatcher($watcher)
    {
        $this->_watcher = $watcher;
    }

    function getWatcher()
    {
        return $this->_watcher;
    }

    function setPseudo($pseudo)
    {
        $this->_pseudo = $pseudo;
    }

    function getPseudo()
    {
        return $this->_pseudo;
    }

    function isPseudo()
    {
        if (!is_null($this->_pseudo)) {
            return true;
        } else {
            return false;
        }
    }

    function hasMessageVariable($variableName)
    {
        return array_key_exists($variableName, $this->_messageVariables);
    }

    function addMessageVariable($variableName, $value)
    {
        $this->_messageVariables[$variableName] = $value;
    }

    function getMessageVariables()
    {
        return $this->_messageVariables;
    }

    function setForceValidation($forceValidation)
    {
        $this->_forceValidation = $forceValidation;
    }

    function forceValidation()
    {
        if (!is_null($this->_forceValidation)) {
            return $this->_forceValidation;
        } else {
            return false;
        }
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
        if (!is_null($this->_basedOn)) {
            return true;
        } else {
            return false;
        }
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
