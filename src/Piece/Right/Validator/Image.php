<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2006 Chihiro Sakatoku <csakatoku@users.sourceforge.net>,
 *               2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 1.3.0
 */

namespace Piece::Right::Validator;
use Piece::Right::Validator::File;

// {{{ Image

/**
 * A validator to check files are valid image files.
 *
 * @package    Piece_Right
 * @copyright  2006 Chihiro Sakatoku <csakatoku@users.sourceforge.net>
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.3.0
 */
class Image extends File
{

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

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ validate()

    /**
     * Validate the given file(s).
     *
     * @param array $value the array of uploaded file(s).
     * @return boolean true if passes, false if not.
     */
    public function validate($value)
    {
        if (!function_exists('getimagesize')) {
            return false;
        }

        if (parent::validate($value)) {
            return true;
        }

        return false;
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    // }}}
    // {{{ _initialize()

    /**
     * Initializes properties.
     */
    protected function _initialize()
    {
        parent::_initialize();
        $this->_addRule('maxWidth');
        $this->_addRule('minWidth', 0);
        $this->_addRule('maxHeight');
        $this->_addRule('minHeight', 0);
    }

    // }}}
    // {{{ _validateFile()

    /**
     * Validate a file.
     * Note that the 3rd parameter $mime will be ignored and
     * the mime-type of the given file is checked using
     * <code>getimagesize</code> function.
     *
     * @param string  $filename the name of the file to be validated.
     * @param integer $size     the file size.
     * @param string  $mime     the mime type which is retrieved
     *                          from HTTP request headers.
     * @return boolean true if the file passes the validation, false if not.
     */
    protected function _validateFile($filename, $size, $mime)
    {
        if (!is_file($filename) || !is_readable($filename)) {
           return false;
        }

        $info = getimagesize($filename);
        if ($info === false || !isset($info['mime'])) {
            return false;
        }

        list($width, $height, $typeFlag) = $info;
        $mime = $info['mime'];
        if (!$this->_validateMimeType($mime)) {
            $this->_setMessage('mimetype');
            return false;
        }

        foreach (array('size', 'width', 'height') as $rule) {
            if (!$this->_inRange($rule, $$rule)) {
                return false;
            }
        }

        return true;
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
