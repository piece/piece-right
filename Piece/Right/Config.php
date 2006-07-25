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
    var $_filters = array();
    var $_watchers = array();

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

        $requiredFields = $config->getRequiredFields();
        array_walk($requiredFields, array(&$this, 'mergeRequiredField'));

        $filters = $config->getFilters();
        array_walk($filters, array(&$this, 'mergeFilters'));

        $watchers = $config->getWatchers();
        array_walk($watchers, array(&$this, 'mergeWatcher'));
    }

    // }}}
    // {{{ mergeValidations()

    /**
     * A callback that will be called by array_walk() function in merge()
     * method.
     *
     * @param array $validations
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
     * @param array  $elements
     * @since Method available since Release 0.3.0
     */
    function setRequired($field, $elements = array())
    {
        if (!array_key_exists($field, $this->_validationSet)) {
            $this->_validationSet[$field] = array();
        }

        if (!array_key_exists($field, $this->_requiredFields)) {
            $this->_requiredFields[$field] = array('enabled' => true,
                                                   'message' => null
                                                   );
        }

        if (array_key_exists('enabled', $elements)) {
            $this->_requiredFields[$field]['enabled'] = $elements['enabled'];
        }

        if (array_key_exists('message', $elements)
            && !is_null($elements['message'])
            ) {
            $this->_requiredFields[$field]['message'] = $elements['message'];
        }
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
        return array_key_exists($field, $this->_requiredFields) && $this->_requiredFields[$field]['enabled'];
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

    // }}}
    // {{{ addFilter()

    /**
     * Adds a filter to a field.
     *
     * @param string $field
     * @param string $filter
     * @since Method available since Release 0.3.0
     */
    function addFilter($field, $filter)
    {
        if (!array_key_exists($field, $this->_filters)) {
            $this->_filters[$field] = array();
        }

        array_push($this->_filters[$field], $filter);
    }

    // }}}
    // {{{ getFiltersByFieldName()

    /**
     * Gets the filters for the given field.
     *
     * @param string $field
     * @return array
     * @since Method available since Release 0.3.0
     */
    function getFiltersByFieldName($field)
    {
        return array_key_exists($field, $this->_filters) ? $this->_filters[$field] : array();
    }

    // }}}
    // {{{ getRequiredFields()

    /**
     * Gets the elements of the required fields as an array.
     *
     * @return array
     * @since Method available since Release 0.3.0
     */
    function getRequiredFields()
    {
        return $this->_requiredFields;
    }

    // }}}
    // {{{ mergeRequiredField()

    /**
     * A callback that will be called by array_walk() function in merge()
     * method.
     *
     * @param array $elements
     * @param string $field
     * @since Method available since Release 0.3.0
     */
    function mergeRequiredField($elements, $field)
    {
        $this->setRequired($field, $elements);
    }

    // }}}
    // {{{ mergeFilters()

    /**
     * A callback that will be called by array_walk() function in merge()
     * method.
     *
     * @param array $filters
     * @param string $field
     * @since Method available since Release 0.3.0
     */
    function mergeFilters($filters, $field)
    {
        foreach ($filters as $filter) {
            $this->addFilter($field, $filter);
        }
    }

    // }}}
    // {{{ getFilters()

    /**
     * Gets the all filters for the current configuration.
     *
     * @return array
     * @since Method available since Release 0.3.0
     */
    function getFilters()
    {
        return $this->_filters;
    }

    // }}}
    // {{{ setWatcher()

    /**
     * Sets the watcher to the given field.
     *
     * @param string $field
     * @param array  $watcher
     * @since Method available since Release 0.3.0
     */
    function setWatcher($field, $watcher)
    {
        $this->_watchers[$field] = $watcher;
    }

    // }}}
    // {{{ getWatchers()

    /**
     * Gets the watchers for the current configuration.
     *
     * @return array
     * @since Method available since Release 0.3.0
     */
    function getWatchers()
    {
        return $this->_watchers;
    }

    // }}}
    // {{{ mergeWatcher()

    /**
     * A callback that will be called by array_walk() function in merge()
     * method.
     *
     * @param array $watcher
     * @param string $field
     * @since Method available since Release 0.3.0
     */
    function mergeWatcher($watcher, $field)
    {
        $this->_watchers[$field] = $watcher;
    }

    // }}}
    // {{{ getWatcher()

    /**
     * Gets the watcher for the given field.
     *
     * @param string $field
     * @return array
     * @since Method available since Release 0.3.0
     */
    function getWatcher($field)
    {
        return array_key_exists($field, $this->_watchers) ? $this->_watchers[$field] : null;
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
