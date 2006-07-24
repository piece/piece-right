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
 * @see        Piece_Right_Config
 * @since      File available since Release 0.1.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Right/Config.php';
require_once 'Piece/Right/Error.php';

// {{{ Piece_Right_ConfigTestCase

/**
 * TestCase for Piece_Right_Config
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://iteman.typepad.jp/piece/
 * @see        Piece_Right_Config
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
        $this->_config->setRequired('first_name');
        $this->_config->addValidation('first_name', 'Length', array('max' => 255));
        $this->_config->setRequired('last_name');
        $this->_config->addValidation('last_name', 'Length', array('max' => 255));
        $validationSet = $this->_config->getValidationSet();

        $this->assertTrue($this->_config->isRequired('first_name'));
        $this->assertEquals('Length', $validationSet['first_name'][0]['validator']);
        $this->assertEquals(array('max' => 255), $validationSet['first_name'][0]['rules']);

        $this->assertTrue($this->_config->isRequired('last_name'));
        $this->assertEquals('Length', $validationSet['last_name'][0]['validator']);
        $this->assertEquals(array('max' => 255), $validationSet['last_name'][0]['rules']);
    }

    function testAddingValidationsWithMessages()
    {
        $this->_config->setRequired('first_name', 'foo');
        $this->_config->addValidation('first_name', 'Length', array('max' => 255), 'bar');
        $this->_config->setRequired('last_name', 'baz');
        $this->_config->addValidation('last_name', 'Length', array('max' => 255), 'qux');
        $this->_config->setRequired('country');
        $this->_config->addValidation('country', 'Length', array('max' => 255));
        $validationSet = $this->_config->getValidationSet();

        $this->assertEquals('foo', $this->_config->getRequiredMessage('first_name'));
        $this->assertEquals('bar', $validationSet['first_name'][0]['message']);
        $this->assertEquals('baz', $this->_config->getRequiredMessage('last_name'));
        $this->assertEquals('qux', $validationSet['last_name'][0]['message']);
        $this->assertNull($this->_config->getRequiredMessage('country'));
        $this->assertNull($validationSet['country'][0]['message']);
    }

    function testGettingFiledNames()
    {
        $this->_config->setRequired('foo');
        $this->_config->setRequired('bar');
        $this->_config->addValidation('bar', 'Length', array('max' => 255));
        $validationSet = $this->_config->getValidationSet();

        $this->assertEquals(array('foo', 'bar'), array_keys($validationSet));
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
        $dynamicConfig->addValidation('foo', 'Regex', array('regex' => '/^foo$/'));
        $this->_config->addFilter('foo', 'strtoupper');
        $this->_config->addValidation('foo', 'Length', array('max' => 255));
        $this->_config->addValidation('bar', 'Length', array('max' => 255));
        $this->_config->merge($dynamicConfig);
        $validationSet = $this->_config->getValidationSet();
        $requiredFields = $this->_config->getRequiredFields();
        $filters = $this->_config->getFilters();

        $this->assertEquals(array('foo', 'bar'), array_keys($validationSet));
        $this->assertEquals('Length', $validationSet['foo'][0]['validator']);
        $this->assertEquals('Regex', $validationSet['foo'][1]['validator']);
        $this->assertEquals('Length', $validationSet['bar'][0]['validator']);
        $this->assertEquals('Length', $validationSet['bar'][1]['validator']);
        $this->assertEquals(255, $validationSet['bar'][0]['rules']['max']);
        $this->assertEquals(5, $validationSet['bar'][1]['rules']['min']);
        $this->assertEquals(array('foo'), array_keys($requiredFields));
        $this->assertEquals(array('foo', 'bar'), array_keys($filters));
        $this->assertEquals('strtoupper', $filters['foo'][0]);
        $this->assertEquals('trim', $filters['foo'][1]);
        $this->assertEquals('trim', $filters['bar'][0]);
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
