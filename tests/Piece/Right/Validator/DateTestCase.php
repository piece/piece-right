<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006, KUBO Atsuhiro <iteman@users.sourceforge.net>
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
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://iteman.typepad.jp/piece/
 * @see        Piece_Right_Validator_Date
 * @since      File available since Release 0.3.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Right/Validator/Date.php';
require_once 'Piece/Right/Results.php';

// {{{ Piece_Right_Validator_DateTestCase

/**
 * TestCase for Piece_Right_Validator_Date
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://iteman.typepad.jp/piece/
 * @see        Piece_Right_Validator_Date
 * @since      File available since Release 0.3.0
 */
class Piece_Right_Validator_DateTestCase extends PHPUnit_TestCase
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
     */
    /**#@-*/

    function testSuccess()
    {
        $validator = &new Piece_Right_Validator_Date();

        $this->assertTrue($validator->validate('1976-01-20'));

        $validator = &new Piece_Right_Validator_Date();
        $validator->setRules(array('pattern' => '/^(\d{4})(\d{2})(\d{2})$/',
                                   'patternYearPosition' => 1,
                                   'patternMonthPosition' => 2,
                                   'patternDayPosition' => 3)
                             );

        $this->assertTrue($validator->validate('19760120'));

        $validator = &new Piece_Right_Validator_Date();
        $validator->setRules(array('isJapaneseEra' => true,
                                   'pattern' => '/^(\d+)-(\d+)-(\d+)-(\d+)$/'));

        $this->assertTrue($validator->validate('51-01-20-3'));

        $validator = &new Piece_Right_Validator_Date();
        $validator->setRules(array('isJapaneseEra' => true,
                                   'pattern' => '/^(\d)(\d{2})(\d{2})(\d{2})$/',
                                   'patternEraPosition' => 1,
                                   'patternYearPosition' => 2,
                                   'patternMonthPosition' => 3,
                                   'patternDayPosition' => 4)
                             );

        $this->assertTrue($validator->validate('3510120'));
    }

    function testFailure()
    {
        $validator = &new Piece_Right_Validator_Date();

        $this->assertFalse($validator->validate('1976-02-30'));

        $validator = &new Piece_Right_Validator_Date();

        $this->assertFalse($validator->validate('19760120'));

        $validator = &new Piece_Right_Validator_Date();
        $validator->setRules(array('pattern' => '/^(\d{4})(\d{2})$'));

        $this->assertFalse(@$validator->validate('19760120'));

        $validator = &new Piece_Right_Validator_Date();
        $validator->setRules(array('isJapaneseEra' => true,
                                   'pattern' => '/^(\d+)-(\d+)-(\d+)-(\d+)$/'));

        $this->assertFalse($validator->validate('52-2-29-3'));

        $validator = &new Piece_Right_Validator_Date();
        $validator->setRules(array('isJapaneseEra' => true,
                                   'pattern' => '/^(\d+)-(\d+)-(\d+)-(\d+)$/'));

        $this->assertFalse($validator->validate('351120'));

        $validator = &new Piece_Right_Validator_Date();
        $validator->setRules(array('isJapaneseEra' => true));

        $this->assertFalse(@$validator->validate('3510120'));

        $validator = &new Piece_Right_Validator_Date();
        $validator->setRules(array('isJapaneseEra' => true,
                                   'pattern' => '/^(\d+)-(\d+)-(\d+)-(\d+)$/'));

        $this->assertFalse($validator->validate('51-2-29-10'));
    }

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
