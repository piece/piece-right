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

use Piece::Right::Validator::Regex;

require_once dirname(__FILE__) . '/../../../prepare.php';

// {{{ DescribeRightValidatorRegex

/**
 * Some specs for Piece::Right::Validator::Regex.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightValidatorRegex extends PHPSpec_Context
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

    private $_validator;

    /**#@-*/

    /**#@+
     * @access public
     */

    public function beforeAll()
    {
        $this->_validator = new Regex();
    }

    public function before()
    {
        $this->_validator->clear();
    }

    public function itShouldNotBeArrayable()
    {
        $this->spec($this->_validator)->shouldNot->beArrayable();
    }

    public function itShouldSucceed()
    {
        $this->_validator->setRules(array('pattern' => '/^\d+$/'));

        $this->spec($this->_validator->validate('12345'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('pattern' => '/^\w+$/'));

        $this->spec($this->_validator->validate('foo_bar_baz_12345'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('pattern' => '/^[a-z0-9]{10}$/i'));

        $this->spec($this->_validator->validate('eapUjdCpJ8'))->should->beTrue();
    }

    public function itShouldFail()
    {
        $this->_validator->setRules(array('pattern' => '/^\d+$/'));

        $this->spec($this->_validator->validate('foo_bar_baz_12345'))->should->beFalse();
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
