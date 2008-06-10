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
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Right/Config/Field.php';
require_once 'Piece/Right/Error.php';

// {{{ Piece_Right_Config

/**
 * The configuration container for Piece_Right validation sets.
 *
 * @package    Piece_Right
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
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

    var $_fields = array();

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ addValidation()

    /**
     * Adds a validation with a given rules to a given field.
     *
     * @param string  $fieldName
     * @param string  $validatorName
     * @param array   $rules
     * @param string  $message
     * @param boolean $useInFinals
     */
    function addValidation($fieldName,
                           $validatorName,
                           $rules = array(),
                           $message = null,
                           $useInFinals = false
                           )
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->addValidation($validatorName,
                                                  $rules,
                                                  $message,
                                                  $useInFinals
                                                  );
    }

    // }}}
    // {{{ merge()

    /**
     * Merges a given configuretion into the existing configuration.
     *
     * @param Piece_Right_Config &$config
     */
    function merge(&$config)
    {
        foreach ($config->getFieldNames() as $fieldName) {
            if (!$this->_hasField($fieldName)) {
                $field = &new Piece_Right_Config_Field();
                $this->_fields[$fieldName] = &$field;
            }

            $this->_fields[$fieldName]->merge($config->getField($fieldName));
        }
    }

    // }}}
    // {{{ setRequired()

    /**
     * Sets a required definition to a given field.
     *
     * @param string $fieldName
     * @param array  $required
     * @since Method available since Release 0.3.0
     */
    function setRequired($fieldName, $required = array())
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setRequired($required);
    }

    // }}}
    // {{{ isRequired()

    /**
     * Checks whether this field is required or not.
     *
     * @param string $fieldName
     * @return boolean
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 0.3.0
     */
    function isRequired($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->isRequired();
    }

    // }}}
    // {{{ getRequiredMessage()

    /**
     * Gets the required message for a given field.
     *
     * @param string $fieldName
     * @return string
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 0.3.0
     */
    function getRequiredMessage($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->getRequiredMessage();
    }

    // }}}
    // {{{ addFilter()

    /**
     * Adds a filter to a given field.
     *
     * @param string $fieldName
     * @param string $filterName
     * @since Method available since Release 0.3.0
     */
    function addFilter($fieldName, $filterName)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->addFilter($filterName);
    }

    // }}}
    // {{{ setWatcher()

    /**
     * Sets a watcher definition to a given field.
     *
     * @param string $fieldName
     * @param array  $watcher
     * @since Method available since Release 0.3.0
     */
    function setWatcher($fieldName, $watcher)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setWatcher($watcher);
    }

    // }}}
    // {{{ getWatcher()

    /**
     * Gets the watcher definition for a given field.
     *
     * @param string $fieldName
     * @return array
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 0.3.0
     */
    function getWatcher($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->getWatcher();
    }

    // }}}
    // {{{ addField()

    /**
     * Adds a field which will be validated.
     *
     * @param string $fieldName
     * @since Method available since Release 0.3.0
     */
    function addField($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            $field = &new Piece_Right_Config_Field();
            $this->_fields[$fieldName] = &$field;
        }

        if (!$this->_fields[$fieldName]->hasMessageVariable('_name')) {
            $this->_fields[$fieldName]->addMessageVariable('_name', $fieldName);
        }
    }

    // }}}
    // {{{ setForceValidation()

    /**
     * Turns force validation on/off for a given field.
     *
     * @param string  $fieldName
     * @param boolean $forceValidation
     * @since Method available since Release 0.3.0
     */
    function setForceValidation($fieldName, $forceValidation = true)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setForceValidation($forceValidation);
    }

    // }}}
    // {{{ forceValidation()

    /**
     * Checks whether or not forcing invocation of all validations for a given
     * field.
     *
     * @param string $fieldName
     * @return boolean
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 0.3.0
     */
    function forceValidation($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->forceValidation();
    }

    // }}}
    // {{{ setPseudo()

    /**
     * Sets a pseudo definition to a given field.
     *
     * @param string $fieldName
     * @param array  $pseudo
     * @since Method available since Release 0.3.0
     */
    function setPseudo($fieldName, $pseudo)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setPseudo($pseudo);
    }

    // }}}
    // {{{ isPseudo()

    /**
     * Checks whether this field is pseudo or not.
     *
     * @param string $fieldName
     * @return boolean
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 0.3.0
     */
    function isPseudo($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->isPseudo();
    }

    // }}}
    // {{{ setDescription()

    /**
     * Sets the description of a given field.
     *
     * @param string $fieldName
     * @param string $description
     * @since Method available since Release 0.3.0
     */
    function setDescription($fieldName, $description)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->addMessageVariable('_description', $description);
    }

    // }}}
    // {{{ addMessageVariable()

    /**
     * Adds a message variable for a given field.
     *
     * @param string $fieldName
     * @param string $variableName
     * @param string $value
     * @since Method available since Release 0.3.0
     */
    function addMessageVariable($fieldName, $variableName, $value)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->addMessageVariable($variableName, $value);
    }

    // }}}
    // {{{ getFieldNames()

    /**
     * Gets all field names from the configuration.
     *
     * @return array
     */
    function getFieldNames()
    {
        return array_keys($this->_fields);
    }

    // }}}
    // {{{ getField()

    /**
     * Gets the Piece_Right_Config_Field object for a given field.
     *
     * @param string $fieldName
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @return Piece_Right_Config_Field
     */
    function &getField($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
            $return = null;
            return $return;
        }

        return $this->_fields[$fieldName];
    }

    // }}}
    // {{{ getValidations()

    /**
     * Gets the validations for a given field.
     *
     * @param string $fieldName
     * @return array
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 1.8.0
     */
    function getValidations($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->getValidations();
    }

    // }}}
    // {{{ getFilters()

    /**
     * Gets the filters for a given field.
     *
     * @param string $fieldName
     * @return array
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 1.8.0
     */
    function getFilters($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->getFilters();
    }

    // }}}
    // {{{ getPseudo()

    /**
     * Gets the pseudo definition for a given field.
     *
     * @param string $fieldName
     * @return array
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 1.8.0
     */
    function getPseudo($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->getPseudo();
    }

    // }}}
    // {{{ getMessageVariables()

    /**
     * Gets the message variables of a given field.
     *
     * @param string $fieldName
     * @return array
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 1.8.0
     */
    function getMessageVariables($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
            return;
        }

        return $this->_fields[$fieldName]->getMessageVariables();
    }

    // }}}
    // {{{ setBasedOn()

    /**
     * Sets a field which a given field based on.
     *
     * @param string $fieldName
     * @param string $basedOn
     * @since Method available since Release 1.8.0
     */
    function setBasedOn($fieldName, $basedOn)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setBasedOn($basedOn);
    }

    // }}}
    // {{{ getBasedOn()

    /**
     * Gets the field which a given field based on.
     *
     * @param string $fieldName
     * @return string
     * @since Method available since Release 1.8.0
     */
    function getBasedOn($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->getBasedOn();
    }

    // }}}
    // {{{ hasBasedOn()

    /**
     * Returns whether a given field is based on any field or not.
     *
     * @param string $fieldName
     * @return boolean
     * @since Method available since Release 1.8.0
     */
    function hasBasedOn($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
        }

        return $this->_fields[$fieldName]->hasBasedOn();
    }

    // }}}
    // {{{ inherit()

    /**
     * Extends a given field using the field in the template.
     *
     * @param string             $fieldName
     * @param string             $basedOn
     * @param Piece_Right_Config &$template
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 1.8.0
     */
    function inherit($fieldName, $basedOn, &$template)
    {
        if (!$this->_hasField($fieldName)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The field [ $fieldName ] not found."
                                    );
            return;
        }

        if (version_compare(phpversion(), '5.0.0', '>=')) {
            $field = clone($this->_fields[$fieldName]);
            $this->_fields[$fieldName] = clone($template->getField($basedOn));
        } else {
            $field = $this->_fields[$fieldName];
            $this->_fields[$fieldName] = $template->getField($basedOn);
        }

        if (Piece_Right_Error::hasErrors('exception')) {
            return;
        }

        $this->_fields[$fieldName]->merge($field);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _hasField()

    /**
     * Returns whether the configuration has a given field or not.
     *
     * @param string $fieldName
     * @return boolean
     * @since Method available since Release 1.8.0
     */
    function _hasField($fieldName)
    {
        return array_key_exists($fieldName, $this->_fields);
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
