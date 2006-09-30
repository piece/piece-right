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
 * @see        Piece_Right_Validation_Script
 * @since      File available since Release 1.2.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Right/Validation/Script.php';
require_once 'Piece/Right/Error.php';
require_once 'Cache/Lite/File.php';
require_once 'Piece/Right/Config.php';

// {{{ Piece_Right_Validation_ScriptTestCase

/**
 * TestCase for Piece_Right_Validation_Script
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right_Validation_Script
 * @since      File available since Release 1.2.0
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
    }

    function tearDown()
    {
        foreach (array_keys($this->_fields) as $name) {
            unset($_POST[$name]);
        }
        unset($_SERVER['REQUEST_METHOD']);
        $this->_postRunCallbackCalled = false;
        $this->_resultsViaCallback = null;
        $cache = &new Cache_Lite_File(array('cacheDir' => dirname(__FILE__) . '/',
                                            'masterFile' => '',
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );
        $cache->clean();
        Piece_Right_Error::clearErrors();
    }

    function testSuccessToValidate()
    {
        $config = &new Piece_Right_Config();
        $config->addValidation('email', 'Email');
        $container = &new stdClass();
        $script = &new Piece_Right_Validation_Script(dirname(__FILE__),
                                                     dirname(__FILE__),
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
        $script = &new Piece_Right_Validation_Script(dirname(__FILE__),
                                                     dirname(__FILE__),
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

    function turnOnPostRunCallbackCalled($validationSet, $results)
    {
        $this->_postRunCallbackCalled = true;
        $this->_resultsViaCallback = $results;

        $this->assertTrue('Script', $validationSet);
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
