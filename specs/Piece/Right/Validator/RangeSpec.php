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

use Piece::Right::Validator::Range;

require_once dirname(__FILE__) . '/../../../prepare.php';

// {{{ DescribeRightValidatorRange

/**
 * Some specs for Piece::Right::Validator::Range.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightValidatorRange extends PHPSpec_Context
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
        $this->_validator = new Range();
    }

    public function before()
    {
        $this->_validator->clear();
    }

    public function itShouldBeArrayable()
    {
        $this->_validator->setRules(array('min' => 2));

        $this->spec($this->_validator)->shouldNot->beArrayable();
        $this->spec($this->_validator->validate(array(3, 5)))->should->beTrue();
        $this->spec($this->_validator->validate(array(1, 3, 5)))->should->beFalse();
        $this->spec($this->_validator->validate(array()))->should->beTrue();
    }

    public function itShouldSucceed()
    {
        $this->_validator->setRules(array('min' => 2));

        $this->spec($this->_validator->validate(3))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('max' => 5));

        $this->spec($this->_validator->validate(3))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('min' => 2, 'max' => 5));

        $this->spec($this->_validator->validate(3))->should->beTrue();
    }

    public function itShouldFail()
    {
        $this->_validator->setRules(array('max' => 2));

        $this->spec($this->_validator->validate(3))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('min' => 4));

        $this->spec($this->_validator->validate(3))->should->beFalse();
    }

    public function itShouldSupportNumerics()
    {
        $this->_validator->setRules(array('min' => -1, 'max' => 17));

        $this->spec($this->_validator->validate('0'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('min' => -1,
                                          'max' => 17,
                                          'useFloat' => true)
                                    );

        $this->spec($this->_validator->validate('0.5'))->should->beTrue();
        $this->spec($this->_validator->validate('.5'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('min' => -1,
                                          'max' => 17,
                                          'allowHexadecimal' => true)
                                    );

        $this->spec($this->_validator->validate('0x10'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('min' => -1,
                                          'max' => 17,
                                          'allowExponent' => true)
                                    );

        $this->spec($this->_validator->validate('1e-1'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('min' => 0,
                                          'max' => 10.5,
                                          'useFloat' => true)
                                    );

        $this->spec($this->_validator->validate('.5'))->should->beTrue();
        $this->spec($this->_validator->validate('10.5'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('min' => 0,
                                          'max' => 10.5,
                                          'allowHexadecimal' => true,
                                          'allowExponent' => true)
                                    );

        $this->spec($this->_validator->validate('0x0A'))->should->beTrue();
        $this->spec($this->_validator->validate('1E+1'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('min' => -1, 'max' => 1));

        $this->spec($this->_validator->validate('2'))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('min' => -1,
                                          'max' => 1,
                                          'useFloat' => true)
                                    );

        $this->spec($this->_validator->validate('1.1'))->should->beFalse();

        $this->_validator->setRules(array('min' => -1,
                                          'max' => 1,
                                          'allowHexadecimal' => true,
                                          'allowExponent' => true)
                                    );

        $this->spec($this->_validator->validate('0x10'))->should->beFalse();
        $this->spec($this->_validator->validate('1e+1'))->should->beFalse();
        
        $this->_validator->setRules(array('min' => 1,
                                          'max' => 10.5,
                                          'useFloat' => true)
                                    );

        $this->spec($this->_validator->validate('0.5'))->should->beFalse();
        $this->spec($this->_validator->validate('10.6'))->should->beFalse();

        $this->_validator->setRules(array('min' => 1,
                                          'max' => 10.5,
                                          'allowHexadecimal' => true,
                                          'allowExponent' => true)
                                    );

        $this->spec($this->_validator->validate('0x0B'))->should->beFalse();
        $this->spec($this->_validator->validate('2e+1'))->should->beFalse();
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
