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
        $this->_results = &new Piece_Right_Results();
        $config = &$this->_configure($validationSetName, $dynamicConfig);
        $validationSet = $config->getValidationSet();

        foreach ($validationSet as $fieldName => $validations) {
            $fieldValue = call_user_func($this->_fieldValuesCallback, $fieldName);
            $filters = $config->getFiltersByFieldName($fieldName);
            foreach ($filters as $filterName) {
                if (!function_exists($filterName)) {
                    $filter = &Piece_Right_Filter_Factory::factory($filterName);
                    $fieldValue = $filter->filter($fieldValue);
                } else {
                    $fieldValue = call_user_func($filterName, $fieldValue);
                }
            }

            $this->_results->setFieldValue($fieldName, $fieldValue);

            if ($config->isRequired($fieldName)) {
                if ((is_null($fieldValue) || !strlen($fieldValue))) {
                    $this->_results->addError($fieldName, 'required', $config->getRequiredMessage($fieldName));
                    continue;
                }
            } else {
                if ((is_null($fieldValue) || !strlen($fieldValue))) {
                    continue;
                }
            }

            foreach ($validations as $validation) {
                $validator = &Piece_Right_Validator_Factory::factory($validation['validator']);
                $validator->setRules($validation['rules']);
                if (!$validator->validate($fieldValue)) {
                    $this->_results->addError($fieldName, $validation['validator'], $validation['message']);
                }
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
     * @return Piece_Right_Config
     */
    function &_configure($validationSet = null, $dynamicConfig = null)
    {
        $config = &Piece_Right_Config_Factory::factory($validationSet,
                                                       $this->_configDirectory,
                                                       $this->_cacheDirectory
                                                       );

        if (is_a($dynamicConfig, 'Piece_Right_Config')) {
            $config->merge($dynamicConfig);
        }

        return $config;
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
