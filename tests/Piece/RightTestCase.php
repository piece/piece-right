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
 * @see        Piece_Right
 * @since      File available since Release 0.1.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Right.php';
require_once 'Piece/Right/Error.php';
require_once 'Piece/Right/Config.php';
require_once 'Cache/Lite/File.php';
require_once 'Piece/Right/Filter/Factory.php';

// {{{ Piece_RightTestCase

/**
 * TestCase for Piece_Right
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right
 * @since      Class available since Release 0.1.0
 */
class Piece_RightTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_oldFilterDirectories;
    var $_oldValidatorDirectories;

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        Piece_Right_Error::pushCallback(create_function('$error', 'var_dump($error); return ' . PEAR_ERRORSTACK_DIE . ';'));
        $this->_oldFilterDirectories = $GLOBALS['PIECE_RIGHT_Filter_Directories'];
        Piece_Right_Filter_Factory::addFilterDirectory(dirname(__FILE__) . '/..');
        $this->_oldValidatorDirectories = $GLOBALS['PIECE_RIGHT_Validator_Directories'];
        Piece_Right_Validator_Factory::addValidatorDirectory(dirname(__FILE__) . '/..');
    }

    function tearDown()
    {
        Piece_Right_Validator_Factory::clearInstances();
        $GLOBALS['PIECE_RIGHT_Validator_Directories'] = $this->_oldValidatorDirectories;
        Piece_Right_Filter_Factory::clearInstances();
        $GLOBALS['PIECE_RIGHT_Filter_Directories'] = $this->_oldFilterDirectories;
        $cache = &new Cache_Lite_File(array('cacheDir' => dirname(__FILE__) . '/',
                                            'masterFile' => '',
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );
        $cache->clean();
        Piece_Right_Error::clearErrors();
        Piece_Right_Error::popCallback();
    }

    function testSuccessToValidate()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['first_name'] = 'Foo';
        $_POST['last_name'] = 'Bar';
        $_POST['phone'] = '0123456789';
        $_POST['country'] = 'Japan';
        $_POST['hobbies'] = array('wine', 'manga');
        $_POST['use_php'] = '1';
        $_POST['favorite_framework'] = 'Piece Framework';
        $_POST['birthdayYear'] = '1976';
        $_POST['birthdayMonth'] = '1';
        $_POST['birthdayDay'] = '20';
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('phone');
        $dynamicConfig->addValidation('phone', 'Length', array('min' => 10, 'max' => 11));
        $right = &new Piece_Right(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__)
                                  );

        $this->assertTrue($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

        unset($_POST['birthdayDay']);
        unset($_POST['birthdayMonth']);
        unset($_POST['birthdayYear']);
        unset($_POST['favorite_framework']);
        unset($_POST['use_php']);
        unset($_POST['hobbies']);
        unset($_POST['country']);
        unset($_POST['phone']);
        unset($_POST['last_name']);
        unset($_POST['first_name']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    function testFailureToValidate()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['first_name'] = ' Foo ';
        $_POST['last_name'] = 'Bar';
        $_POST['phone'] = '012345678';
        $_POST['country'] = 'Japan';
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('phone');
        $dynamicConfig->addValidation('phone', 'Length', array('min' => 10, 'max' => 11));
        $right = &new Piece_Right(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__)
                                  );

        $this->assertFalse($right->validate('Example', $dynamicConfig));

        unset($_POST['country']);
        unset($_POST['phone']);
        unset($_POST['last_name']);
        unset($_POST['first_name']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    function testGettingErrorInformation()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['last_name'] = 'Bar';
        $_POST['phone'] = '0123456789';
        $_POST['country'] = 'Japan';
        $_POST['use_php'] = '1';
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('phone');
        $dynamicConfig->addValidation('phone', 'Length', array('min' => 10, 'max' => 11));
        $right = &new Piece_Right(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__)
                                  );

        $this->assertFalse($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

        $this->assertEquals(3, $results->countErrors());

        foreach (array('first_name', 'hobbies', 'favorite_framework') as $field) {
            $this->assertTrue(in_array($field, $results->getErrorFields()), "The field [ $field ] is expected.");
        }

        $this->assertTrue($results->isError('first_name'));
        $this->assertFalse($results->isError('last_name'));
        $this->assertFalse($results->isError('phone'));
        $this->assertFalse($results->isError('country'));
        $this->assertTrue($results->isError('hobbies'));
        $this->assertTrue($results->isError('favorite_framework'));
        $this->assertEquals('foo', $results->getErrorMessage('first_name'));
        $this->assertEquals(array('foo'), $results->getErrorMessages('first_name'));

        unset($_POST['use_php']);
        unset($_POST['country']);
        unset($_POST['phone']);
        unset($_POST['last_name']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testFieldIsNotRequired()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['bar'] = 'baz';
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addValidation('foo', 'Length', array('min' => 5));
        $dynamicConfig->addValidation('bar', 'Length', array('min' => 5));
        $right = &new Piece_Right();

        $this->assertFalse($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

        $this->assertEquals(1, $results->countErrors());
        $this->assertFalse($results->isError('foo'));
        $this->assertTrue($results->isError('bar'));

        unset($_POST['baz']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testFiltersWithDynamicConfiguration()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = ' THIS TEXT IS WRITTEN IN LOWER CASE ';
        $_POST['bar'] = ' THIS ';
        $_POST['baz'] = array(' FOO ', array(' BAR '), 'baz' => ' BAZ ');
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addFilter('foo', 'LowerCase');
        $dynamicConfig->addFilter('foo', 'trim');
        $dynamicConfig->addValidation('foo', 'Length', array('min' => 5));
        $dynamicConfig->addFilter('bar', 'LowerCase');
        $dynamicConfig->addFilter('bar', 'trim');
        $dynamicConfig->addValidation('bar', 'Length', array('min' => 5));
        $dynamicConfig->addFilter('baz', 'LowerCase');
        $dynamicConfig->addFilter('baz', 'trim');
        $dynamicConfig->addValidation('baz', 'Length', array('min' => 5));
        $right = &new Piece_Right();

        $this->assertFalse($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

        $this->assertEquals(2, $results->countErrors());
        $this->assertFalse($results->isError('foo'));
        $this->assertTrue($results->isError('bar'));
        $this->assertTrue($results->isError('baz'));
        $this->assertEquals('this text is written in lower case', $results->getFieldValue('foo'));
        $this->assertEquals('this', $results->getFieldValue('bar'));

        $baz = $results->getFieldValue('baz');

        $this->assertEquals('foo', $baz[0]);

        $baz1 = $baz[1];

        $this->assertEquals('bar', $baz1[0]);
        $this->assertEquals('baz', $baz['baz']);

        unset($_POST['baz']);
        unset($_POST['bar']);
        unset($_POST['foo']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testFilters()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['first_name'] = ' THIS TEXT IS WRITTEN IN LOWER CASE ';
        $_POST['use_php'] = '1';
        $_POST['favorite_framework'] = 'Piece Framework';
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addFilter('first_name', 'strtolower');
        $right = &new Piece_Right(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__)
                                  );

        $this->assertFalse($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

        $this->assertEquals(2, $results->countErrors());
        $this->assertFalse($results->isError('foo'));
        $this->assertEquals('this text is written in lower case', $results->getFieldValue('first_name'));

        unset($_POST['favorite_framework']);
        unset($_POST['use_php']);
        unset($_POST['foo']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testWatchingFields()
    {
        $this->_assertWatchingFields('birthdayMonth', '1', array('birthdayDay', 'birthdayYear'));
        $this->_assertWatchingFields('birthdayDay', '20', array('birthdayMonth', 'birthdayYear'));
        $this->_assertWatchingFields('birthdayYear', '1976', array('birthdayMonth', 'birthdayDay'));
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testMultipleValues()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['checkboxes'] = array('foo', 'bar');
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('checkboxes');
        $dynamicConfig->addValidation('checkboxes', 'List', array('elements' => array('foo', 'bar', 'baz')));
        $right = &new Piece_Right();

        $this->assertTrue($right->validate('Example', $dynamicConfig));

        unset($_POST['checkboxes']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testWatchingFieldsWithTriggers()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = 'a';
        $_POST['qux'] = '6';
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('foo');
        $dynamicConfig->setRequired('qux');
        $dynamicConfig->setWatcher('bar',
                                   array('target' => array(array('name' => 'foo',
                                                                 'trigger' => array('comparisonOperator' => '==', 'comparisonTo' => 'a'))))
                                   );
        $dynamicConfig->setWatcher('baz',
                                   array('target' => array(array('name' => 'qux',
                                                                 'trigger' => array('comparisonOperator' => '>', 'comparisonTo' => '5'))))
                                   );

        $right = &new Piece_Right();

        $this->assertFalse($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

        $this->assertEquals(2, $results->countErrors());

        foreach (array('bar', 'baz') as $field) {
            $this->assertTrue(in_array($field, $results->getErrorFields()), "The field [ $field ] is expected.");
        }

        unset($_POST['qux']);
        unset($_POST['foo']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testProblemThatNonRequiredFieldsCannotBeTurnedRequiredOn()
    {
        $this->_assertProblemThatNonRequiredFieldsCannotBeTurnedRequiredOn('param1', array('param2', 'param3'));
        $this->_assertProblemThatNonRequiredFieldsCannotBeTurnedRequiredOn('param2', array('param1', 'param3'));
        $this->_assertProblemThatNonRequiredFieldsCannotBeTurnedRequiredOn('param3', array('param1', 'param2'));
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testRuleMessage()
    {
        $dynamicConfig = &new Piece_Right_Config();
        $this->_assertRuleMessage('a', 'The value is too short.', $dynamicConfig);

        $dynamicConfig = &new Piece_Right_Config();
        $this->_assertRuleMessage('abcdefghijk', 'The value is too long.', $dynamicConfig);

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addValidation('foo', 'Length',
                                      array('min' => 5,
                                            'min_message' => 'The value is too short.',
                                            'max' => 10,
                                            'max_message' => 'The value is too long.')
                                      );
        $this->_assertRuleMessage('a', 'The value is too short.', $dynamicConfig);

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addValidation('foo', 'Length',
                                      array('min' => 5,
                                            'min_message' => 'The value is too short.',
                                            'max' => 10,
                                            'max_message' => 'The value is too long.')
                                      );
        $this->_assertRuleMessage('abcdefghijk', 'The value is too long.', $dynamicConfig);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testForceValidationBasedOnWatcher()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['has_home_phone'] = 2;
        $_POST['homePhone1'] = '';
        $_POST['homePhone2'] = '';
        $_POST['homePhone3'] = '';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertTrue($right->validate('ForceValidationBasedOnWatcher'));

        unset($_POST['homePhone3']);
        unset($_POST['homePhone2']);
        unset($_POST['homePhone1']);
        unset($_POST['has_home_phone']);
        unset($_SERVER['REQUEST_METHOD']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['has_home_phone'] = 2;
        $_POST['homePhone1'] = '1111';
        $_POST['homePhone2'] = '';
        $_POST['homePhone3'] = '';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertFalse($right->validate('ForceValidationBasedOnWatcher'));

        $results = &$right->getResults();

        $this->assertEquals(3, $results->countErrors());

        foreach (array('home_phone', 'homePhone2', 'homePhone3') as $field) {
            $this->assertTrue(in_array($field, $results->getErrorFields()), "The field [ $field ] is expected.");
        }

        $this->assertEquals('Please input all fields of the home phone.', $results->getErrorMessage('home_phone'));
        $this->assertEquals('This field is required.', $results->getErrorMessage('homePhone2'));
        $this->assertEquals('This field is required.', $results->getErrorMessage('homePhone3'));

        unset($_POST['homePhone3']);
        unset($_POST['homePhone2']);
        unset($_POST['homePhone1']);
        unset($_POST['has_home_phone']);
        unset($_SERVER['REQUEST_METHOD']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['has_home_phone'] = 2;
        $_POST['homePhone1'] = '1111';
        $_POST['homePhone2'] = '2222';
        $_POST['homePhone3'] = '3333';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertTrue($right->validate('ForceValidationBasedOnWatcher'));

        unset($_POST['homePhone3']);
        unset($_POST['homePhone2']);
        unset($_POST['homePhone1']);
        unset($_POST['has_home_phone']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testAppropriateValidationMessage()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = '10';
        $_POST['bar'] = '10';
        $_POST['baz'] = '0';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertFalse($right->validate('AppropriateValidationMessage'));

        $results = &$right->getResults();

        $this->assertEquals('Please select an element.', $results->getErrorMessage('foo'));
        $this->assertEquals('The value is too big.', $results->getErrorMessage('bar'));
        $this->assertEquals('The value is too small.', $results->getErrorMessage('baz'));

        unset($_POST['baz']);
        unset($_POST['bar']);
        unset($_POST['foo']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testPseudoField()
    {
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('emailUser');
        $dynamicConfig->addFilter('emailUser', 'strtolower');
        $dynamicConfig->setRequired('emailHost');
        $dynamicConfig->addFilter('emailHost', 'strtolower');
        $dynamicConfig->setRequired('email');
        $dynamicConfig->addValidation('email',
                                      'Regex',
                                      array('pattern' => '/^[^@]+@.+$/')
                                      );
        $dynamicConfig->setPseudo('email',
                                  array('format' => '%s@%s',
                                        'arg' => array('emailUser',
                                                       'emailHost'))
                                  );

        $this->_assertPseudoField(true, $dynamicConfig);
        $this->_assertPseudoField(false, new Piece_Right_Config());
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testMessageVariable()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['age'] = '18';
        $_POST['phone'] = '01234567891';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertFalse($right->validate('MessageVariable'));

        $results = &$right->getResults();

        $this->assertEquals(3, $results->countErrors());
        $this->assertEquals('[Last Name] is required.',
                            $results->getErrorMessage('last_name')
                            );
        $this->assertEquals('[age] is must greater than 20.',
                            $results->getErrorMessage('age')
                            );
        $this->assertEquals('[phone] is must less than 10.',
                            $results->getErrorMessage('phone')
                            );

        unset($_POST['phone']);
        unset($_POST['age']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testForceValidation()
    {
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('password', array('message' => '[%_name%] is required.'));
        $dynamicConfig->addValidation('password',
                                      'Length',
                                      array('min' => 6),
                                      'The password is invalid.'
                                      );
        $dynamicConfig->setForceValidation('password');

        $this->_assertForceValidation(true, $dynamicConfig);
        $this->_assertForceValidation(false, new Piece_Right_Config());
    }

    /**
     * @since Method available since Release 0.4.0
     */
    function testProblemThatValidationOfPseudoFieldsAreAlwaysInvoked()
    {
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addField('homePhone1');
        $dynamicConfig->addField('homePhone2');
        $dynamicConfig->addField('homePhone3');
        $dynamicConfig->setRequired('home_phone',
                                    array('enabled' => false,
                                          'message' => 'These fields are required.')
                                    );
        $dynamicConfig->addValidation('home_phone',
                                      'Regex',
                                      array('pattern' => '/^\d{1,4}-\d{1,4}-\d{1,4}$/'),
                                      'Please input all fields of the home phone.'
                                      );
        $dynamicConfig->setPseudo('home_phone',
                                  array('format' => '%s-%s-%s',
                                        'arg' => array('homePhone1',
                                                       'homePhone2',
                                                       'homePhone3'))
                                  );
        $dynamicConfig->setWatcher('home_phone',
                                   array('target' => array(array('name' => 'has_home_phone',
                                                                 'trigger' => array('comparisonOperator' => '==', 'comparisonTo' => '1')),
                                                           array('name' => 'has_home_phone',
                                                                 'trigger' => array('comparisonOperator' => '==', 'comparisonTo' => '3')),
                                                           array('name' => 'homePhone1'),
                                                           array('name' => 'homePhone2'),
                                                           array('name' => 'homePhone3')),
                                         'turnOnForceValidation' => true)
                                   );
        $dynamicConfig->setRequired('has_home_phone',
                                    array('message' => 'This field is required.')
                                    );
        $dynamicConfig->addValidation('has_home_phone',
                                      'Range',
                                      array('min' => 1, 'max' => 3),
                                      'Please choice from these radios.'
                                      );

        $this->_assertProblemThatValidationOfPseudoFieldsAreAlwaysInvoked(false, $dynamicConfig);
        $this->_assertProblemThatValidationOfPseudoFieldsAreAlwaysInvoked(false, new Piece_Right_Config());
    }

    /**
     * @since Method available since Release 0.4.0
     */
    function testValidFieldNames()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = 'foo';
        $_POST['bar'] = '';
        $_POST['baz'] = '0';

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('foo');
        $dynamicConfig->setRequired('bar');
        $dynamicConfig->addField('baz');
        $right = &new Piece_Right();

        $this->assertFalse($right->validate(null, $dynamicConfig));

        $results = &$right->getResults();
        foreach (array('bar') as $field) {
            $this->assertTrue(in_array($field, $results->getErrorFields()), "The field [ $field ] is expected.");
        }

        foreach (array('foo', 'baz') as $field) {
            $this->assertTrue(in_array($field, $results->getValidFields()), "The field [ $field ] is expected.");
        }

        unset($_POST['baz']);
        unset($_POST['bar']);
        unset($_POST['foo']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.5.0
     */
    function testPayload()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = 'bar';
        $payload = &new stdClass();
        $payload->validatorCalled = false;

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addValidation('foo', 'PayloadTest');
        $right = &new Piece_Right();
        $right->setPayload($payload);

        $this->assertTrue($right->validate(null, $dynamicConfig));

        $results = &$right->getResults();

        $this->assertTrue($payload->validatorCalled);

        unset($_POST['foo']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 1.0.0
     */
    function testSeparatedDateValidationWithPseudoFieldIfPseudoFieldIsRequired()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertFalse($right->validate('SeparatedDateValidationWithPseudoFieldIfPseudoFieldIsRequired'));
        $results = &$right->getResults();
        $errorFields = $results->getErrorFields();

        $this->assertEquals(1, count($errorFields));
        $this->assertContains('birthday', $errorFields);
        $this->assertEquals('[birthday] is required.', $results->getErrorMessage('birthday'));

        unset($_SERVER['REQUEST_METHOD']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['year'] = '1976';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertFalse($right->validate('SeparatedDateValidationWithPseudoFieldIfPseudoFieldIsRequired'));

        $results = &$right->getResults();
        $errorFields = $results->getErrorFields();

        $this->assertEquals(1, count($errorFields));
        $this->assertContains('birthday', $errorFields);
        $this->assertEquals('[birthday] is required.', $results->getErrorMessage('birthday'));

        unset($_POST['year']);
        unset($_SERVER['REQUEST_METHOD']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['year'] = '1976';
        $_POST['month'] = '1';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertFalse($right->validate('SeparatedDateValidationWithPseudoFieldIfPseudoFieldIsRequired'));

        $results = &$right->getResults();
        $errorFields = $results->getErrorFields();

        $this->assertEquals(1, count($errorFields));
        $this->assertContains('birthday', $errorFields);
        $this->assertEquals('[birthday] is required.', $results->getErrorMessage('birthday'));

        unset($_POST['month']);
        unset($_POST['year']);
        unset($_SERVER['REQUEST_METHOD']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['year'] = '1976';
        $_POST['month'] = '1';
        $_POST['day'] = '20';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertTrue($right->validate('SeparatedDateValidationWithPseudoFieldIfPseudoFieldIsRequired'));

        unset($_POST['day']);
        unset($_POST['month']);
        unset($_POST['year']);
        unset($_SERVER['REQUEST_METHOD']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['year'] = '1977';
        $_POST['month'] = '2';
        $_POST['day'] = '29';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertFalse($right->validate('SeparatedDateValidationWithPseudoFieldIfPseudoFieldIsRequired'));

        $results = &$right->getResults();
        $errorFields = $results->getErrorFields();

        $this->assertEquals(1, count($errorFields));
        $this->assertContains('birthday', $errorFields);
        $this->assertEquals('[birthday] is invalid.', $results->getErrorMessage('birthday'));

        unset($_POST['day']);
        unset($_POST['month']);
        unset($_POST['year']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 1.0.0
     */
    function testSeparatedDateValidationWithPseudoFieldIfPseudoFieldIsNotRequired()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertTrue($right->validate('SeparatedDateValidationWithPseudoFieldIfPseudoFieldIsNotRequired'));

        unset($_SERVER['REQUEST_METHOD']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['year'] = '1976';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertFalse($right->validate('SeparatedDateValidationWithPseudoFieldIfPseudoFieldIsNotRequired'));

        $results = &$right->getResults();
        $errorFields = $results->getErrorFields();

        $this->assertEquals(1, count($errorFields));
        $this->assertContains('birthday', $errorFields);
        $this->assertEquals('[birthday] is invalid.', $results->getErrorMessage('birthday'));

        unset($_POST['year']);
        unset($_SERVER['REQUEST_METHOD']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['year'] = '1976';
        $_POST['month'] = '1';
        $_POST['day'] = '20';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertTrue($right->validate('SeparatedDateValidationWithPseudoFieldIfPseudoFieldIsNotRequired'));

        unset($_POST['day']);
        unset($_POST['month']);
        unset($_POST['year']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function testFileUploadSuccess()
    {
        $size = filesize(__FILE__);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_FILES['userfile'] = array('tmp_name' => __FILE__,
                                    'name'     => __FILE__,
                                    'size'     => $size,
                                    'type'     => 'text/plain',
                                    'error'    => 0
                                    );

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('userfile');
        $dynamicConfig->addValidation('userfile',
                                      'File',
                                      array('maxSize'  => $size,
                                            'minSize'  => $size,
                                            'mimetype' => 'text')
                                      );
        $right = &new Piece_Right();

        $this->assertTrue($right->validate(null, $dynamicConfig));

        unset($_FILES['userfile']);

        for ($i = 0; $i < 5; ++$i) {
            $_FILES['userfile']['tmp_name'][$i] = __FILE__;
            $_FILES['userfile']['name'][$i] = __FILE__;
            $_FILES['userfile']['type'][$i] = 'text/plain';
            $_FILES['userfile']['size'][$i] = $size;
            $_FILES['userfile']['error'][$i] = 0;
        }

        $this->assertTrue($right->validate(null, $dynamicConfig));

        unset($_FILES['userfile']);
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function testFileUploadFailure()
    {
        $size = filesize(__FILE__);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_FILES['userfile'] = array('tmp_name' => __FILE__,
                                    'name'     => __FILE__,
                                    'size'     => $size,
                                    'type'     => 'image/jpeg',
                                    'error'    => 0
                                    );

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('userfile');
        $dynamicConfig->addValidation('userfile',
                                      'File',
                                      array('maxSize' => $size - 1,
                                            'maxSize_message' => 'too large')
                                      );
        $right = &new Piece_Right();

        $this->assertFalse($right->validate(null, $dynamicConfig));

        $results = &$right->getResults();

        $this->assertTrue($results->isError('userfile'));
        $this->assertEquals('too large', $results->getErrorMessage('userfile'));

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('userfile');
        $dynamicConfig->addValidation('userfile',
                                      'File',
                                      array('minSize' => $size + 1,
                                            'minSize_message' => 'too small')
                                      );
        $right = &new Piece_Right();

        $this->assertFalse($right->validate(null, $dynamicConfig));

        $results = &$right->getResults();

        $this->assertTrue($results->isError('userfile'));
        $this->assertEquals('too small', $results->getErrorMessage('userfile'));

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('userfile');
        $dynamicConfig->addValidation('userfile',
                                      'File',
                                      array('maxSize' => $size,
                                            'maxSize_message' => 'too large',
                                            'mimetype' => 'text/.+'),
                                      'invalid file'
                                      );
        $right = &new Piece_Right();

        $this->assertFalse($right->validate(null, $dynamicConfig));

        $results = &$right->getResults();

        $this->assertTrue($results->isError('userfile'));
        $this->assertEquals('invalid file', $results->getErrorMessage('userfile'));

        unset($_FILES['userfile']);
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function testImageUploadSuccess()
    {
        $width  = 175;
        $height = 175;

        $imagedir = dirname(__FILE__). '/Right/Validator/images';
        $image = $imagedir.'/image.jpg';
        $size = filesize($image);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_FILES['userimage'] = array('tmp_name' => $image,
                                     'name'     => $image,
                                     'size'     => $size,
                                     'type'     => 'image/jpeg',
                                     'error'    => 0
                                     );

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('userimage');
        $dynamicConfig->addValidation('userimage',
                                      'Image',
                                      array('maxSize'   => $size,
                                            'minSize'   => $size,
                                            'maxWidth'  => $width,
                                            'minWidth'  => $width,
                                            'maxHeight' => $height,
                                            'minHeight' => $height,
                                            'mimetype'  => 'jpeg')
                                      );

        $right = &new Piece_Right();

        $this->assertTrue($right->validate(null, $dynamicConfig));

        unset($_FILES['userimage']);

        foreach (array('bmp', 'gif', 'jpg', 'png', 'tif') as $i => $ext) {
            $imagefile = "{$imagedir}/image.{$ext}";

            /*
             * the mime-type in the following array is not a valid mime-type,
             * but this is intentional. Our image validator should
             * ignore this value since mime-type in HTTP requests is not
             * trustworthy and we MUST check the uploaded data
             * by GD, or by some other ways which are equivalent to GD.
             */
            $_FILES['userimage']['tmp_name'][$i] = $imagefile;
            $_FILES['userimage']['name'][$i] = $imagefile;
            $_FILES['userimage']['type'][$i] = 'do/not/trust/this/value';
            $_FILES['userimage']['size'][$i] = filesize($imagefile);
            $_FILES['userimage']['error'][$i] = 0;
        }

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('userimage');
        $dynamicConfig->addValidation('userimage',
                                      'Image',
                                      array('maxWidth'  => $width,
                                            'minWidth'  => $width,
                                            'maxHeight' => $height,
                                            'minHeight' => $height,
                                            'mimetype'  =>'image/.+')
                                      );

        $this->assertTrue($right->validate(null, $dynamicConfig));

        unset($_FILES['userimage']);
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function testImageUploadFailure()
    {
        $width  = 175;
        $height = 175;

        $imagedir = dirname(__FILE__). '/Right/Validator/images';
        $image = $imagedir.'/image.jpg';
        $size = filesize($image);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_FILES['userimage'] = array('tmp_name' => $image,
                                     'name'     => $image,
                                     'size'     => $size,
                                     'type'     => 'image/jpeg',
                                     'error'    => 0
                                     );

        $rules = array(array('maxSize'=> $size - 1,
                             'maxSize_message' => 'too large size'),
                       array('minSize' => $size + 1,
                             'minSize_message' => 'too smalls size'),
                       array('maxWidth' => $width - 1,
                             'maxWidth_message' => 'too large width'),
                       array('minWidth' => $width + 1,
                             'minWidth_message' => 'too small width'),
                       array('maxHeight' => $height - 1,
                             'maxHeight_message' => 'too large height'),
                       array('minHeight' => $height + 1,
                             'minHeight_message' => 'too small height')
                       );

        $right = &new Piece_Right();

        foreach ($rules as $rule) {
            $dynamicConfig = &new Piece_Right_Config();
            $dynamicConfig->setRequired('userimage');
            $dynamicConfig->addValidation('userimage', 'Image', $rule);

            $this->assertFalse($right->validate(null, $dynamicConfig));

            $message = next($rule);

            $results = &$right->getResults();
            $this->assertTrue($results->isError('userimage'));
            $this->assertEquals($message, $results->getErrorMessage('userimage'));
        }

        unset($_FILES['userimage']);

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('userimage');
        $dynamicConfig->addValidation('userimage',
                                      'Image',
                                      array('mimetype'         => 'image/psd',
                                            'mimetype_message' => 'must be psd')
                                      );

        foreach (array('bmp', 'gif', 'jpg', 'png', 'tif') as $ext) {
            $imagefile = "{$imagedir}/image.{$ext}";
            $_FILES['userimage'] = array('tmp_name' => $imagefile,
                                         'name'     => $imagefile,
                                         'size'     => filesize($imagefile),
                                         'type'     => 'do/not/trust/this/value',
                                         'error'    => 0
                                         );

            $this->assertFalse($right->validate(null, $dynamicConfig));

            $results = &$right->getResults();

            $this->assertTrue($results->isError('userimage'));
            $this->assertTrue('must be psd', $results->getErrorMessages('userimage'));
        }

        unset($_FILES['userimage']);
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function testTurnOff()
    {
        $this->_assertTurnOff('', '', false);
        $this->_assertTurnOff('0123456789', '', true);
        $this->_assertTurnOff('', '0123456789', true);
        $this->_assertTurnOff('0123456789', '0123456789', true);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    /**
     * @since Method available since Release 0.3.0
     */
    function _assertWatchingFields($name, $value, $invalidFields)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['first_name'] = 'Foo';
        $_POST['last_name'] = 'Bar';
        $_POST['country'] = 'Japan';
        $_POST['hobbies'] = array('programming');
        $_POST[$name] = $value;

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('age');
        $right = &new Piece_Right(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__)
                                  );

        $this->assertFalse($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

        foreach ($invalidFields as $field) {
            $this->assertTrue(in_array($field, $results->getErrorFields()), "The field [ $field ] is expected.");
        }

        unset($_POST[$name]);
        unset($_POST['country']);
        unset($_POST['last_name']);
        unset($_POST['first_name']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function _assertProblemThatNonRequiredFieldsCannotBeTurnedRequiredOn($name, $invalidFields)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST[$name] = '1234';

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('param1', array('enabled' => false, 'message' => 'param1 is required'));
        $dynamicConfig->addValidation('param1', 'Regex', array('pattern' => '/^\d{1,4}$/', 'max' => 11), 'param1 validation error');
        $dynamicConfig->setWatcher('param1',
                                   array('target' => array(array('name' => 'param2'),
                                                           array('name' => 'param3')))
                                   );
        $dynamicConfig->addValidation('param2', 'Regex', array('pattern' => '/^\d{1,4}$/', 'max' => 11), 'param2 validation error');
        $dynamicConfig->addValidation('param3', 'Regex', array('pattern' => '/^\d{1,4}$/', 'max' => 11), 'param3 validation error');
        $right = &new Piece_Right();

        $this->assertFalse($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

        foreach ($invalidFields as $field) {
            $this->assertTrue(in_array($field, $results->getErrorFields()), "The field [ $field ] is expected.");
        }

        unset($_POST[$name]);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function _assertRuleMessage($fieldValue, $expectedMessage, &$dynamicConfig)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = $fieldValue;
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        $this->assertFalse($right->validate('RuleMessage'));

        $results = &$right->getResults();

        $this->assertEquals(1, $results->countErrors());

        foreach (array('foo') as $field) {
            $this->assertTrue(in_array($field, $results->getErrorFields()), "The field [ $field ] is expected.");
        }

        $this->assertEquals($expectedMessage, $results->getErrorMessage('foo'));

        unset($_POST['foo']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function _assertPseudoField($useDynamicConfiguration, &$dynamicConfig)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['emailUser'] = 'ITEMAN';
        $_POST['emailHost'] = 'USERS.SOURCEFORGE.NET';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        if ($useDynamicConfiguration) {
            $this->assertTrue($right->validate('PseudoField', $dynamicConfig));
        } else {
            $this->assertTrue($right->validate('PseudoField'));
        }

        $results = &$right->getResults();

        $this->assertEquals('iteman@users.sourceforge.net',
                            $results->getFieldValue('email')
                            );

        unset($_POST['emailHost']);
        unset($_POST['emailUser']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function _assertForceValidation($useDynamicConfiguration, &$dynamicConfig)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        if ($useDynamicConfiguration) {
            $this->assertFalse($right->validate('ForceValidation', $dynamicConfig));
        } else {
            $this->assertFalse($right->validate('ForceValidation'));
        }

        $results = &$right->getResults();

        $this->assertEquals('The password is invalid.',
                            $results->getErrorMessage('password')
                            );

        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 0.4.0
     */
    function _assertProblemThatValidationOfPseudoFieldsAreAlwaysInvoked($useDynamicConfiguration, &$dynamicConfig)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['has_home_phone'] = '2';
        $right = &new Piece_Right(dirname(__FILE__), dirname(__FILE__));

        if ($useDynamicConfiguration) {
            $this->assertTrue($right->validate('ProblemThatValidationOfPseudoFieldsAreAlwaysInvoked', $dynamicConfig));
        } else {
            $this->assertTrue($right->validate('ProblemThatValidationOfPseudoFieldsAreAlwaysInvoked'));
        }

        $results = &$right->getResults();
        foreach ($results->getErrorFields() as $field) {
            print "$field => " . $results->getFieldValue($field) . ': ' . $results->getErrorMessage($field) . "\n";
        }

        unset($_POST['has_home_phone']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function _assertTurnOff($homePhone, $mobilePhone, $result)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['home_phone'] = $homePhone;
        $_POST['mobile_phone'] = $mobilePhone;

        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('home_phone');
        $dynamicConfig->addValidation('home_phone', 'Length', array('min' => 10, 'max' => 11));
        $dynamicConfig->setRequired('mobile_phone');
        $dynamicConfig->addValidation('mobile_phone', 'Length', array('min' => 10, 'max' => 11));
        $dynamicConfig->setWatcher('home_phone',
                                   array('target' => array(array('name' => 'home_phone')),
                                         'turnOff' => array('mobile_phone'))
                                   );
        $dynamicConfig->setWatcher('mobile_phone',
                                   array('target' => array(array('name' => 'mobile_phone')),
                                         'turnOff' => array('home_phone'))
                                   );
        $right = &new Piece_Right();

        if ($result) {
            $this->assertTrue($right->validate(null, $dynamicConfig));
        } else {
            $this->assertFalse($right->validate(null, $dynamicConfig));

            $results = &$right->getResults();
            foreach (array('home_phone', 'mobile_phone') as $field) {
                $this->assertTrue(in_array($field, $results->getErrorFields()), "The field [ $field ] is expected.");
            }
        }

        unset($_POST['home_phone']);
        unset($_POST['mobile_phone']);
        unset($_SERVER['REQUEST_METHOD']);
    }

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
