<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @link       http://piece-framework.com/piece-right/
 * @since      File available since Release 0.3.0
 */

require_once 'Piece/Right/Error.php';

// {{{ GLOBALS

$GLOBALS['PIECE_RIGHT_Filter_Instances'] = array();
$GLOBALS['PIECE_RIGHT_Filter_Directories'] = array(dirname(__FILE__) . '/../../..');

// }}}
// {{{ Piece_Right_Filter_Factory

/**
 * A factory class for creating filter objects.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
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
     * @param string $filter
     * @return mixed
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     */
    function &factory($filter)
    {
        $filter = "Piece_Right_Filter_$filter";
        if (!array_key_exists($filter, $GLOBALS['PIECE_RIGHT_Filter_Instances'])) {
            $found = false;
            foreach ($GLOBALS['PIECE_RIGHT_Filter_Directories'] as $filterDirectory) {
                $found = Piece_Right_Filter_Factory::_load($filter, $filterDirectory);
                if ($found) {
                    break;
                }
            }

            if (!$found) {
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                        "The filter [ $filter ] not found in the following directories:\n" .
                                        implode("\n", $GLOBALS['PIECE_RIGHT_Filter_Directories'])
                                        );
                $return = null;
                return $return;
            }

            $instance = &new $filter();
            $GLOBALS['PIECE_RIGHT_Filter_Instances'][$filter] = &$instance;
        }

        return $GLOBALS['PIECE_RIGHT_Filter_Instances'][$filter];
    }

    // }}}
    // {{{ addFilterDirectory()

    /**
     * Adds a filter directory.
     *
     * @param string $directory
     * @return array
     */
    function addFilterDirectory($directory)
    {
        $oldDirectories = $GLOBALS['PIECE_RIGHT_Filter_Directories'];
        array_unshift($GLOBALS['PIECE_RIGHT_Filter_Directories'], $directory);
        return $oldDirectories;
    }

    // }}}
    // {{{ setFilterDirectories()

    /**
     * Sets the directories as the filter directories.
     *
     * @param array $directories
     * @return array
     */
    function setFilterDirectories($directories)
    {
        $oldDirectories = $GLOBALS['PIECE_RIGHT_Filter_Directories'];
        $GLOBALS['PIECE_RIGHT_Filter_Directories'] = $directories;
        return $oldDirectories;
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
    // {{{ getFilterDirectories()

    /**
     * Gets the filter directories.
     *
     * @return array
     */
    function getFilterDirectories()
    {
        return $GLOBALS['PIECE_RIGHT_Filter_Directories'];
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _load()

    /**
     * Loads a filter corresponding to the given filter name.
     *
     * @param string $filter
     * @param string $filterDirectory
     * @return boolean
     * @static
     */
    function _load($filter, $filterDirectory)
    {
        $file = "$filterDirectory/" . str_replace('_', '/', $filter) . '.php';

        if (!file_exists($file)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The filter file [ $file ] for the class [ $filter ] not found.",
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

        if (version_compare(phpversion(), '5.0.0', '<')) {
            $found = class_exists($filter);
        } else {
            $found = class_exists($filter, false);
        }

        return $found;
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
