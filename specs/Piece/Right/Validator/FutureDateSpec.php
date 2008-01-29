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

use Piece::Right::Validator::FutureDate;

require_once dirname(__FILE__) . '/../../../prepare.php';

// {{{ DescribeRightValidatorFutureDate

/**
 * Some specs for Piece::Right::Validator::FutureDate.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightValidatorFutureDate extends PHPSpec_Context
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
        $this->_validator = new FutureDate();
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
        $currentTime = time();

        $this->spec($this->_validator->validate(strftime('%Y-%m-%d',
                                                         mktime(0, 0, 0,
                                                                date('n', $currentTime),
                                                                date('j', $currentTime) + 1,
                                                                date('Y', $currentTime)))))
             ->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('allowCurrentDate' => true));

        $this->spec($this->_validator->validate(strftime('%Y-%m-%d',
                                                         mktime(0, 0, 0,
                                                                date('n', $currentTime),
                                                                date('j', $currentTime),
                                                                date('Y', $currentTime)))))
             ->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('pattern' => '/^(\d{4})(\d{2})(\d{2})$/',
                                          'patternYearPosition' => 1,
                                          'patternMonthPosition' => 2,
                                          'patternDayPosition' => 3)
                                    );

        $this->spec($this->_validator->validate(strftime('%Y%m%d',
                                                         mktime(0, 0, 0,
                                                                1,
                                                                20,
                                                                date('Y', $currentTime) + 1))))
             ->should->beTrue();
    }

    public function itShouldFail()
    {
        $this->spec($this->_validator->validate(strftime('%Y-%m-%d', time())))->should->beFalse();
        $this->spec($this->_validator->validate('19760120'))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('pattern' => '/^(\d{4})(\d{2})$'));

        $this->spec(@$this->_validator->validate('19760120'))->should->beFalse();
    }

    public function itShouldNotHaveTheYear2038Problem()
    {
        $oldTimezone = getenv('TZ');
        putenv('TZ=UTC');

        $this->spec($this->_validator->validate('2038-01-19'))->should->beTrue();
        $this->spec($this->_validator->validate('2038-01-20'))->should->beTrue();

        putenv("TZ=$oldTimezone");
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
