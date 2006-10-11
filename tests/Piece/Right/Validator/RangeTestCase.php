<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right_Validator_Range
 * @since      File available since Release 0.1.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Right/Validator/Range.php';

// {{{ Piece_Right_Validator_RangeTestCase

/**
 * TestCase for Piece_Right_Validator_Range
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right_Validator_Range
 * @since      File available since Release 0.1.0
 */
class Piece_Right_Validator_RangeTestCase extends PHPUnit_TestCase
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
        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('min' => 2));

        $this->assertTrue($validator->validate(3));

        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('max' => 5));

        $this->assertTrue($validator->validate(3));

        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('min' => 2, 'max' => 5));

        $this->assertTrue($validator->validate(3));
    }

    function testFailureToBeLessThan()
    {
        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('max' => 2));

        $this->assertFalse($validator->validate(3));
    }

    function testFailureToBeGraeterThan()
    {
        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('min' => 4));

        $this->assertFalse($validator->validate(3));
    }

    function testNotNumeric()
    {
        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('max' => 2147483647));

        $this->assertFalse($validator->validate("spam"));
    }
    
    function testNumericSuccess()
    {
        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('min' => -1, 'max' => 17));

        $this->assertTrue($validator->validate('0'));
        $this->assertTrue($validator->validate('0.5'));
        $this->assertTrue($validator->validate('.5'));
        $this->assertTrue($validator->validate('0x10'));
        $this->assertTrue($validator->validate('1e-1'));
        
        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('min' => 0, 'max' => 10.5));

        $this->assertTrue($validator->validate('.5'));
        $this->assertTrue($validator->validate('10.5'));
        $this->assertTrue($validator->validate('1E+1'));
        $this->assertTrue($validator->validate('0x0A'));
    }

    function testNumericFailure()
    {
        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('min' => -1, 'max' => 1));

        $this->assertFalse($validator->validate('2'));
        $this->assertFalse($validator->validate('1.1'));
        $this->assertFalse($validator->validate('1e+1'));
        $this->assertFalse($validator->validate('0x10'));
        
        $validator = &new Piece_Right_Validator_Range();
        $validator->setRules(array('min' => 1, 'max' => 10.5));

        $this->assertFalse($validator->validate('.5'));
        $this->assertFalse($validator->validate('10.6'));
        $this->assertFalse($validator->validate('2e+1'));
        $this->assertFalse($validator->validate('0x0B'));
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
