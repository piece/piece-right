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

use Piece::Right::Validator::Date;

require_once dirname(__FILE__) . '/../../../prepare.php';

// {{{ DescribeRightValidatorDate

/**
 * Some specs for Piece::Right::Validator::Date.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightValidatorDate extends PHPSpec_Context
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
        $this->_validator = new Date();
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
        $this->spec($this->_validator->validate('1976-01-20'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('pattern' => '/^(\d{4})(\d{2})(\d{2})$/',
                                          'patternYearPosition' => 1,
                                          'patternMonthPosition' => 2,
                                          'patternDayPosition' => 3)
                                    );

        $this->spec($this->_validator->validate('19760120'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('isJapaneseEra' => true,
                                          'pattern' => '/^(\d+)-(\d+)-(\d+)-(\d+)$/')
                                    );

        $this->spec($this->_validator->validate('51-01-20-3'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('isJapaneseEra' => true,
                                          'pattern' => '/^(\d)(\d{2})(\d{2})(\d{2})$/',
                                          'patternEraPosition' => 1,
                                          'patternYearPosition' => 2,
                                          'patternMonthPosition' => 3,
                                          'patternDayPosition' => 4)
                                    );

        $this->spec($this->_validator->validate('3510120'))->should->beTrue();
    }

    public function itShouldFail()
    {
        $this->spec($this->_validator->validate('1976-02-30'))->should->beFalse();
        $this->spec($this->_validator->validate('19760120'))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('pattern' => '/^(\d{4})(\d{2})$'));

        $this->spec(@$this->_validator->validate('19760120'))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('isJapaneseEra' => true,
                                          'pattern' => '/^(\d+)-(\d+)-(\d+)-(\d+)$/')
                                    );

        $this->spec($this->_validator->validate('52-2-29-3'))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('isJapaneseEra' => true,
                                          'pattern' => '/^(\d+)-(\d+)-(\d+)-(\d+)$/')
                                    );

        $this->spec($this->_validator->validate('351120'))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('isJapaneseEra' => true));

        $this->spec($this->_validator->validate('3510120'))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('isJapaneseEra' => true,
                                          'pattern' => '/^(\d+)-(\d+)-(\d+)-(\d+)$/')
                                    );

        $this->spec($this->_validator->validate('51-2-29-10'))->should->beFalse();
    }

    public function itShouldCheckRangeOfYearsWithJapaneseEra()
    {
        $this->_validator->setRules(array('isJapaneseEra' => true,
                                          'pattern' => '/^(\d+)-(\d+)-(\d+)-(\d+)$/')
                                    );

        $this->spec($this->_validator->validate('1-1-7-4'))->should->beFalse();
        $this->spec($this->_validator->validate('0-1-8-4'))->should->beFalse();
        $this->spec($this->_validator->validate('1-1-8-4'))->should->beTrue();

        $this->spec($this->_validator->validate('1-12-24-3'))->should->beFalse();
        $this->spec($this->_validator->validate('0-12-25-3'))->should->beFalse();
        $this->spec($this->_validator->validate('1-12-25-3'))->should->beTrue();
        $this->spec($this->_validator->validate('64-1-8-3'))->should->beFalse();
        $this->spec($this->_validator->validate('65-1-8-3'))->should->beFalse();
        $this->spec($this->_validator->validate('64-1-7-3'))->should->beTrue();

        $this->spec($this->_validator->validate('1-7-29-2'))->should->beFalse();
        $this->spec($this->_validator->validate('0-7-30-2'))->should->beFalse();
        $this->spec($this->_validator->validate('1-7-30-2'))->should->beTrue();
        $this->spec($this->_validator->validate('15-12-26-2'))->should->beFalse();
        $this->spec($this->_validator->validate('16-12-26-2'))->should->beFalse();
        $this->spec($this->_validator->validate('15-12-25-2'))->should->beTrue();

        $this->spec($this->_validator->validate('1-1-24-1'))->should->beFalse();
        $this->spec($this->_validator->validate('0-1-24-1'))->should->beFalse();
        $this->spec($this->_validator->validate('1-1-25-1'))->should->beTrue();
        $this->spec($this->_validator->validate('45-7-31-1'))->should->beFalse();
        $this->spec($this->_validator->validate('46-7-31-1'))->should->beFalse();
        $this->spec($this->_validator->validate('45-7-30-1'))->should->beTrue();
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
