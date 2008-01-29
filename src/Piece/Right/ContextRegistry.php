<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 2.0.0
 */

namespace Piece::Right;
use Piece::Right::Context;

// {{{ ContextRegistry

/**
 * The registry for Context objects.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class ContextRegistry
{

    // {{{ constants

    const DEFAULT_CONTEXT_ID = '_Piece_Right_ContextRegistry_defaultContextID';

    // }}}
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

    private static $_contexts = array();
    private static $_contextID;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ getContext()

    /**
     * Gets the current Context object from the registry.
     *
     * @return Piece::Right::Context
     */
    public static function getContext()
    {
        return @self::$_contexts[ self::$_contextID ];
    }

    // }}}
    // {{{ addContext()

    /**
     * Adds a Context object to the registry.
     *
     * @param Piece::Right::Context $context
     */
    public static function addContext(Context $context)
    {
        self::$_contexts[ $context->getID() ] = $context;
    }

    // }}}
    // {{{ createContext()

    /**
     * Creates a new context with the given ID.
     *
     * @param string $id
     */
    public static function createContext($id = null)
    {
        if (is_null($id)) {
            $id = self::DEFAULT_CONTEXT_ID;
        }

        $context = new Context($id);
        ContextRegistry::addContext($context);
        self::setContextID($id);
    }

    // }}}
    // {{{ setContextID()

    /**
     * Sets the context ID.
     *
     * @param string $id
     */
    public static function setContextID($id)
    {
        self::$_contextID = $id;
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
