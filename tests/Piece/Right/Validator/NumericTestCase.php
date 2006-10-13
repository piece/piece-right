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
 * @see        Piece_Right_Validator_Numeric
 * @since      File available since Release 1.3.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Right/Validator/Numeric.php';

// {{{ Piece_Right_Validator_NumericTestCase

/**
 * TestCase for Piece_Right_Validator_Numeric
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right_Validator_Numeric
 * @since      Class available since Release 1.3.0
 */
class Piece_Right_Validator_NumericTestCase extends PHPUnit_TestCase
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

    function testToAllowOnlyDecimal()
    {
        $validator = &new Piece_Right_Validator_Numeric();
        $validator->setRules(array('allowDecimal' => true,
                                   'allowOctal'   => false,
                                   'allowHexadecimal' => false,
                                   'allowExponent' => false,
                                   'useInteger' => true,
                                   'useFloat' => false)
                             );

        $this->assertTrue($validator->validate('1234'));
        $this->assertTrue($validator->validate('-123'));
        $this->assertTrue($validator->validate('+123'));
        $this->assertFalse($validator->validate('0123'));
        $this->assertFalse($validator->validate('-0123'));
        $this->assertFalse($validator->validate('+0123'));
        $this->assertFalse($validator->validate('0x1A'));
        $this->assertFalse($validator->validate('-0x1A'));
        $this->assertFalse($validator->validate('+0x1A'));
        $this->assertFalse($validator->validate('1.234'));
        $this->assertFalse($validator->validate('-1.234'));
        $this->assertFalse($validator->validate('+1.234'));
        $this->assertFalse($validator->validate('1.2e3'));
        $this->assertFalse($validator->validate('1.2e-3'));
        $this->assertFalse($validator->validate('1.2e+3'));
    }

    function testToAllowOnlyFloats()
    {
        $validator = &new Piece_Right_Validator_Numeric();
        $validator->setRules(array('allowDecimal' => false,
                                   'allowOctal'   => false,
                                   'allowHexadecimal' => false,
                                   'allowExponent' => false,
                                   'useInteger' => false,
                                   'useFloat' => true)
                             );

        $this->assertFalse($validator->validate('1234'));
        $this->assertFalse($validator->validate('-123'));
        $this->assertFalse($validator->validate('+123'));
        $this->assertFalse($validator->validate('0123'));
        $this->assertFalse($validator->validate('-0123'));
        $this->assertFalse($validator->validate('+0123'));
        $this->assertFalse($validator->validate('0x1A'));
        $this->assertFalse($validator->validate('-0x1A'));
        $this->assertFalse($validator->validate('+0x1A'));
        $this->assertTrue($validator->validate('1.234'));
        $this->assertTrue($validator->validate('-1.234'));
        $this->assertTrue($validator->validate('+1.234'));
        $this->assertFalse($validator->validate('1.2e3'));
        $this->assertFalse($validator->validate('1.2e-3'));
        $this->assertFalse($validator->validate('1.2e+3'));
    }

    function testIntegers()
    {
        $validator = &new Piece_Right_Validator_Numeric();
        $validator->setRules(array('allowDecimal' => true,
                                   'allowOctal'   => true,
                                   'allowHexadecimal' => true,
                                   'allowExponent' => false,
                                   'useInteger' => true,
                                   'useFloat' => false)
                             );

        $this->assertTrue($validator->validate('1234'));
        $this->assertTrue($validator->validate('-123'));
        $this->assertTrue($validator->validate('+123'));
        $this->assertTrue($validator->validate('0123'));
        $this->assertTrue($validator->validate('-0123'));
        $this->assertTrue($validator->validate('+0123'));
        $this->assertTrue($validator->validate('0x1A'));
        $this->assertTrue($validator->validate('-0x1A'));
        $this->assertTrue($validator->validate('+0x1A'));
        $this->assertFalse($validator->validate('1.234'));
        $this->assertFalse($validator->validate('-1.234'));
        $this->assertFalse($validator->validate('+1.234'));
        $this->assertFalse($validator->validate('1.2e3'));
        $this->assertFalse($validator->validate('1.2e-3'));
        $this->assertFalse($validator->validate('1.2e+3'));
    }

    function testNumeric()
    {
        $validator = &new Piece_Right_Validator_Numeric();
        $validator->setRules(array('allowDecimal' => true,
                                   'allowOctal'   => true,
                                   'allowHexadecimal' => true,
                                   'allowExponent' => true,
                                   'useInteger' => true,
                                   'useFloat' => true)
                             );

        $this->assertTrue($validator->validate('1234'));
        $this->assertTrue($validator->validate('-123'));
        $this->assertTrue($validator->validate('+123'));
        $this->assertTrue($validator->validate('0123'));
        $this->assertTrue($validator->validate('-0123'));
        $this->assertTrue($validator->validate('+0123'));
        $this->assertTrue($validator->validate('0x1A'));
        $this->assertTrue($validator->validate('-0x1A'));
        $this->assertTrue($validator->validate('+0x1A'));
        $this->assertTrue($validator->validate('1.234'));
        $this->assertTrue($validator->validate('-1.234'));
        $this->assertTrue($validator->validate('+1.234'));
        $this->assertTrue($validator->validate('1.2e3'));
        $this->assertTrue($validator->validate('1.2e-3'));
        $this->assertTrue($validator->validate('1.2e+3'));
    }

    function testExponent()
    {
        $validator = &new Piece_Right_Validator_Numeric();
        $validator->setRules(array('allowDecimal' => false,
                                   'allowOctal'   => false,
                                   'allowHexadecimal' => false,
                                   'allowExponent' => true,
                                   'useInteger' => false,
                                   'useFloat' => false)
                             );

        $this->assertTrue($validator->validate('1234'));
        $this->assertTrue($validator->validate('-123'));
        $this->assertTrue($validator->validate('+123'));
        $this->assertFalse($validator->validate('0123'));
        $this->assertFalse($validator->validate('-0123'));
        $this->assertFalse($validator->validate('+0123'));
        $this->assertFalse($validator->validate('0x1A'));
        $this->assertFalse($validator->validate('-0x1A'));
        $this->assertFalse($validator->validate('+0x1A'));
        $this->assertTrue($validator->validate('1.234'));
        $this->assertTrue($validator->validate('-1.234'));
        $this->assertTrue($validator->validate('+1.234'));
        $this->assertTrue($validator->validate('1.2e3'));
        $this->assertTrue($validator->validate('1.2e-3'));
        $this->assertTrue($validator->validate('1.2e+3'));
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
