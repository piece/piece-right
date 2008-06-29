<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
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

require_once 'Piece/Right/Config/Factory.php';
require_once 'Piece/Right/Validator/Factory.php';
require_once 'Piece/Right/Results.php';
require_once 'Piece/Right/Filter/Factory.php';
require_once 'Piece/Right/Error.php';

// {{{ Piece_Right

/**
 * A single entry point for Piece_Right validation sets.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Piece_Right
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_configDirectory;
    var $_cacheDirectory;
    var $_fieldValuesCallback;
    var $_results;
    var $_config;
    var $_payload;
    var $_currentFilter;
    var $_currentFilterIsArrayable;
    var $_template;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * Configures the current validation.
     *
     * @param string   $configDirectory
     * @param string   $cacheDirectory
     * @param callback $fieldValuesCallback
     */
    function Piece_Right($configDirectory = null,
                         $cacheDirectory = null,
                         $fieldValuesCallback = null
                         )
    {
        $this->_configDirectory = $configDirectory;
        $this->_cacheDirectory = $cacheDirectory;
        if (is_callable($fieldValuesCallback)) {
            $this->_fieldValuesCallback = $fieldValuesCallback;
        } else {
            $this->_fieldValuesCallback = array(__CLASS__, 'getFieldValueFromSuperglobals');
        }
    }

    // }}}
    // {{{ validate()

    /**
     * Validates the current field values with the current validation set.
     *
     * @param string             $validationSetName
     * @param Piece_Right_Config $dynamicConfig
     * @return boolean
     */
    function validate($validationSetName = null, $dynamicConfig = null)
    {
        $config = &$this->_configure($validationSetName, $dynamicConfig);
        if (Piece_Right_Error::hasErrors()) {
            return;
        }

        $this->_config = &$config;
        $this->_results = &new Piece_Right_Results();

        $messageVariables = array();
        foreach ($this->_config->getFieldNames() as $fieldName) {
            $messageVariables[$fieldName] = $this->_config->getMessageVariables($fieldName);
        }
        $this->_results->setMessageVariables($messageVariables);

        $this->_filter();
        if (Piece_Right_Error::hasErrors()) {
            return;
        }

        $this->_generatePseudoFields();

        $this->_watch();
        if (Piece_Right_Error::hasErrors()) {
            return;
        }

        $this->_validateFields(false);

        if (!$this->_results->countErrors()) {
            $this->_validateFields(true);
        }

        return !(boolean)$this->_results->countErrors();
    }

    // }}}
    // {{{ getFieldValueFromSuperglobals()

    /**
     * Gets the value of the given field name from PHP superglobals.
     *
     * @param string $fieldName
     * @return mixed
     * @static
     */
    function getFieldValueFromSuperglobals($fieldName)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            return @$_GET[$fieldName];
        }

        if (array_key_exists($fieldName, $_FILES)) {
            return $_FILES[$fieldName];
        }

        return @$_POST[$fieldName];
    }

    // }}}
    // {{{ getResults()

    /**
     * Gets the Piece_Right_Results object for the current validation.
     *
     * @return Piece_Right_Results
     */
    function &getResults()
    {
        return $this->_results;
    }

    // }}}
    // {{{ setPayload()

    /**
     * Sets the given payload.
     *
     * @param mixed &$payload
     * @since Method available since Release 0.5.0
     */
    function setPayload(&$payload)
    {
        $this->_payload = &$payload;
    }

    // }}}
    // {{{ isEmpty()

    /**
     * Returns whether a value of a field is empty or not.
     *
     * @param string $value
     * @return boolean
     * @static
     * @since Method available since Release 0.3.0
     */
    function isEmpty($value)
    {
        if (is_null($value)) {
            return true;
        }

        if (is_array($value)) {
            if (!count($value)) {
                return true;
            }
        } else {
            if (!strlen($value)) {
                return true;
            }
        }

        return false;
    }

    // }}}
    // {{{ getFieldValuesCallback()

    /**
     * Gets the callback to get field values for the current validation.
     *
     * @return callback
     */
    function getFieldValuesCallback()
    {
        return $this->_fieldValuesCallback;
    }

    // }}}
    // {{{ getFieldNames()

    /**
     * Gets all field names corresponding to the given validation set and
     * a Piece_Right_Config object.
     *
     * @param string             $validationSetName
     * @param Piece_Right_Config $dynamicConfig
     * @return array
     */
    function getFieldNames($validationSetName = null, $dynamicConfig = null)
    {
        $config = &$this->_configure($validationSetName, $dynamicConfig);
        if (Piece_Right_Error::hasErrors()) {
            return;
        }

        return $config->getFieldNames();
    }

    // }}}
    // {{{ setTemplate()

    /**
     * Sets the given validation set as a template.
     *
     * @param string $template
     * @since Method available since Release 1.8.0
     */
    function setTemplate($template)
    {
        $this->_template = $template;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _configure()

    /**
     * Configures the validation set.
     *
     * First this method tries to load a configuration from a configuration
     * file in the given configration directory using
     * Piece_Right_Config_Factory::factory method. The method creates a new
     * object if the load failed.
     * Second this method merges the given configuretion into the loaded
     * configuration.
     *
     * @param string             $validationSetName
     * @param Piece_Right_Config $dynamicConfig
     * @return Piece_Right_Config
     */
    function &_configure($validationSetName = null, $dynamicConfig = null)
    {
        $config = &Piece_Right_Config_Factory::factory($validationSetName,
                                                       $this->_configDirectory,
                                                       $this->_cacheDirectory,
                                                       $this->_template
                                                       );
        if (Piece_Right_Error::hasErrors()) {
            $return = null;
            return $return;
        }

        if (is_a($dynamicConfig, 'Piece_Right_Config')) {
            $config->merge($dynamicConfig);
        }

        return $config;
    }

    // }}}
    // {{{ _filter()

    /**
     * Filters field values.
     *
     * @since Method available since Release 0.3.0
     */
    function _filter()
    {
        foreach ($this->_config->getFieldNames() as $fieldName) {
            $value = call_user_func($this->_fieldValuesCallback, $fieldName);
            $filters = $this->_config->getFilters($fieldName);
            foreach ($filters as $filterName) {
                if (!function_exists($filterName)) {
                    $filter = &Piece_Right_Filter_Factory::factory($filterName);
                    if (Piece_Right_Error::hasErrors()) {
                        return;
                    }

                    $this->_currentFilter = array(&$filter, 'filter');

                    if (method_exists($filter, 'isArrayable')) {
                        $this->_currentFilterIsArrayable = $filter->isArrayable();
                    } else {
                        $this->_currentFilterIsArrayable = false;
                    }

                    $value = $this->_invokeFilter($value);
                } else {
                    $this->_currentFilter = $filterName;
                    $this->_currentFilterIsArrayable = false;
                    $value = $this->_invokeFilter($value);
                }
            }

            $this->_results->setFieldValue($fieldName, $value);
        }
    }

    // }}}
    // {{{ _watch()

    /**
     * Watches the target fields and turns the fields requirements
     * on/off.
     *
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @since Method available since Release 0.3.0
     */
    function _watch()
    {
        $fieldNames = $this->_config->getFieldNames();
        foreach ($fieldNames as $fieldName) {
            $watcher = $this->_config->getWatcher($fieldName);
            if (!is_array($watcher)) {
                continue;
            }

            $found = false;
            foreach ($watcher['target'] as $target) {
                if ($target['name'] == $fieldName) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $watcher['target'][] = array('name' => $fieldName);
            }

            if (!array_key_exists('turnOn', $watcher)) {
                $watcher['turnOn'] = array();
                foreach ($watcher['target'] as $target) {
                    if (!in_array($target['name'], $watcher['turnOn'])) {
                        $watcher['turnOn'][] = $target['name'];
                    }
                }
            }

            if (!in_array($fieldName, $watcher['turnOn'])) {
                $watcher['turnOn'][] = $fieldName;
            }

            if (!array_key_exists('turnOff', $watcher)) {
                $watcher['turnOff'] = array();
            }

            $turnOnFields = array();
            $turnOffFields = array();
            foreach ($watcher['target'] as $target) {
                $targetValue = $this->_results->getFieldValue($target['name']);
                if (Piece_Right::isEmpty($targetValue)) {
                    continue;
                }

                if ($this->_shouldBeTurnedOn($target, $targetValue)) {
                    foreach ($watcher['turnOn'] as $turnOnFieldName) {
                        $turnOnFields[] = $turnOnFieldName;
                    }

                    foreach ($watcher['turnOff'] as $turnOffFieldName) {
                        $turnOffFields[] = $turnOffFieldName;
                    }
                }
            }

            foreach ($turnOnFields as $turnOnFieldName) {
                if (!in_array($turnOnFieldName, $fieldNames)) {
                    Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                            "The field [ $turnOnFieldName ] which is a target of turn on is not found in the configuration."
                                            );
                    return;
                }

                $this->_config->setRequired($turnOnFieldName, array('enabled' => true));
            }

            foreach ($turnOffFields as $turnOffFieldName) {
                if (!in_array($turnOffFieldName, $fieldNames)) {
                    Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                            "The field [ $turnOffFieldName ] which is a target of turn off is not found in the configuration."
                                            );
                    return;
                }

                $this->_config->setRequired($turnOffFieldName, array('enabled' => false));
            }

            if (array_key_exists('turnOnForceValidation', $watcher)
                && $watcher['turnOnForceValidation']
                && count($turnOnFields)
                ) {
                $this->_config->setForceValidation($fieldName);
            }
        }
    }

    // }}}
    // {{{ _checkValidationRequirement()

    /**
     * Returns whether the current validation should be continued or not.
     *
     * @param string $fieldName
     * @param string $value
     * @return boolean
     * @since Method available since Release 0.3.0
     */
    function _checkValidationRequirement($fieldName, $value)
    {
        if ($this->_config->isRequired($fieldName)) {
            if (Piece_Right::isEmpty($value)) {
                $this->_results->addError($fieldName,
                                          'required',
                                          $this->_config->getRequiredMessage($fieldName)
                                          );
                return false;
            }
        } else {
            if (Piece_Right::isEmpty($value)) {
                return false;
            }
        }

        return true;
    }

    // }}}
    // {{{ _validateField()

    /**
     * Validates a field value by the given validations.
     *
     * @param string  $fieldName
     * @param string  $value
     * @param boolean $isFinals
     * @since Method available since Release 0.3.0
     */
    function _validateField($fieldName, $value, $isFinals)
    {
        foreach ($this->_config->getValidations($fieldName) as $validation) {
            if ($validation['useInFinals'] != $isFinals) {
                continue;
            }

            $validator = &Piece_Right_Validator_Factory::factory($validation['validator']);
            if (Piece_Right_Error::hasErrors()) {
                return;
            }

            if (is_array($value) && !$validator->isArrayable()) {
                trigger_error("The value of the field [ $fieldName ] is an array, but the validator [ {$validation['validator']} ] is not arrayable. This validation is skipped.",
                              E_USER_WARNING
                              );
                $this->_results->addError($fieldName,
                                          $validation['validator'],
                                          $validator->getMessage()
                                          );
                continue;
            }

            $validator->setResults($this->_results);
            $validator->setRules($validation['rules']);
            $validator->setMessage($validation['message']);
            $validator->setPayload($this->_payload);
            if (!$validator->validate($value)) {
                $this->_results->addError($fieldName,
                                          $validation['validator'],
                                          $validator->getMessage()
                                          );

                if (!$this->_config->forceValidation($fieldName)) {
                    return;
                }
            }
        }
    }

    // }}}
    // {{{ _shouldBeTurnedOn()

    /**
     * Returns whether the target field should be turned on or not.
     *
     * @param array $target
     * @param string $targetValue
     * @return boolean
     * @since Method available since Release 0.3.0
     */
    function _shouldBeTurnedOn($target, $targetValue)
    {
        if (!array_key_exists('trigger', $target)
            || !array_key_exists('comparisonOperator', $target['trigger'])
            || !array_key_exists('comparisonTo', $target['trigger'])
            ) {
            return true;
        }

        $turnOn = false;
        switch ($target['trigger']['comparisonOperator']) {
        case '==':
            if ($targetValue == $target['trigger']['comparisonTo']) {
                $turnOn = true;
            }
            break;
        case '!=':
        case '<>':
            if ($targetValue != $target['trigger']['comparisonTo']) {
                $turnOn = true;
            }
            break;
        case '<':
            if ($targetValue < $target['trigger']['comparisonTo']) {
                $turnOn = true;
            }
            break;
        case '>':
            if ($targetValue > $target['trigger']['comparisonTo']) {
                $turnOn = true;
            }
            break;
        case '<=':
            if ($targetValue <= $target['trigger']['comparisonTo']) {
                $turnOn = true;
            }
            break;
        case '>=':
            if ($targetValue >= $target['trigger']['comparisonTo']) {
                $turnOn = true;
            }
            break;
        default:
            break;
        }

        return $turnOn;
    }

    // }}}
    // {{{ _generatePseudoFields()

    /**
     * Generates pseudo fields.
     *
     * @since Method available since Release 0.3.0
     */
    function _generatePseudoFields()
    {
        foreach ($this->_config->getFieldNames() as $fieldName) {
            if (!$this->_config->isPseudo($fieldName)) {
                continue;
            }

            $pseudo = $this->_config->getPseudo($fieldName);
            if (!array_key_exists('format', $pseudo)) {
                continue;
            }

            if (!array_key_exists('arg', $pseudo) || !is_array($pseudo['arg'])) {
                continue;
            }

            $numberOfValidFields = 0;
            $args = array();
            foreach ($pseudo['arg'] as $arg) {
                $value = $this->_results->getFieldValue($arg);
                if (!Piece_Right::isEmpty($value)) {
                    ++$numberOfValidFields;
                }

                $args[] = $value;
            }

            if ($numberOfValidFields < count($pseudo['arg'])) {
                continue;
            }

            $this->_results->setFieldValue($fieldName,
                                           vsprintf($pseudo['format'], $args)
                                           );
        }
    }

    // }}}
    // {{{ _invokeFilter()

    /**
     * Filters a field value.
     *
     * @param mixed $value
     * @return mixed
     * @since Method available since Release 1.3.0
     */
    function _invokeFilter($value)
    {
        if (!is_array($value)) {
            return call_user_func($this->_currentFilter, $value);
        } else {
            if (!$this->_currentFilterIsArrayable) {
                return array_map(array(&$this, __FUNCTION__), $value);
            } else {
                return call_user_func($this->_currentFilter, $value);
            }
        }
    }

    // }}}
    // {{{ _validateFields()

    /**
     * Validates value of all fields.
     *
     * @param boolean $isFinals
     * @since Method available since Release 1.6.0
     */
    function _validateFields($isFinals)
    {
        foreach ($this->_config->getFieldNames() as $fieldName) {
            $value = $this->_results->getFieldValue($fieldName);

            if (!$this->_config->forceValidation($fieldName)) {
                if (!$this->_checkValidationRequirement($fieldName, $value)) {
                    continue;
                }
            }

            $this->_validateField($fieldName, $value, $isFinals);
            if (Piece_Right_Error::hasErrors()) {
                return;
            }
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
