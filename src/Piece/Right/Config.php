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
use Piece::Right::Config::Field;

// {{{ Config

/**
 * The configuration container for Piece_Right validation sets.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Config
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

    private $_fields = array();

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ addValidation()

    /**
     * Adds a validation to a field with the given rules.
     *
     * @param string $fieldName
     * @param string $validatorName
     * @param array  $rules
     * @param string $message
     */
    public function addValidation($fieldName, $validatorName, $rules = array(),
                                  $message = null, $useInFinals = false
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
     * Merges the given configuretion into the existing configuration.
     *
     * @param Piece::Right::Config $config
     */
    public function merge(Config $config)
    {
        foreach ($config->getFieldNames() as $fieldName) {
            if (!$this->_hasField($fieldName)) {
                $this->_fields[$fieldName] = new Field();
            }

            $this->_fields[$fieldName]->merge($config->getField($fieldName));
        }
    }

    // }}}
    // {{{ setRequired()

    /**
     * Sets a field as required.
     *
     * @param string $fieldName
     * @param array  $required
     * @since Method available since Release 0.3.0
     */
    public function setRequired($fieldName, $required = array())
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setRequired($required);
    }

    // }}}
    // {{{ isRequired()

    /**
     * Returns whether the given field is required or not.
     *
     * @param string $fieldName
     * @return boolean
     * @throws Piece::Right::Exception
     * @since Method available since Release 0.3.0
     */
    public function isRequired($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->isRequired();
    }

    // }}}
    // {{{ getRequiredMessage()

    /**
     * Gets the message when a field is required.
     *
     * @param string $fieldName
     * @return string
     * @throws Piece::Right::Exception
     * @since Method available since Release 0.3.0
     */
    public function getRequiredMessage($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->getRequiredMessage();
    }

    // }}}
    // {{{ addFilter()

    /**
     * Adds a filter to a field.
     *
     * @param string $fieldName
     * @param string $filterName
     * @since Method available since Release 0.3.0
     */
    public function addFilter($fieldName, $filterName)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->addFilter($filterName);
    }

    // }}}
    // {{{ setWatcher()

    /**
     * Sets the watcher to the given field.
     *
     * @param string $fieldName
     * @param array  $watcher
     * @since Method available since Release 0.3.0
     */
    public function setWatcher($fieldName, $watcher)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setWatcher($watcher);
    }

    // }}}
    // {{{ getWatcher()

    /**
     * Gets the watcher for the given field.
     *
     * @param string $fieldName
     * @return array
     * @throws Piece::Right::Exception
     * @since Method available since Release 0.3.0
     */
    public function getWatcher($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
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
    public function addField($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            $this->_fields[$fieldName] = new Field();
        }

        if (!$this->_fields[$fieldName]->hasMessageVariable('_name')) {
            $this->_fields[$fieldName]->addMessageVariable('_name', $fieldName);
        }
    }

    // }}}
    // {{{ setForceValidation()

    /**
     * Turns force validation on/off for the given field.
     *
     * @param string  $fieldName
     * @param boolean $forceValidation
     * @since Method available since Release 0.3.0
     */
    public function setForceValidation($fieldName, $forceValidation = true)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setForceValidation($forceValidation);
    }

    // }}}
    // {{{ forceValidation()

    /**
     * Forces validation for the given field.
     *
     * @param string $fieldName
     * @throws Piece::Right::Exception
     * @since Method available since Release 0.3.0
     */
    public function forceValidation($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->forceValidation();
    }

    // }}}
    // {{{ setPseudo()

    /**
     * Sets the given field as a pseudo field.
     *
     * @param string $fieldName
     * @param array  $pseudo
     * @since Method available since Release 0.3.0
     */
    public function setPseudo($fieldName, $pseudo)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setPseudo($pseudo);
    }

    // }}}
    // {{{ isPseudo()

    /**
     * Returns whether the given field is pseudo or not.
     *
     * @param string $fieldName
     * @return boolean
     * @throws Piece::Right::Exception
     * @since Method available since Release 0.3.0
     */
    public function isPseudo($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->isPseudo();
    }

    // }}}
    // {{{ setDescription()

    /**
     * Sets the description of the given field.
     *
     * @param string $fieldName
     * @param string $description
     * @since Method available since Release 0.3.0
     */
    public function setDescription($fieldName, $description)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->addMessageVariable('_description', $description);
    }

    // }}}
    // {{{ addMessageVariable()

    /**
     * Adds a message variable for the given field.
     *
     * @param string $fieldName
     * @param string $variableName
     * @param string $value
     * @since Method available since Release 0.3.0
     */
    public function addMessageVariable($fieldName, $variableName, $value)
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
    public function getFieldNames()
    {
        return array_keys($this->_fields);
    }

    // }}}
    // {{{ getField()

    /**
     * Gets the Field object for the given field.
     *
     * @param string $fieldName
     * @throws Piece::Right::Exception
     * @return Piece::Right::Config::Field
     */
    public function getField($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName];
    }

    // }}}
    // {{{ getValidations()

    /**
     * Gets the validations for the given field.
     *
     * @param string $fieldName
     * @return array
     * @throws Piece::Right::Exception
     * @since Method available since Release 1.8.0
     */
    public function getValidations($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->getValidations();
    }

    // }}}
    // {{{ getFilters()

    /**
     * Gets the filters for the given field.
     *
     * @param string $fieldName
     * @return array
     * @throws Piece::Right::Exception
     * @since Method available since Release 1.8.0
     */
    public function getFilters($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->getFilters();
    }

    // }}}
    // {{{ getPseudo()

    /**
     * Gets the pseudo definition for the given field.
     *
     * @param string $fieldName
     * @return array
     * @throws Piece::Right::Exception
     * @since Method available since Release 1.8.0
     */
    public function getPseudo($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->getPseudo();
    }

    // }}}
    // {{{ getMessageVariables()

    /**
     * Gets the message variables of the given field.
     *
     * @param string $fieldName
     * @return array
     * @throws Piece::Right::Exception
     * @since Method available since Release 1.8.0
     */
    public function getMessageVariables($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->getMessageVariables();
    }

    // }}}
    // {{{ setBasedOn()

    /**
     * Sets a field which the given field based on.
     *
     * @param string $fieldName
     * @param string $basedOn
     * @since Method available since Release 1.8.0
     */
    public function setBasedOn($fieldName, $basedOn)
    {
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setBasedOn($basedOn);
    }

    // }}}
    // {{{ getBasedOn()

    /**
     * Gets the field which the given field based on.
     *
     * @param string $fieldName
     * @return string
     * @throws Piece::Right::Exception
     * @since Method available since Release 1.8.0
     */
    public function getBasedOn($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->getBasedOn();
    }

    // }}}
    // {{{ hasBasedOn()

    /**
     * Returns whether the given field is based on any field or not.
     *
     * @param string $fieldName
     * @return boolean
     * @throws Piece::Right::Exception
     * @since Method available since Release 1.8.0
     */
    public function hasBasedOn($fieldName)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        return $this->_fields[$fieldName]->hasBasedOn();
    }

    // }}}
    // {{{ inherit()

    /**
     * Extends the given field using the field in the template.
     *
     * @param string               $fieldName
     * @param string               $basedOn
     * @param Piece::Right::Config $template
     * @throws Piece::Right::Exception
     * @since Method available since Release 1.8.0
     */
    public function inherit($fieldName, $basedOn, Config $template)
    {
        if (!$this->_hasField($fieldName)) {
            throw new Exception("The field [ $fieldName ] is not found.");
        }

        if (version_compare(phpversion(), '5.0.0', '>=')) {
            $field = clone($this->_fields[$fieldName]);
            $this->_fields[$fieldName] = clone($template->getField($basedOn));
        } else {
            $field = $this->_fields[$fieldName];
            $this->_fields[$fieldName] = $template->getField($basedOn);
        }

        $this->_fields[$fieldName]->merge($field);
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
    // {{{ _hasField()

    /**
     * Returns whether the configuration has the given field or not.
     *
     * @param string $fieldName
     * @return boolean
     * @since Method available since Release 1.8.0
     */
    private function _hasField($fieldName)
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
