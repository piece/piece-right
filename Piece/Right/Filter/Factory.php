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
 * @since      File available since Release 0.3.0
 */

require_once 'Piece/Right/Error.php';
require_once 'Piece/Right/ClassLoader.php';

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
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
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
     * @throws PIECE_RIGHT_ERROR_CANNOT_READ
     */
    function &factory($filterName)
    {
        if (!array_key_exists($filterName, $GLOBALS['PIECE_RIGHT_Filter_Instances'])) {
            $found = false;
            foreach ($GLOBALS['PIECE_RIGHT_Filter_Prefixes'] as $prefixAlias) {
                $filterClass = Piece_Right_Filter_Factory::_getFilterClass($filterName, $prefixAlias);
                if (Piece_Right_ClassLoader::loaded($filterClass)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                foreach ($GLOBALS['PIECE_RIGHT_Filter_Directories'] as $filterDirectory) {
                    foreach ($GLOBALS['PIECE_RIGHT_Filter_Prefixes'] as $prefixAlias) {
                        $filterClass = Piece_Right_Filter_Factory::_getFilterClass($filterName, $prefixAlias);

                        Piece_Right_Error::disableCallback();
                        Piece_Right_ClassLoader::load($filterClass, $filterDirectory);
                        Piece_Right_Error::enableCallback();

                        if (Piece_Right_Error::hasErrors()) {
                            $error = Piece_Right_Error::pop();
                            if ($error['code'] == PIECE_RIGHT_ERROR_NOT_FOUND) {
                                continue;
                            }

                            Piece_Right_Error::push(PIECE_RIGHT_ERROR_CANNOT_READ,
                                                    "Failed to read the filter [ $filterName ] for any reasons.",
                                                    'exception',
                                                    array(),
                                                    $error
                                                    );
                            $return = null;
                            return $return;
                        }

                        if (Piece_Right_ClassLoader::loaded($filterClass)) {
                            $found = true;
                            break 2;
                        }
                    }
                }

                if (!$found) {
                    Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                            "The filter [ $filterName ] not found in the following directories:\n" .
                                            implode("\n", $GLOBALS['PIECE_RIGHT_Filter_Directories'])
                                            );
                    $return = null;
                    return $return;
                }
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
