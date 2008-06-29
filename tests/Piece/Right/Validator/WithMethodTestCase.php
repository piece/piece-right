<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
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
 * @since      File available since Release 0.3.0
 */

require_once realpath(dirname(__FILE__) . '/../../../prepare.php');
require_once 'PHPUnit.php';
require_once 'Piece/Right/Validator/WithMethod.php';
require_once 'Piece/Right/Results.php';

// {{{ Piece_Right_Validator_WithMethodTestCase

/**
 * Some tests for Piece_Right_Validator_WithMethod.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.3.0
 */
class Piece_Right_Validator_WithMethodTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_oldIncludePath;

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        $this->_oldIncludePath = set_include_path(dirname(__FILE__) . '/' . basename(__FILE__, '.php') . PATH_SEPARATOR . get_include_path());
    }

    function tearDown()
    {
        set_include_path($this->_oldIncludePath);
        Piece_Right_Error::clearErrors();
    }

    function testSuccess()
    {
        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'WithMethod',
                                   'method' => 'isValid')
                             );

        $this->assertTrue($validator->validate('foo'));
        $this->assertTrue($validator->validate(array('foo', 'foo')));

        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'WithMethod',
                                   'method' => 'isValid',
                                   'isStatic' => true)
                             );

        $this->assertTrue($validator->validate('foo'));

        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'WithMethod',
                                   'method' => 'isFoo',
                                   'isStatic' => false)
                             );

        $this->assertTrue($validator->validate('foo'));

        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'WithMethod',
                                   'method' => 'isValidAndSetFoo',
                                   'isStatic' => false)
                             );
        $payload = &new StdClass();
        $validator->setPayload($payload);

        $this->assertTrue($validator->validate('foo'));
        $this->assertTrue(array_key_exists('foo', $payload));
        $this->assertEquals('foo', $payload->foo);
    }

    function testFailure()
    {
        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'WithMethod',
                                   'method' => 'isValid')
                             );

        $this->assertFalse($validator->validate('bar'));
        $this->assertFalse($validator->validate(array('foo', 'bar')));

        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'WithMethod',
                                   'method' => 'isValid',
                                   'isStatic' => true)
                             );

        $this->assertFalse($validator->validate('bar'));

        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'WithMethod',
                                   'method' => 'isFoo',
                                   'isStatic' => false)
                             );

        $this->assertFalse($validator->validate('bar'));

        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'WithMethod',
                                   'method' => 'isValidAndSetFoo',
                                   'isStatic' => false)
                             );
        $payload = &new StdClass();
        $validator->setPayload($payload);

        $this->assertFalse($validator->validate('bar'));
        $this->assertTrue(array_key_exists('foo', $payload));
        $this->assertEquals('foo', $payload->foo);
    }

    /**
     * @since Method available since Release 1.6.0
     */
    function testClassShouldBeLoadedAutomaticallyBySpecifyingDirectory()
    {
        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'Piece_Right_Validator_WithMethodTestCase_Foo',
                                   'method' => 'isFoo',
                                   'directory' => dirname(__FILE__) . '/' . basename(__FILE__, '.php'))
                             );

        $this->assertTrue($validator->validate('foo'));
        $this->assertTrue($validator->validate(array('foo', 'foo')));
    }

    /**
     * @since Method available since Release 1.6.0
     */
    function testExceptionShoudBeRaisedIfSpecifiedClassNotFound()
    {
        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setRules(array('class' => 'Piece_Right_Validator_WithMethodTestCase_Bar',
                                   'method' => 'isFoo',
                                   'directory' => dirname(__FILE__) . '/' . basename(__FILE__, '.php'))
                             );

        Piece_Right_Error::disableCallback();
        $result = $validator->validate('foo');
        Piece_Right_Error::enableCallback();

        $this->assertNull($result);
        $this->assertTrue(Piece_Right_Error::hasErrors());

        $error = Piece_Right_Error::pop();

        $this->assertEquals(PIECE_RIGHT_ERROR_NOT_FOUND, $error['code']);
    }

    /**
     * @since Method available since Release 1.6.0
     */
    function testResultsShouldBePassedToMethod()
    {
        $results = &new Piece_Right_Results();
        $results->setFieldValue('bar', 'baz');
        $validator = &new Piece_Right_Validator_WithMethod();
        $validator->setResults($results);
        $validator->setRules(array('class' => 'Piece_Right_Validator_WithMethodTestCase_Foo',
                                   'method' => 'compare',
                                   'directory' => dirname(__FILE__) . '/' . basename(__FILE__, '.php'))
                             );

        $this->assertTrue($validator->validate('baz'));
        $this->assertTrue(array_key_exists('foo', $results));
        $this->assertEquals('bar', $results->foo);
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
