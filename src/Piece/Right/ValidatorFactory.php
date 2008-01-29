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
use Piece::Right::Validator::Common;
use Stagehand::ObjectFactory;
use Piece::Right::ContextRegistry;

// {{{ ValidatorFactory

/**
 * A factory class for creating validator objects.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class ValidatorFactory extends ObjectFactory
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
    // {{{ _getFactoryClass()

    /**
     * Gets the class name of the factory.
     *
     * @return string
     */
    protected static function _getFactoryClass()
    {
        return __CLASS__;
    }

    // }}}
    // {{{ _getExceptionClass()

    /**
     * Gets the exception class for the factory.
     *
     * @return string
     */
    protected static function _getExceptionClass()
    {
        return __NAMESPACE__ . '::Exception';
    }

    // }}}
    // {{{ _getContextID()

    /**
     * Gets the context ID for the factory.
     *
     * @return string
     */
    protected static function _getContextID()
    {
        return ContextRegistry::getContext()->getID();
    }

    // }}}
    // {{{ _afterInstantiation()

    /**
     * A callback which is called after instantiation.
     *
     * @param Piece::Right::Validator::Common $instance
     */
    protected static function _afterInstantiation(Common $instance) {}

    // }}}
    // {{{ _existingInstance()

    /**
     * A callback which is called if an instance already exists.
     *
     * @param Piece::Right::Validator::Common $instance
     */
    protected static function _existingInstance(Common $instance)
    {
        $instance->clear();
    }

    // }}}
    // {{{ _getDefaultNamespaces()

    /**
     * Gets the default namespaces for classes.
     *
     * @return array
     */
    protected static function _getDefaultNamespaces()
    {
        return array('Piece::Right::Validator');
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
