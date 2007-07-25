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
 * @since      File available since Release 1.2.0
 */

require_once 'Piece/Right.php';
require_once 'Piece/Right/Error.php';

// {{{ Piece_Right_Validation_Script

/**
 * A validation script which will be used to run a typical process of
 * validation with Piece_Right.
 *
 * @package    Piece_Right
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.2.0
 */
class Piece_Right_Validation_Script
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
    var $_postRunCallback;
    var $_payload;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * Prepares to run the validation script.
     *
     * @param string   $configDirectory
     * @param string   $cacheDirectory
     * @param callback $fieldValuesCallback
     * @param callback $postRunCallback
     */
    function Piece_Right_Validation_Script($configDirectory = null,
                                           $cacheDirectory = null,
                                           $fieldValuesCallback = null,
                                           $postRunCallback = null
                                           )
    {
        $this->_configDirectory     = $configDirectory;
        $this->_cacheDirectory      = $cacheDirectory;
        $this->_fieldValuesCallback = $fieldValuesCallback;
        $this->_postRunCallback     = $postRunCallback;
    }

    // }}}
    // {{{ run()

    /**
     * Runs the validation script with a validation set and/or dynamic
     * configration and sets appropriate values to the given container.
     *
     * @param string             $validationSet
     * @param mixed              &$container
     * @param Piece_Right_Config $config
     * @param boolean            $keepOriginalFieldValue
     * @return Piece_Right_Results
     * @throws PIECE_RIGHT_ERROR_INVALID_CONFIGURATION
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @throws PIECE_RIGHT_ERROR_INVALID_FILTER
     */
    function &run($validationSet,
                  &$container,
                  $config,
                  $keepOriginalFieldValue = true
                  )
    {
        $right = &new Piece_Right($this->_configDirectory,
                                  $this->_cacheDirectory,
                                  $this->_fieldValuesCallback
                                  );

        if (!is_null($this->_payload)) {
            $right->setPayload($this->_payload);
        }

        $result = $right->validate($validationSet, $config);
        if (Piece_Right_Error::hasErrors('exception')) {
            $return = null;
            return $return;
        }

        $results = $right->getResults();

        if ($result) {
            foreach ($results->getFieldNames() as $field) {
                $container->$field = $results->getFieldValue($field);
            }
        } else {
            if ($keepOriginalFieldValue) {
                foreach ($results->getFieldNames() as $field) {
                    $container->$field = call_user_func($right->getFieldValuesCallback(), $field);
                }
            } else {
                foreach ($results->getFieldNames() as $field) {
                    $container->$field = $results->getFieldValue($field);
                }
            }
        }

        if (!is_null($this->_postRunCallback)) {
            call_user_func_array($this->_postRunCallback,
                                 array($validationSet, &$results)
                                 );
        }

        return $results;
    }

    // }}}
    // {{{ setPayload()

    /**
     * Sets the given payload.
     *
     * @param mixed &$payload
     * @since Method available since Release 1.3.0
     */
    function setPayload(&$payload)
    {
        $this->_payload = &$payload;
    }

    // }}}
    // {{{ getFieldNames()

    /**
     * Gets all field names corresponding to the given validation set and
     * a Piece_Right_Config object.
     *
     * @param string             $validationSet
     * @param Piece_Right_Config $config
     * @return array
     * @throws PIECE_RIGHT_ERROR_INVALID_CONFIGURATION
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     */
    function getFieldNames($validationSet, $config)
    {
        $right = &new Piece_Right($this->_configDirectory,
                                  $this->_cacheDirectory,
                                  $this->_fieldValuesCallback
                                  );
        $fieldNames = $right->getFieldNames($validationSet, $config);
        if (Piece_Right_Error::hasErrors('exception')) {
            return;
        }

        return $fieldNames;
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
