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

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        Piece_Right_Error::pushCallback(create_function('$error', 'var_dump($error); return ' . PEAR_ERRORSTACK_DIE . ';'));
    }

    function tearDown()
    {
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
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addValidation('phone', 'Required');
        $dynamicConfig->addValidation('phone', 'Length', array('min' => 10, 'max' => 11));
        $right = &new Piece_Right(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__)
                                  );

        $this->assertTrue($right->validate('Example', $dynamicConfig));

        unset($_POST['country']);
        unset($_POST['phone']);
        unset($_POST['last_name']);
        unset($_POST['first_name']);
        unset($_SERVER['REQUEST_METHOD']);
    }

    function testFailureToValidate()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['first_name'] = 'Foo';
        $_POST['last_name'] = 'Bar';
        $_POST['phone'] = '012345678';
        $_POST['country'] = 'Japan';
        $dynamicConfig = &new Piece_Right_Config();
        $dynamicConfig->addValidation('phone', 'Required');
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
