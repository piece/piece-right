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
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
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
     * @throws PIECE_RIGHT_ERROR_INVALID_CONFIGURATION
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @throws PIECE_RIGHT_ERROR_INVALID_FILTER
     */
    function validate($validationSetName = null, $dynamicConfig = null)
    {
        $this->_configure($validationSetName, $dynamicConfig);
        if (Piece_Right_Error::hasErrors('exception')) {
            return;
        }

        $this->_results = &new Piece_Right_Results();
        $validationSet = $this->_config->getValidationSet();

        $this->_filter($validationSet);
        if (Piece_Right_Error::hasErrors('exception')) {
            return;
        }

        $this->_watch($validationSet);

        foreach ($validationSet as $fieldName => $validations) {
            $fieldValue = $this->_results->getFieldValue($fieldName);

            if (!$this->_checkValidationRequirement($fieldName, $fieldValue)) {
                continue;
            }

            $this->_validate($fieldName, $fieldValue, $validations);
            if (Piece_Right_Error::hasErrors('exception')) {
                return;
            }
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return @$_POST[$fieldName];
        }

        return @$_GET[$fieldName];
    }

    // }}}
    // {{{ getResults()

    /**
     * Gets the Piece_Right_Results object for the current validation set.
     *
     * @return Piece_Right_Results
     */
    function &getResults()
    {
        return $this->_results;
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
     * @param string             $validationSet
     * @param Piece_Right_Config $dynamicConfig
     * @throws PIECE_RIGHT_ERROR_INVALID_CONFIGURATION
     */
    function _configure($validationSet = null, $dynamicConfig = null)
    {
        $this->_config = &Piece_Right_Config_Factory::factory($validationSet,
                                                              $this->_configDirectory,
                                                              $this->_cacheDirectory
                                                              );
        if (Piece_Right_Error::hasErrors('exception')) {
            return;
        }

        if (is_a($dynamicConfig, 'Piece_Right_Config')) {
            $this->_config->merge($dynamicConfig);
        }
    }

    // }}}
    // {{{ _isEmpty()

    /**
     * Returns whether a value of a field is empty or not.
     *
     * @param string $value
     * @return boolean
     * @since Method available since Release 0.3.0
     */
    function _isEmpty($value)
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
    // {{{ _filter()

    /**
     * Filters field values.
     *
     * @param array $validationSet
     * @since Method available since Release 0.3.0
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @throws PIECE_RIGHT_ERROR_INVALID_FILTER
     */
    function _filter($validationSet)
    {
        foreach ($validationSet as $fieldName => $validations) {
            $fieldValue = call_user_func($this->_fieldValuesCallback, $fieldName);
            $filters = $this->_config->getFiltersByFieldName($fieldName);
            foreach ($filters as $filterName) {
                if (!function_exists($filterName)) {
                    $filter = &Piece_Right_Filter_Factory::factory($filterName);
                    if (Piece_Right_Error::hasErrors('exception')) {
                        return;
                    }

                    $fieldValue = $filter->filter($fieldValue);
                } else {
                    $fieldValue = call_user_func($filterName, $fieldValue);
                }
            }

            $this->_results->setFieldValue($fieldName, $fieldValue);
        }
    }

    // }}}
    // {{{ _watch()

    /**
     * Watches the target fields and turns the fields requirements
     * on/off.
     *
     * @param array $validationSet
     * @since Method available since Release 0.3.0
     */
    function _watch($validationSet)
    {
        foreach ($validationSet as $fieldName => $validations) {
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

            $turnOnFields = array();
            foreach ($watcher['target'] as $target) {
                $targetValue = $this->_results->getFieldValue($target['name']);
                if ($this->_isEmpty($targetValue)) {
                    continue;
                }

                if ($this->_shouldBeTurnedOn($target, $targetValue)) {
                    foreach ($watcher['turnOn'] as $turnOnFieldName) {
                        $turnOnFields[] = $turnOnFieldName;
                    }
                }
            }

            foreach ($turnOnFields as $turnOnFieldName) {
                $this->_config->setRequired($turnOnFieldName, array('enabled' => true));
            }

            if (array_key_exists('turnOff', $watcher)) {
                foreach ($watcher['turnOff'] as $turnOffFieldName) {
                    $this->_config->setRequired($turnOffFieldName, array('enabled' => false));
                }
            }
        }
    }

    // }}}
    // {{{ _checkValidationRequirement()

    /**
     * Returns whether the current validation should be continued or not.
     *
     * @param string $name
     * @param string $value
     * @return boolean
     * @since Method available since Release 0.3.0
     */
    function _checkValidationRequirement($name, $value)
    {
        if ($this->_config->isRequired($name)) {
            if ($this->_isEmpty($value)) {
                $this->_results->addError($name,
                                          'required',
                                          $this->_config->getRequiredMessage($name)
                                          );
                return false;
            }
        } else {
            if ($this->_isEmpty($value)) {
                return false;
            }
        }

        return true;
    }

    // }}}
    // {{{ _validate()

    /**
     * Validates the value by the validations of the field.
     *
     * @param string $name
     * @param string $value
     * @param array  $validations
     * @since Method available since Release 0.3.0
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @throws PIECE_RIGHT_ERROR_INVALID_VALIDATOR
     */
    function _validate($name, $value, $validations)
    {
        foreach ($validations as $validation) {
                $validator = &Piece_Right_Validator_Factory::factory($validation['validator']);
                if (Piece_Right_Error::hasErrors('exception')) {
                    return;
                }

                $validator->setResults($this->_results);
                $validator->setRules($validation['rules']);
                $validator->setMessage($validation['message']);
                if (!$validator->validate($value)) {
                    $this->_results->addError($name,
                                              $validation['validator'],
                                              $validator->getMessage()
                                              );
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
        if (!array_key_exists('trigger', $target)) {
            return true;
        }

        if (!array_key_exists('trigger', $target)
            || !array_key_exists('comparisonOperator', $target['trigger'])
            || !array_key_exists('comparisonTo', $target['trigger'])
            ) {
            return false;
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
