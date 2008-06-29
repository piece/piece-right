<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 1.2.0
 */

require_once realpath(dirname(__FILE__) . '/../../../prepare.php');
require_once 'PHPUnit.php';
require_once 'Piece/Right/Validation/Script.php';
require_once 'Piece/Right/Error.php';
require_once 'Cache/Lite/File.php';
require_once 'Piece/Right/Config.php';
require_once 'Piece/Right/Validator/Factory.php';

// {{{ Piece_Right_Validation_ScriptTestCase

/**
 * TestCase for Piece_Right_Validation_Script
 *
 * @package    Piece_Right
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.2.0
 */
class Piece_Right_Validation_ScriptTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_postRunCallbackCalled = false;
    var $_resultsViaCallback;
    var $_fields;
    var $_cacheDirectory;


    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->_fields = array('first_name' => ' Foo ',
                               'last_name' => ' Bar ',
                               'email' => 'baz@example.org',
                               );
        foreach ($this->_fields as $name => $value) {
            $_POST[$name] = $value;
        }
        $this->_cacheDirectory = dirname(__FILE__) . '/' . basename(__FILE__, '.php');
    }

    function tearDown()
    {
        foreach (array_keys($_POST) as $field) {
            unset($_POST[$field]);
        }
        unset($_SERVER['REQUEST_METHOD']);
        $this->_postRunCallbackCalled = false;
        $this->_resultsViaCallback = null;
        $cache = &new Cache_Lite_File(array('cacheDir' => "{$this->_cacheDirectory}/",
                                            'masterFile' => '',
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );
        $cache->clean();
        Piece_Right_Error::clearErrors();
    }

    function turnOnPostRunCallbackCalled($validationSet, &$results)
    {
        $this->_postRunCallbackCalled = true;
        $this->_resultsViaCallback = &$results;
    }

    function testSuccessToValidate()
    {
        $config = &new Piece_Right_Config();
        $config->addValidation('email', 'Email');
        $container = &new stdClass();
        $script = &new Piece_Right_Validation_Script($this->_cacheDirectory,
                                                     $this->_cacheDirectory,
                                                     null,
                                                     array(&$this, 'turnOnPostRunCallbackCalled')
                                                     );
        $results = $script->run('Script', $container, $config);

        $this->assertEquals(0, $results->countErrors());
        $this->assertTrue($this->_postRunCallbackCalled);
        $this->assertEquals($results, $this->_resultsViaCallback);

        foreach ($this->_fields as $name => $value) {
            $this->assertEquals(trim($value), $container->$name, $name);
        }
    }

    function testFailureToValidate()
    {
        $this->_fields['email'] = '';
        $_POST['email'] = '';
        $config = &new Piece_Right_Config();
        $config->addValidation('email', 'Email');
        $container = &new stdClass();
        $script = &new Piece_Right_Validation_Script($this->_cacheDirectory,
                                                     $this->_cacheDirectory,
                                                     null,
                                                     array(&$this, 'turnOnPostRunCallbackCalled')
                                                     );
        $results = $script->run('Script', $container, $config);

        $this->assertEquals(1, $results->countErrors());
        $this->assertTrue($this->_postRunCallbackCalled);
        $this->assertEquals($results, $this->_resultsViaCallback);

        foreach ($this->_fields as $name => $value) {
            $this->assertEquals($value, $container->$name, $name);
        }
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function testResultsByReference()
    {
        $config = &new Piece_Right_Config();
        $config->addValidation('email', 'Email');
        $container = &new stdClass();
        $script = &new Piece_Right_Validation_Script($this->_cacheDirectory,
                                                     $this->_cacheDirectory,
                                                     null,
                                                     array(&$this, 'turnOnPostRunCallbackCalled')
                                                     );
        $results = &$script->run('Script', $container, $config);
        $results->foo = 'bar';

        $this->assertTrue(array_key_exists('foo', $this->_resultsViaCallback));
        $this->assertEquals($results->foo, $this->_resultsViaCallback->foo);
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function testPayload()
    {
        $oldValidatorDirectories = $GLOBALS['PIECE_RIGHT_Validator_Directories'];
        Piece_Right_Validator_Factory::addValidatorDirectory($this->_cacheDirectory);
        $config = &new Piece_Right_Config();
        $config->addValidation('email', 'ScriptPayloadTest');
        $container = &new stdClass();
        $payload = &new stdClass();
        $script = &new Piece_Right_Validation_Script($this->_cacheDirectory,
                                                     $this->_cacheDirectory,
                                                     null,
                                                     array(&$this, 'turnOnPostRunCallbackCalled')
                                                     );
        $script->setPayload($payload);
        $results = $script->run('Script', $container, $config);

        $this->assertEquals(0, $results->countErrors());
        $this->assertTrue(array_key_exists('foo', $payload));
        $this->assertEquals('bar', $payload->foo);

        $GLOBALS['PIECE_RIGHT_Validator_Directories'] = $oldValidatorDirectories;
    }

    /**
     * @since Method available since Release 1.7.0
     */
    function testFieldNamesShouldBeAbleToGetByValidationSetAndConfiguration()
    {
        $config = &new Piece_Right_Config();
        $config->addValidation('bar', 'Length', array('min' => 1, 'max' => 255));
        $script = &new Piece_Right_Validation_Script($this->_cacheDirectory,
                                                     $this->_cacheDirectory,
                                                     null,
                                                     array(&$this, 'turnOnPostRunCallbackCalled')
                                                     );
        $fieldNames = $script->getFieldNames('FieldNames', $config);

        $this->assertEquals(2, count($fieldNames));
        $this->assertContains('foo', $fieldNames);
        $this->assertContains('bar', $fieldNames);
    }

    /**
     * @since Method available since Release 1.8.0
     */
    function testTemplateShouldBeUsedIfFileIsSetAndBasedOnElementIsSpecified()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['firstName'] = ' Foo ';
        $_POST['lastName'] = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

        $config = &new Piece_Right_Config();
        $container = &new stdClass();
        $script = &new Piece_Right_Validation_Script($this->_cacheDirectory,
                                                     $this->_cacheDirectory,
                                                     null,
                                                     array(&$this, 'turnOnPostRunCallbackCalled')
                                                     );
        $script->setTemplate('Common');
        $results = &$script->run('TemplateShouldBeUsedIfFileIsSetAndBasedOnElementIsSpecified', $container, $config);

        $this->assertEquals(1, $results->countErrors());

        $this->assertTrue(in_array('firstName', $results->getValidFields()));
        $this->assertTrue(in_array('lastName', $results->getErrorFields()));
        $this->assertEquals('Foo', $results->getFieldValue('firstName'));
        $this->assertEquals('The length of Last Name must be less than 255 characters', $results->getErrorMessage('lastName'));
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
