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
use Piece::Right::Filter::Common;
use Piece::Right::Exception;

// {{{ FilterFactory

/**
 * A factory class for creating filter objects.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class FilterFactory
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

    private static $_instances = array();
    private static $_namespaces = array('Piece::Right::Filter');

    /**#@-*/

    /**#@+
     * @access public
     * @static
     */

    // }}}
    // {{{ factory()

    /**
     * Creates a filter object from a filter name and a namespace.
     *
     * @param string $filterName
     * @return Piece::Right::Filter::Common
     * @throws Piece::Right::Exception
     */
    public static function factory($filterName)
    {
        while (true) {
            if (!array_key_exists($filterName, self::$_instances)) {
                foreach (self::$_namespaces as $namespace) {
                    $class = strlen($namespace) ?
                        "$namespace::$filterName" : $filterName;
                    if (class_exists($class)) {
                        $instance = new $class();
                        if (!($instance instanceof Common)) {
                            throw new Exception('Invalid filter $class, a filter must extend the Piece::Right::Filter::Common class.');
                        }

                        self::$_instances[$filterName] = $instance;
                        break 2;
                    }
                }

                throw new Exception("Unknown filter $filterName, be sure the filter exists and is loaded prior to use.");
            } else {
                break;
            }
        }

        return self::$_instances[$filterName];
    }

    // }}}
    // {{{ clearInstances()

    /**
     * Clears the filter instances.
     */
    public static function clearInstances()
    {
        self::$_instances = array();
    }

    // }}}
    // {{{ addNamespace()

    /**
     * Adds a namespace for a filter.
     *
     * @param string $namespace
     */
    public static function addNamespace($namespace)
    {
        array_unshift(self::$_namespaces, $namespace);
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

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
