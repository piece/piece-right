<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 2.0.0
 */

use Piece::Right::Validator::Image;

require_once dirname(__FILE__) . '/../../../prepare.php';

// {{{ DescribeRightValidatorImage

/**
 * Some specs for Piece::Right::Validator::Image.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightValidatorImage extends PHPSpec_Context
{

    // {{{ constants

    const TEST_IMAGE_WIDTH = 175;
    const TEST_IMAGE_HEIGHT = 175;

    // }}}
    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    private $_validator;
    private $_images = array();

    /**#@-*/

    /**#@+
     * @access public
     */

    public function beforeAll()
    {
        $this->_validator = new Image();

        $imageDirectory = dirname(__FILE__) . '/' . basename(__FILE__, '.php');
        foreach (array('jpg' => 'jpeg',
                       'png' => 'png',
                       'tif' => 'tiff',
                       'gif' => 'gif',
                       'bmp' => 'bmp') as $ext => $mime
                 ) {
            $filename = "$imageDirectory/image.{$ext}";
            $this->_images[] = array('name'     => $filename,
                                     'type'     => "image/{$mime}",
                                     'size'     => filesize($filename),
                                     'tmp_name' => $filename,
                                     'error'    => 0
                                     );
        }
    }

    public function before()
    {
        $this->_validator->clear();
    }

    public function itShouldBeArrayable()
    {
        $this->spec($this->_validator)->should->beArrayable();
    }

    public function itShouldSucceed()
    {
        foreach ($this->_images as $target) {
            $size = $target['size'];
            $this->_validator->clear();
            $this->_validator->setRules(array('minSize' => $size,
                                              'maxSize' => $size,
                                              'mimetype'=>'image/.*')
                                        );

            $this->spec($this->_validator->validate($target))->should->beTrue();
        }
    }

    public function itShouldFail()
    {
        foreach ($this->_images as $target) {
            $size = $target['size'];
            $this->_validator->setRules(array('minSize' => $size + 1,
                                              'maxSize' => $size - 1)
                                        );

            $this->spec($this->_validator->validate($target))->should->beFalse();
        }
    }

    public function itShouldFailIfAFileIsNotAImage()
    {
        $file = array('name'     => __FILE__,
                      'type'     => 'image/jpeg',
                      'size'     => filesize(__FILE__),
                      'tmp_name' => __FILE__,
                      'error'    => 0
                      );

        $this->spec($this->_validator->validate($file))->should->beFalse();
    }

    public function itShouldSupportToValidateByWidth()
    {
        $this->_validator->setRules(array('minWidth' => self::TEST_IMAGE_WIDTH));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beTrue();
        }

        $this->_validator->clear();
        $this->_validator->setRules(array('maxWidth' => self::TEST_IMAGE_WIDTH));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beTrue();
        }

        $this->_validator->clear();
        $this->_validator->setRules(array('minWidth' => self::TEST_IMAGE_WIDTH + 1));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beFalse();
        }

        $this->_validator->clear();
        $this->_validator->setRules(array('maxWidth' => self::TEST_IMAGE_WIDTH - 1));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beFalse();
        }
    }

    public function itShouldSupportToValidateByHeight()
    {
        $this->_validator->setRules(array('minHeight' => self::TEST_IMAGE_HEIGHT));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beTrue();
        }

        $this->_validator->clear();
        $this->_validator->setRules(array('maxHeight' => self::TEST_IMAGE_HEIGHT));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beTrue();
        }

        $this->_validator->clear();
        $this->_validator->setRules(array('minHeight' => self::TEST_IMAGE_HEIGHT + 1));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beFalse();
        }

        $this->_validator->clear();
        $this->_validator->setRules(array('maxHeight' => self::TEST_IMAGE_HEIGHT - 1));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beFalse();
        }
    }

    public function itShouldSupportToValidateByMimetype()
    {
        $this->_validator->setRules(array('mimetype' => 'image/.*'));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beTrue();
        }

        $this->_validator->clear();
        $this->_validator->setRules(array('mimetype' => 'image/psd'));

        foreach ($this->_images as $target) {
            $this->spec($this->_validator->validate($target))->should->beFalse();
        }
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

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
