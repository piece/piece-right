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
 * @since      File available since Release 0.1.0
 */

require_once realpath(dirname(__FILE__) . '/../../../prepare.php');
require_once 'PHPUnit.php';
require_once 'Piece/Right/Config/Factory.php';
require_once 'Piece/Right/Error.php';
require_once 'Cache/Lite/File.php';
require_once 'PEAR/ErrorStack.php';

// {{{ GLOBALS

$GLOBALS['PIECE_RIGHT_Config_FactoryTestCase_hasWarnings'] = false;

// }}}
// {{{ Piece_Right_Config_FactoryTestCase

/**
 * Some tests for Piece_Right_Config_Factory.
 *
 * @package    Piece_Right
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_Config_FactoryTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_cacheDirectory;

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        PEAR_ErrorStack::setDefaultCallback(create_function('$error', 'var_dump($error); return ' . PEAR_ERRORSTACK_DIE . ';'));
        $this->_cacheDirectory = dirname(__FILE__) . '/' . basename(__FILE__, '.php');
    }

    function tearDown()
    {
        $cache = &new Cache_Lite_File(array('cacheDir' => "{$this->_cacheDirectory}/",
                                            'masterFile' => '',
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );
        $cache->clean();
        Piece_Right_Error::clearErrors();
    }

    function testCreate()
    {
        $this->assertTrue(is_a(Piece_Right_Config_Factory::factory(),
                               'Piece_Right_Config')
                          );
    }

    function testCreateUsingConfigurationFile()
    {
        $config = &Piece_Right_Config_Factory::factory('Example',
                                                       $this->_cacheDirectory,
                                                       $this->_cacheDirectory
                                                       );
        $this->assertTrue(is_a($config, 'Piece_Right_Config'));

        $validations = $config->getValidations('first_name');

        $this->assertTrue(is_array($validations));
        $this->assertTrue($config->isRequired('first_name'));
        $this->assertEquals('Length', $validations[0]['validator']);
        $this->assertEquals(array('min' => 1, 'max' => 255), $validations[0]['rules']);
        $this->assertEquals('foo', $config->getRequiredMessage('first_name'));
        $this->assertEquals('bar', $validations[0]['message']);

        $validations = $config->getValidations('last_name');

        $this->assertTrue($config->isRequired('last_name'));
        $this->assertEquals('Length', $validations[0]['validator']);
        $this->assertEquals(array('min' => 1, 'max' => 255), $validations[0]['rules']);
        $this->assertEquals('baz', $config->getRequiredMessage('last_name'));
        $this->assertEquals('bar', $validations[0]['message']);

        $validations = $config->getValidations('country');

        $this->assertFalse($config->isRequired('country'));
        $this->assertEquals('Length', $validations[0]['validator']);
        $this->assertEquals(array('min' => 1, 'max' => 255), $validations[0]['rules']);
        $this->assertNull($validations[0]['message']);
    }

    function testCreateIfConfigurationDirectoryNotFound()
    {
        Piece_Right_Error::disableCallback();
        $config = &Piece_Right_Config_Factory::factory('Example',
                                                       "{$this->_cacheDirectory}/foo",
                                                       $this->_cacheDirectory
                                                       );
        Piece_Right_Error::enableCallback();

        $this->assertNull($config);
        $this->assertTrue(Piece_Right_Error::hasErrors());

        $error = Piece_Right_Error::pop();

        $this->assertEquals(PIECE_RIGHT_ERROR_NOT_FOUND, $error['code']);
    }

    function testCreateIfConfigurationFileNotFound()
    {
        Piece_Right_Error::disableCallback();
        $config = &Piece_Right_Config_Factory::factory('Example',
                                                       dirname(__FILE__),
                                                       $this->_cacheDirectory
                                                       );
        Piece_Right_Error::enableCallback();

        $this->assertNull($config);
        $this->assertTrue(Piece_Right_Error::hasErrors());

        $error = Piece_Right_Error::pop();

        $this->assertEquals(PIECE_RIGHT_ERROR_NOT_FOUND, $error['code']);
    }

    function testNotCacheIfCacheDirectoryNotFound()
    {
        set_error_handler(create_function('$code, $message, $file, $line', "
if (\$code == E_USER_WARNING) {
    \$GLOBALS['PIECE_RIGHT_Config_FactoryTestCase_hasWarnings'] = true;
}
"));
        $config = &Piece_Right_Config_Factory::factory('Example',
                                                       $this->_cacheDirectory,
                                                       'foo'
                                                       );
        restore_error_handler();

        $this->assertTrue($GLOBALS['PIECE_RIGHT_Config_FactoryTestCase_hasWarnings']);
    }

    /**
     * @since Method available since Release 0.3.0
     */
    function testInvalidConfiguration()
    {
        Piece_Right_Error::disableCallback();
        Piece_Right_Config_Factory::factory('InvalidConfiguration',
                                            $this->_cacheDirectory
                                            );
        Piece_Right_Error::enableCallback();

        $this->assertTrue(Piece_Right_Error::hasErrors());

        $error = Piece_Right_Error::pop();

        $this->assertEquals(PIECE_RIGHT_ERROR_INVALID_CONFIGURATION, $error['code']);
    }

    /**
     * @since Method available since Release 1.8.0
     */
    function testCacheIDsShouldUniqueInOneCacheDirectory()
    {
        $oldDirectory = getcwd();
        chdir("{$this->_cacheDirectory}/CacheIDsShouldBeUniqueInOneCacheDirectory1");
        Piece_Right_Config_Factory::factory('CacheIDsShouldBeUniqueInOneCacheDirectory',
                                            '.',
                                            $this->_cacheDirectory
                                            );

        $this->assertEquals(1, $this->_getCacheFileCount($this->_cacheDirectory));

        chdir("{$this->_cacheDirectory}/CacheIDsShouldBeUniqueInOneCacheDirectory2");
        Piece_Right_Config_Factory::factory('CacheIDsShouldBeUniqueInOneCacheDirectory',
                                            '.',
                                            $this->_cacheDirectory
                                            );

        $this->assertEquals(2, $this->_getCacheFileCount($this->_cacheDirectory));

        chdir($oldDirectory);
    }

    /**
     * @since Method available since Release 1.8.0
     */
    function testTemplateShouldBeUsedIfFileIsSetAndBasedOnElementIsSpecified()
    {
        $config = &Piece_Right_Config_Factory::factory('TemplateShouldBeUsedIfFileIsSetAndBasedOnElementIsSpecified',
                                                       $this->_cacheDirectory,
                                                       $this->_cacheDirectory,
                                                       'Common'
                                                       );

        $fieldNames = $config->getFieldNames();

        $this->assertEquals(2, count($fieldNames));
        $this->assertContains('firstName', $fieldNames);
        $this->assertContains('lastName', $fieldNames);

        foreach (array('firstName', 'lastName') as $fieldName) {
            $this->assertTrue($config->isRequired($fieldName));

            $validations = $config->getValidations($fieldName);

            $this->assertEquals(1, count($validations));
            $this->assertEquals('Length', $validations[0]['validator']);
            $this->assertEquals(array('min' => 1, 'max' => 255), $validations[0]['rules']);
            $this->assertEquals('%_description% is required', $config->getRequiredMessage('firstName'));
            $this->assertEquals('The length of %_description% must be less than %max% characters', $validations[0]['message']);
        }
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    /**
     * @since Method available since Release 1.2.0
     */
    function _getCacheFileCount($directory)
    {
        $cacheFileCount = 0;
        if ($dh = opendir($directory)) {
            while (true) {
                $file = readdir($dh);
                if ($file === false) {
                    break;
                }

                if (filetype("$directory/$file") == 'file') {
                    if (preg_match('/^cache_.+/', $file)) {
                        ++$cacheFileCount;
                    }
                }
            }

            closedir($dh);
        }

        return $cacheFileCount;
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
