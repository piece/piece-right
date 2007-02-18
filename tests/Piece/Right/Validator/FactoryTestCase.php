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
 * @see        Piece_Right_Validator_Factory
 */

require_once 'PHPUnit.php';
require_once 'Piece/Right/Validator/Factory.php';
require_once 'Piece/Right/Error.php';

// {{{ Piece_Right_Validator_FactoryTestCase

/**
 * TestCase for Piece_Right_Validator_Factory
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right_Validator_Factory
 */
class Piece_Right_Validator_FactoryTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_oldValidatorDirectories;
    var $_oldPrefixes;

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        Piece_Right_Error::pushCallback(create_function('$error', 'var_dump($error); return ' . PEAR_ERRORSTACK_DIE . ';'));
        $this->_oldValidatorDirectories = $GLOBALS['PIECE_RIGHT_Validator_Directories'];
        Piece_Right_Validator_Factory::addValidatorDirectory(dirname(__FILE__) . '/FactoryTestCase');
        $this->_oldPrefixes = $GLOBALS['PIECE_RIGHT_Validator_Prefixes'];
    }

    function tearDown()
    {
        $GLOBALS['PIECE_RIGHT_Validator_Prefixes'] = $this->_oldPrefixes;
        Piece_Right_Validator_Factory::clearInstances();
        $GLOBALS['PIECE_RIGHT_Validator_Directories'] = $this->_oldValidatorDirectories;
        Piece_Right_Error::clearErrors();
        Piece_Right_Error::popCallback();
    }

    function testFailureToCreateByNonExistingFile()
    {
        Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));

        Piece_Right_Validator_Factory::factory('NonExisting');

        $this->assertTrue(Piece_Right_Error::hasErrors('exception'));

        $error = Piece_Right_Error::pop();

        $this->assertEquals(PIECE_RIGHT_ERROR_NOT_FOUND, $error['code']);

        Piece_Right_Error::popCallback();
    }

    function testFailureToCreateByInvalidValidator()
    {
        Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));

        Piece_Right_Validator_Factory::factory('Invalid');

        $this->assertTrue(Piece_Right_Error::hasErrors('exception'));

        $error = Piece_Right_Error::pop();

        $this->assertEquals(PIECE_RIGHT_ERROR_INVALID_VALIDATOR, $error['code']);

        Piece_Right_Error::popCallback();
    }

    function testFactory()
    {
        $fooValidator = &Piece_Right_Validator_Factory::factory('FactoryTestCase_Foo');

        $this->assertTrue(is_a($fooValidator, 'Piece_Right_Validator_FactoryTestcase_Foo'));

        $barValidator = &Piece_Right_Validator_Factory::factory('FactoryTestCase_Bar');

        $this->assertTrue(is_a($barValidator, 'Piece_Right_Validator_FactoryTestCase_Bar'));

        $fooValidator->baz = 'qux';

        $validator = &Piece_Right_Validator_Factory::factory('FactoryTestCase_Foo');

        $this->assertTrue(array_key_exists('baz', $fooValidator));
    }

    /**
     * @since Method available since Release 1.5.0
     */
    function testAlias()
    {
        Piece_Right_Validator_Factory::addValidatorPrefix('FactoryTestCaseAlias');
        $foo = &Piece_Right_Validator_Factory::factory('Foo');

        $this->assertTrue(is_object($foo));
        $this->assertTrue(is_a($foo, 'FactoryTestCaseAlias_Foo'));
    }

    /**
     * @since Method available since Release 1.5.0
     */
    function testAliasWithEmptyPrefix()
    {
        Piece_Right_Validator_Factory::addValidatorPrefix('');
        $bar = &Piece_Right_Validator_Factory::factory('Bar');

        $this->assertTrue(is_object($bar));
        $this->assertTrue(is_a($bar, 'Bar'));
    }

    /**
     * @since Method available since Release 1.5.0
     */
    function testCreateExistingClass()
    {
        Piece_Right_Validator_Factory::addValidatorPrefix('FactoryTestCaseAlias');
        $foo = &Piece_Right_Validator_Factory::factory('FactoryTestCase_Foo');

        $this->assertTrue(is_object($foo));
        $this->assertFalse(is_a($foo, 'FactoryTestCaseAlias_FactoryTestCase_Foo'));
        $this->assertTrue(is_a($foo, 'Piece_Right_Validator_FactoryTestCase_Foo'));
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
