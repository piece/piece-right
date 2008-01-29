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

namespace Piece::Right::FilterFactory;
use Piece::Right::ContextRegistry;
use Piece::Right::Exception;
use Piece::Right::Filter::Common;

// {{{ Ghost

/**
 * A ghost class for FilterFactory.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class Ghost extends Stagehand::ObjectFactory::Ghost
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

    /**#@-*/

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    // }}}
    // {{{ _getContextID()

    /**
     * Gets the context ID for the factory.
     *
     * @return string
     */
    protected function _getContextID()
    {
        return ContextRegistry::getContext()->getID();
    }

    // }}}
    // {{{ _throwException()

    /**
     * Throws an exception if an action is not found.
     *
     * @param string $class
     * @throws Piece::Right::Exception
     */
    protected function _throwException($class)
    {
        throw new Exception("Unknown filter $class, be sure the filter exists and is loaded prior to use.");
    }

    // }}}
    // {{{ _getNamespaces()

    /**
     * Gets the namespaces for the factory.
     *
     * @return array
     */
    protected function _getNamespaces()
    {
        return array_merge(parent::_getNamespaces(), array('Piece::Right::Filter'));
    }

    // }}}
    // {{{ _afterInstantiation()

    /**
     * A callback which is called after instantiation.
     *
     * @param Piece::Right::Filter::Common $instance
     */
    protected function _afterInstantiation(Common $instance) {}

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
