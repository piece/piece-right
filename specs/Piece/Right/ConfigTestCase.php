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
 * @since      File available since Release 0.1.0
 */

require_once realpath(dirname(__FILE__) . '/../../prepare.php');
require_once 'PHPUnit.php';
require_once 'Piece/Right/Config.php';
require_once 'Piece/Right/Error.php';

// {{{ Piece_Right_ConfigTestCase

/**
 * TestCase for Piece_Right_Config
 *
 * @package    Piece_Right
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_ConfigTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_config;

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        Piece_Right_Error::pushCallback(create_function('$error', 'var_dump($error); return ' . PEAR_ERRORSTACK_DIE . ';'));
        $this->_config = &new Piece_Right_Config();
    }

    function tearDown()
    {
        $this->_config = null;
        Piece_Right_Error::clearErrors();
        Piece_Right_Error::popCallback();
    }

    function testAddingValidations()
    {
        foreach (array('first_name', 'last_name') as $fieldName) {
            $this->_config->setRequired($fieldName);
            $this->_config->addValidation($fieldName, 'Length', array('max' => 255));
        }

        foreach (array('first_name', 'last_name') as $fieldName) {
            $validations = $this->_config->getValidations($fieldName);

            $this->assertTrue($this->_config->isRequired($fieldName));
            $this->assertEquals('Length', $validations[0]['validator']);
            $this->assertEquals(array('max' => 255), $validations[0]['rules']);
        }
    }

    function testAddingValidationsWithMessages()
    {
        $this->_config->setRequired('first_name', array('message' => 'foo'));
        $this->_config->addValidation('first_name', 'Length', array('max' => 255), 'bar');
        $this->_config->setRequired('last_name', array('message' => 'baz'));
        $this->_config->addValidation('last_name', 'Length', array('max' => 255), 'qux');
        $this->_config->setRequired('country');
        $this->_config->addValidation('country', 'Length', array('max' => 255));

        $validations = $this->_config->getValidations('first_name');

        $this->assertEquals('foo', $this->_config->getRequiredMessage('first_name'));
        $this->assertEquals('bar', $validations[0]['message']);

        $validations = $this->_config->getValidations('last_name');

        $this->assertEquals('baz', $this->_config->getRequiredMessage('last_name'));
        $this->assertEquals('qux', $validations[0]['message']);

        $validations = $this->_config->getValidations('country');

        $this->assertNull($this->_config->getRequiredMessage('country'));
        $this->assertNull($validations[0]['message']);
    }

    function testGettingFiledNames()
    {
        $this->_config->setRequired('foo');
        $this->_config->setRequired('bar');
        $this->_config->addValidation('bar', 'Length', array('max' => 255));
        $fieldNames = $this->_config->getFieldNames();

        $this->assertEquals(2, count($fieldNames));
        $this->assertContains('foo', $fieldNames);
        $this->assertContains('bar', $fieldNames);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testMergingConfigurations()
    {
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addFilter('bar', 'trim');
        $dynamicConfig->addValidation('bar', 'Length', array('min' => 5));
        $dynamicConfig->setRequired('foo');
        $dynamicConfig->addFilter('foo', 'trim');
        $dynamicConfig->addValidation('foo', 'Regex', array('pattern' => '/^foo$/'));
        $dynamicConfig->setWatcher('bar', array('target' => array('qux')));
        $dynamicConfig->setRequired('baz');
        $this->_config->addFilter('foo', 'strtoupper');
        $this->_config->addValidation('foo', 'Length', array('max' => 255));
        $this->_config->addValidation('bar', 'Length', array('max' => 255));
        $this->_config->setWatcher('foo', array('target' => array('baz')));
        $this->_config->setRequired('baz', array('message' => 'baz is required', 'enabled' => false));
        $this->_config->merge($dynamicConfig);

        $fieldNames = $this->_config->getFieldNames();

        $this->assertEquals(3, count($fieldNames));
        $this->assertContains('foo', $fieldNames);
        $this->assertContains('bar', $fieldNames);
        $this->assertContains('baz', $fieldNames);

        $validations = $this->_config->getValidations('foo');

        $this->assertEquals(2, count($validations));
        $this->assertEquals('Length', $validations[0]['validator']);
        $this->assertEquals('Regex', $validations[1]['validator']);

        $validations = $this->_config->getValidations('bar');

        $this->assertEquals(2, count($validations));
        $this->assertEquals('Length', $validations[0]['validator']);
        $this->assertEquals('Length', $validations[1]['validator']);
        $this->assertEquals(255, $validations[0]['rules']['max']);
        $this->assertEquals(5, $validations[1]['rules']['min']);

        $validations = $this->_config->getValidations('baz');

        $this->assertEquals(0, count($validations));

        $this->assertTrue($this->_config->isRequired('foo'));
        $this->assertFalse($this->_config->isRequired('bar'));
        $this->assertTrue($this->_config->isRequired('baz'));
        $this->assertEquals('baz is required', $this->_config->getRequiredMessage('baz'));

        $filters = $this->_config->getFilters('foo');

        $this->assertEquals(2, count($filters));
        $this->assertContains('strtoupper', $filters);
        $this->assertContains('trim', $filters);

        $filters = $this->_config->getFilters('bar');

        $this->assertEquals(1, count($filters));
        $this->assertContains('trim', $filters);

        $filters = $this->_config->getFilters('baz');

        $this->assertEquals(0, count($filters));

        $watcher = $this->_config->getWatcher('foo');

        $this->assertEquals('baz', $watcher['target'][0]);

        $watcher = $this->_config->getWatcher('bar');

        $this->assertEquals('qux', $watcher['target'][0]);
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
