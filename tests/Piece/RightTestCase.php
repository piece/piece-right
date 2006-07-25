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
 * @link       http://iteman.typepad.jp/piece/
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

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        Piece_Right_Error::pushCallback(create_function('$error', 'var_dump($error); return ' . PEAR_ERRORSTACK_DIE . ';'));
        $this->_oldFilterDirectories = $GLOBALS['PIECE_RIGHT_Filter_Directories'];
        Piece_Right_Filter_Factory::addFilterDirectory(dirname(__FILE__) . '/..');
    }

    function tearDown()
    {
        $GLOBALS['PIECE_RIGHT_Filter_Instances'] = array();
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
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->setRequired('phone');
        $dynamicConfig->addValidation('phone', 'Length', array('min' => 10, 'max' => 11));
        $right = &new Piece_Right(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__)
                                  );

        $this->assertTrue($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

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
        $errorFields = $results->getErrorFields();
        foreach (array('first_name', 'hobbies', 'favorite_framework') as $field) {
            $this->assertTrue(in_array($field, $errorFields));
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
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addFilter('foo', 'LowerCase');
        $dynamicConfig->addFilter('foo', 'trim');
        $dynamicConfig->addValidation('foo', 'Length', array('min' => 5));
        $dynamicConfig->addFilter('bar', 'LowerCase');
        $dynamicConfig->addFilter('bar', 'trim');
        $dynamicConfig->addValidation('bar', 'Length', array('min' => 5));
        $right = &new Piece_Right();

        $this->assertFalse($right->validate('Example', $dynamicConfig));

        $results = &$right->getResults();

        $this->assertEquals(1, $results->countErrors());
        $this->assertFalse($results->isError('foo'));
        $this->assertTrue($results->isError('bar'));
        $this->assertEquals('this text is written in lower case', $results->getFieldValue('foo'));
        $this->assertEquals('this', $results->getFieldValue('bar'));

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
        $this->_assertWatchingFields('birthdayMonth', '1');
        $this->_assertWatchingFields('birthdayDay', '20');
        $this->_assertWatchingFields('birthdayYear', '1976');
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
            $this->assertTrue(in_array($field, $results->getErrorFields()));
        }

        unset($_POST['qux']);
        unset($_POST['foo']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    function _assertWatchingFields($name, $value)
    {
        $fields = array('birthdayYear' => true,
                        'birthdayMonth' => true,
                        'birthdayDay' => true
                        );
        unset($fields[$name]);
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

        $this->assertEquals(3, $results->countErrors());

        foreach (array_keys($fields) as $field) {
            $this->assertTrue(in_array($field, $results->getErrorFields()));
        }

        unset($_POST[$name]);
        unset($_POST['country']);
        unset($_POST['last_name']);
        unset($_POST['first_name']);
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
