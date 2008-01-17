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

use Piece::Right::Validator::Numeric;

require_once dirname(__FILE__) . '/../../../prepare.php';

// {{{ DescribeRightValidatorNumeric

/**
 * Some specs for Piece::Right::Validator::Numeric.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightValidatorNumeric extends PHPSpec_Context
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
        $this->_validator = new Numeric();
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
        $this->_validator->setRules(array('allowDecimal' => true,
                                          'allowOctal'   => false,
                                          'allowHexadecimal' => false,
                                          'allowExponent' => false,
                                          'useInteger' => true,
                                          'useFloat' => false)
                                    );

        $this->spec($this->_validator->validate('1234'))->should->beTrue();
        $this->spec($this->_validator->validate('-123'))->should->beTrue();
        $this->spec($this->_validator->validate('+123'))->should->beTrue();
        $this->spec($this->_validator->validate('0123'))->should->beFalse();
        $this->spec($this->_validator->validate('-0123'))->should->beFalse();
        $this->spec($this->_validator->validate('+0123'))->should->beFalse();
        $this->spec($this->_validator->validate('0x1A'))->should->beFalse();
        $this->spec($this->_validator->validate('-0x1A'))->should->beFalse();
        $this->spec($this->_validator->validate('+0x1A'))->should->beFalse();
        $this->spec($this->_validator->validate('1.234'))->should->beFalse();
        $this->spec($this->_validator->validate('-1.234'))->should->beFalse();
        $this->spec($this->_validator->validate('+1.234'))->should->beFalse();
        $this->spec($this->_validator->validate('1.2e3'))->should->beFalse();
        $this->spec($this->_validator->validate('1.2e-3'))->should->beFalse();
        $this->spec($this->_validator->validate('1.2e+3'))->should->beFalse();
    }

    public function itShouldSupportToAllowOnlyFloats()
    {
        $this->_validator->setRules(array('allowDecimal' => false,
                                          'allowOctal'   => false,
                                          'allowHexadecimal' => false,
                                          'allowExponent' => false,
                                          'useInteger' => false,
                                          'useFloat' => true)
                                    );

        $this->spec($this->_validator->validate('1234'))->should->beFalse();
        $this->spec($this->_validator->validate('-123'))->should->beFalse();
        $this->spec($this->_validator->validate('+123'))->should->beFalse();
        $this->spec($this->_validator->validate('0123'))->should->beFalse();
        $this->spec($this->_validator->validate('-0123'))->should->beFalse();
        $this->spec($this->_validator->validate('+0123'))->should->beFalse();
        $this->spec($this->_validator->validate('0x1A'))->should->beFalse();
        $this->spec($this->_validator->validate('-0x1A'))->should->beFalse();
        $this->spec($this->_validator->validate('+0x1A'))->should->beFalse();

        $this->spec($this->_validator->validate('1.234'))->should->beTrue();
        $this->spec($this->_validator->validate('-1.234'))->should->beTrue();
        $this->spec($this->_validator->validate('+1.234'))->should->beTrue();

        $this->spec($this->_validator->validate('1.2e3'))->should->beFalse();
        $this->spec($this->_validator->validate('1.2e-3'))->should->beFalse();
        $this->spec($this->_validator->validate('1.2e+3'))->should->beFalse();
    }

    public function itShouldSupportIntegers()
    {
        $this->_validator->setRules(array('allowDecimal' => true,
                                          'allowOctal'   => true,
                                          'allowHexadecimal' => true,
                                          'allowExponent' => false,
                                          'useInteger' => true,
                                          'useFloat' => false)
                                    );

        $this->spec($this->_validator->validate('1234'))->should->beTrue();
        $this->spec($this->_validator->validate('-123'))->should->beTrue();
        $this->spec($this->_validator->validate('+123'))->should->beTrue();
        $this->spec($this->_validator->validate('0123'))->should->beTrue();
        $this->spec($this->_validator->validate('-0123'))->should->beTrue();
        $this->spec($this->_validator->validate('+0123'))->should->beTrue();
        $this->spec($this->_validator->validate('0x1A'))->should->beTrue();
        $this->spec($this->_validator->validate('-0x1A'))->should->beTrue();
        $this->spec($this->_validator->validate('+0x1A'))->should->beTrue();

        $this->spec($this->_validator->validate('1.234'))->should->beFalse();
        $this->spec($this->_validator->validate('-1.234'))->should->beFalse();
        $this->spec($this->_validator->validate('+1.234'))->should->beFalse();
        $this->spec($this->_validator->validate('1.2e3'))->should->beFalse();
        $this->spec($this->_validator->validate('1.2e-3'))->should->beFalse();
        $this->spec($this->_validator->validate('1.2e+3'))->should->beFalse();
    }

    public function itShouldSupportNumerics()
    {
        $this->_validator->setRules(array('allowDecimal' => true,
                                          'allowOctal'   => true,
                                          'allowHexadecimal' => true,
                                          'allowExponent' => true,
                                          'useInteger' => true,
                                          'useFloat' => true)
                                    );

        $this->spec($this->_validator->validate('1234'))->should->beTrue();
        $this->spec($this->_validator->validate('-123'))->should->beTrue();
        $this->spec($this->_validator->validate('+123'))->should->beTrue();
        $this->spec($this->_validator->validate('0123'))->should->beTrue();
        $this->spec($this->_validator->validate('-0123'))->should->beTrue();
        $this->spec($this->_validator->validate('+0123'))->should->beTrue();
        $this->spec($this->_validator->validate('0x1A'))->should->beTrue();
        $this->spec($this->_validator->validate('-0x1A'))->should->beTrue();
        $this->spec($this->_validator->validate('+0x1A'))->should->beTrue();
        $this->spec($this->_validator->validate('1.234'))->should->beTrue();
        $this->spec($this->_validator->validate('-1.234'))->should->beTrue();
        $this->spec($this->_validator->validate('+1.234'))->should->beTrue();
        $this->spec($this->_validator->validate('1.2e3'))->should->beTrue();
        $this->spec($this->_validator->validate('1.2e-3'))->should->beTrue();
        $this->spec($this->_validator->validate('1.2e+3'))->should->beTrue();
    }

    public function itShouldSupportExponents()
    {
        $this->_validator->setRules(array('allowDecimal' => false,
                                          'allowOctal'   => false,
                                          'allowHexadecimal' => false,
                                          'allowExponent' => true,
                                          'useInteger' => false,
                                          'useFloat' => false)
                                    );

        $this->spec($this->_validator->validate('1234'))->should->beTrue();
        $this->spec($this->_validator->validate('-123'))->should->beTrue();
        $this->spec($this->_validator->validate('+123'))->should->beTrue();

        $this->spec($this->_validator->validate('0123'))->should->beFalse();
        $this->spec($this->_validator->validate('-0123'))->should->beFalse();
        $this->spec($this->_validator->validate('+0123'))->should->beFalse();
        $this->spec($this->_validator->validate('0x1A'))->should->beFalse();
        $this->spec($this->_validator->validate('-0x1A'))->should->beFalse();
        $this->spec($this->_validator->validate('+0x1A'))->should->beFalse();

        $this->spec($this->_validator->validate('1.234'))->should->beTrue();
        $this->spec($this->_validator->validate('-1.234'))->should->beTrue();
        $this->spec($this->_validator->validate('+1.234'))->should->beTrue();
        $this->spec($this->_validator->validate('1.2e3'))->should->beTrue();
        $this->spec($this->_validator->validate('1.2e-3'))->should->beTrue();
        $this->spec($this->_validator->validate('1.2e+3'))->should->beTrue();
    }

    public function itShouldFailIfAValueIsNotANumeric()
    {
        $this->spec($this->_validator->validate('foo'))->should->beFalse();
        $this->spec($this->_validator->validate('12-34'))->should->beFalse();
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
