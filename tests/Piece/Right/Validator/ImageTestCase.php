<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006 Chihiro Sakatoku <csakatoku@users.sourceforge.net>,
 *               2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2006 Chihiro Sakatoku <csakatoku@users.sourceforge.net>
 * @copyright  2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 1.3.0
 */

require_once realpath(dirname(__FILE__) . '/../../../prepare.php');
require_once 'PHPUnit.php';
require_once 'Piece/Right/Validator/Image.php';

// {{{ constants

define('TEST_IMAGE_WIDTH', 175);
define('TEST_IMAGE_HEIGHT', 175);

// }}}
// {{{ Piece_Right_Validator_ImageTestCase

/**
 * TestCase for Piece_Right_Validator_Image
 *
 * @package    Piece_Right
 * @copyright  2006 Chihiro Sakatoku <csakatoku@users.sourceforge.net>
 * @copyright  2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.3.0
 */
class Piece_Right_Validator_ImageTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_images;

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        $tmp = array();
        foreach (array('jpg' => 'jpeg',
                       'png' => 'png',
                       'tif' => 'tiff',
                       'gif' => 'gif',
                       'bmp' => 'bmp') as $ext => $mime
                 ) {
            $filename = dirname(__FILE__). "/images/image.{$ext}";
            $tmp[] = array('name'     => $filename,
                           'type'     => "image/{$mime}",
                           'size'     => filesize($filename),
                           'tmp_name' => $filename,
                           'error'    => 0
                           );
        }

        $this->_images = $tmp;
    }

    function testSuccess()
    {
        foreach ($this->_images as $target) {
            $size = $target['size'];
            $validator = &new Piece_Right_Validator_Image();
            $validator->setRules(array('minSize' => $size,
                                       'maxSize' => $size,
                                       'mimetype'=>'image/.*')
                                 );
            $this->assertTrue($validator->validate($target));
        }
    }

    function testFailure()
    {
        foreach ($this->_images as $target) {
            $size = $target['size'];
            $validator = &new Piece_Right_Validator_Image();
            $validator->setRules(array('minSize' => $size + 1,
                                       'maxSize' => $size - 1)
                                 );
            $this->assertFalse($validator->validate($target));
        }
    }

    function testNotImageFile()
    {
        $file = array('name'     => __FILE__,
                      'type'     => 'image/jpeg',
                      'size'     => filesize(__FILE__),
                      'tmp_name' => __FILE__,
                      'error'    => 0
                      );

        $validator = &new Piece_Right_Validator_Image();
        $this->assertFalse($validator->validate($file));
    }

    function testWidthSuccess()
    {
        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('minWidth' => TEST_IMAGE_WIDTH));

        foreach ($this->_images as $target) {
            $this->assertTrue($validator->validate($target));
        }

        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('maxWidth' => TEST_IMAGE_WIDTH));

        foreach ($this->_images as $target) {
            $this->assertTrue($validator->validate($target));
        }
    }

    function testWidthFailure()
    {
        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('minWidth' => TEST_IMAGE_WIDTH + 1));

        foreach ($this->_images as $target) {
            $this->assertFalse($validator->validate($target));
        }

        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('maxWidth' => TEST_IMAGE_WIDTH  - 1));

        foreach ($this->_images as $target) {
            $this->assertFalse($validator->validate($target));
        }
    }

    function testHeightSuccess()
    {
        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('minHeight' => TEST_IMAGE_HEIGHT));

        foreach ($this->_images as $target) {
            $this->assertTrue($validator->validate($target));
        }

        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('maxHeight' => TEST_IMAGE_HEIGHT));

        foreach ($this->_images as $target) {
            $this->assertTrue($validator->validate($target));
        }
    }

    function testHeightFailure()
    {
        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('minHeight' => TEST_IMAGE_HEIGHT + 1));

        foreach ($this->_images as $target) {
            $this->assertFalse($validator->validate($target));
        }

        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('maxHeight' => TEST_IMAGE_HEIGHT  - 1));

        foreach ($this->_images as $target) {
            $this->assertFalse($validator->validate($target));
        }
    }

    function testTypeSuccess()
    {
        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('mimetype' => 'image/.*'));

        foreach ($this->_images as $target) {
            $this->assertTrue($validator->validate($target));
        }
    }

    function testTypeFailure()
    {
        $validator = &new Piece_Right_Validator_Image();
        $validator->setRules(array('mimetype' => 'image/psd'));

        foreach ($this->_images as $target) {
            $this->assertFalse($validator->validate($target));
        }
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
