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
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://piece-framework.com/piece-right/
 * @since      File available since Release 0.3.0
 */

require_once 'Piece/Right/Error.php';

// {{{ GLOBALS

$GLOBALS['PIECE_RIGHT_Filter_Instances'] = array();
$GLOBALS['PIECE_RIGHT_Filter_Directories'] = array(dirname(__FILE__) . '/../../..');
$GLOBALS['PIECE_RIGHT_Filter_Prefixes'] = array('Piece_Right_Filter');

// }}}
// {{{ Piece_Right_Filter_Factory

/**
 * A factory class for creating filter objects.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @since      Class available since Release 0.3.0
 */
class Piece_Right_Filter_Factory
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     * @static
     */

    // }}}
    // {{{ factory()

    /**
     * Creates a filter object from the filter directories.
     *
     * @param string $filterName
     * @return mixed
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @throws PIECE_RIGHT_ERROR_INVALID_FILTER
     */
    function &factory($filterName)
    {
        if (!array_key_exists($filterName, $GLOBALS['PIECE_RIGHT_Filter_Instances'])) {
            $filterClass = Piece_Right_Filter_Factory::_findFilterClass($filterName);
            if (is_null($filterClass)) {
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                        "The filter [ $filterName ] not found in the following directories:\n" .
                                        implode("\n", $GLOBALS['PIECE_RIGHT_Filter_Directories'])
                                        );
                $return = null;
                return $return;
            }

            $GLOBALS['PIECE_RIGHT_Filter_Instances'][$filterName] = &new $filterClass();
        }

        return $GLOBALS['PIECE_RIGHT_Filter_Instances'][$filterName];
    }

    // }}}
    // {{{ addFilterDirectory()

    /**
     * Adds a filter directory.
     *
     * @param string $filterDirectory
     */
    function addFilterDirectory($filterDirectory)
    {
        array_unshift($GLOBALS['PIECE_RIGHT_Filter_Directories'], $filterDirectory);
    }

    // }}}
    // {{{ clearInstances()

    /**
     * Clears the filter instances.
     */
    function clearInstances()
    {
        $GLOBALS['PIECE_RIGHT_Filter_Instances'] = array();
    }

    // }}}
    // {{{ addFilterPrefix()

    /**
     * Adds a prefix for a filter.
     *
     * @param string $filterPrefix
     */
    function addFilterPrefix($filterPrefix)
    {
        array_unshift($GLOBALS['PIECE_RIGHT_Filter_Prefixes'], $filterPrefix);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _loadFromDirectory()

    /**
     * Loads a filter from the given directory.
     *
     * @param string $filterClass
     * @param string $filterDirectory
     * @return boolean
     * @static
     */
    function _loadFromDirectory($filterClass, $filterDirectory)
    {
        $file = "$filterDirectory/" . str_replace('_', '/', $filterClass) . '.php';

        if (!file_exists($file)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The filter file [ $file ] for the class [ $filterClass ] not found.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();
            return false;
        }

        if (!is_readable($file)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_READABLE,
                                    "The filter file [ $file ] was not readable.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();
            return false;
        }

        if (!include_once $file) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The filter file [ $file ] not found or was not readable.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();
            return false;
        }

        return Piece_Right_Filter_Factory::_loaded($filterClass);
    }

    // }}}
    // {{{ _loaded()

    /**
     * Returns whether the given filter has already been loaded or not.
     *
     * @param string $filterClass
     * @return boolean
     * @static
     */
    function _loaded($filterClass)
    {
        if (version_compare(phpversion(), '5.0.0', '<')) {
            return class_exists($filterClass);
        } else {
            return class_exists($filterClass, false);
        }
    }

    // }}}
    // {{{ _findFilterClass()

    /**
     * Finds a filter class from the filter directories and the prefixes.
     *
     * @param string $filterName
     * @return string
     */
    function _findFilterClass($filterName)
    {
        foreach ($GLOBALS['PIECE_RIGHT_Filter_Prefixes'] as $prefixAlias) {
            $filterClass = Piece_Right_Filter_Factory::_getFilterClass($filterName, $prefixAlias);
            if (Piece_Right_Filter_Factory::_loaded($filterClass)) {
                return $filterClass;
            }
        }

        foreach ($GLOBALS['PIECE_RIGHT_Filter_Directories'] as $filterDirectory) {
            foreach ($GLOBALS['PIECE_RIGHT_Filter_Prefixes'] as $prefixAlias) {
                $filterClass = Piece_Right_Filter_Factory::_getFilterClass($filterName, $prefixAlias);
                if (Piece_Right_Filter_Factory::_loadFromDirectory($filterClass, $filterDirectory)) {
                    return $filterClass;
                }
            }
        }
    }

    // }}}
    // {{{ _getFilterClass()

    /**
     * Gets the class name for a given filter name with a prefix alias.
     *
     * @param string $filterName
     * @param string $prefixAlias
     * @return string
     */
    function _getFilterClass($filterName, $prefixAlias)
    {
        if ($prefixAlias) {
            return "{$prefixAlias}_{$filterName}";
        } else {
            return $filterName;
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
?>
