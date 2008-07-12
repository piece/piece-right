<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
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

require_once 'Piece/Right/Validator/Common.php';

// {{{ GLOBALS

$GLOBALS['PIECE_RIGHT_Validator_File_ErrorCodes'] = array('UPLOAD_ERR_INI_SIZE',
                                                          'UPLOAD_ERR_FORM_SIZE',
                                                          'UPLOAD_ERR_PARTIAL',
                                                          'UPLOAD_ERR_NO_FILE',
                                                          'UPLOAD_ERR_NO_TMP_DIR',
                                                          'UPLOAD_ERR_CANT_WRITE',
                                                          'UPLOAD_ERR_EXTENSION'
                                                          );

// }}}
// {{{ Piece_Right_Validator_File

/**
 * A validator which is used to validate a file.
 *
 * @package    Piece_Right
 * @copyright  2006 Chihiro Sakatoku <csakatoku@users.sourceforge.net>
 * @copyright  2007-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.3.0
 */
class Piece_Right_Validator_File extends Piece_Right_Validator_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_isArrayable = true;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ validate()

    /**
     * Validate given file(s).
     *
     * @param array $value the array of uploaded file(s).
     * @return boolean true if passes, false if not.
     */
    function validate($value)
    {
        if (!is_array($value)) {
            return false;
        }

        if (!array_key_exists('error', $value)
            || !array_key_exists('tmp_name', $value)
            || !array_key_exists('size', $value)
            || !array_key_exists('type', $value)
            ) {
            return false;
        }

        if (!is_array($value['error'])) {

            /*
             * create an new array,
             * array('name'=>array('a'), 'error'=>array(0)...)
             * from the given array,
             * array('name'=>'a', 'error'=>0, ...)
             */
            $value = array_map(create_function('$v', 'return array($v);'), $value);
        }

        for ($i = 0, $count = count($value['error']); $i < $count; ++$i) {
            if ($value['error'][$i] != UPLOAD_ERR_OK) {
                $this->_setMessageByErrorCode($value['error'][$i]);

                return false;
            }

            if (!$this->_validateFile($value['tmp_name'][$i], $value['size'][$i], $value['type'][$i])) {
                return false;
            }
        }

        return true;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _validateFile()

    /**
     * Validate a file.
     *
     * @param string  $filename the name of the file to be validated.
     * @param integer $size     the file size.
     * @param string  $mime     the mime type which is retrieved
     *                          from HTTP request headers.
     * @return boolean true if the file passes the validation, false if not.
     * @see Piece_Right_Validator_File
     */
    function _validateFile($filename, $size, $mime)
    {
        if (!$this->_inRange('size', $size)) {
            return false;
        }

        $useMagic = $this->_getRule('useMagic');
        if ($useMagic) {
            $mime = $this->_detectMimeType($filename);
            if ($mime === false) {
                return false;
            }
        }

        if (!$this->_validateMimeType($mime)) {
            $this->_setMessage('mimetype');
            return false;
        }

        return true;
    }

    // }}}
    // {{{ _inRange()

    /**
     * Utility function to check the given numeric value is in range.
     *
     * @param string $key   the prefix of the rule.
     * @param string $value the numberic string to be compared.
     * @return boolean true if the value in range, false if not.
     */
    function _inRange($key, $value)
    {
        $key = ucfirst($key);

        $max = $this->_getRule("max{$key}");
        if (!is_null($max)) {
            if ($value > $max) {
                $this->_setMessage("max{$key}");
                return false;
            }
        }

        $min = $this->_getRule("min{$key}");
        if (!is_null($min)) {
            if ($value < $min) {
                $this->_setMessage("min{$key}");
                return false;
            }
        }

        return true;
    }

    // }}}
    // {{{ _validateMimeType()

    /**
     * Validate the given mime-type.
     *
     * @param $mime string the mime-type.
     * @return boolean true if the pattern maches, false if not.
     */
    function _validateMimeType($mime)
    {
        $pattern = $this->_getRule('mimetype');
        if (is_null($pattern)) {
            return true;
        }

        return preg_match("!{$pattern}!", $mime);
    }

    // }}}
    // {{{ _detectMimeType()

    /**
     * Detect the mime-type of the given file.
     *
     * @param string $filename the file name to be checked.
     * @return mixed the mime-type string, or false if failed.
     */
    function _detectMimeType($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            return false;
        }

        if (function_exists('finfo_file')) {
            return $this->_detectMimeWithFileinfo($filename);
        }

        if (function_exists('mime_content_type')) {
            return mime_content_type($filename);
        }

        if (substr(PHP_OS, 0, 3) != 'WIN') {
            return exec('file -bi '. escapeshellarg($filename));
        }

        return false;
    }

    // }}}
    // {{{ _detectMimeWithFileinfo()

    /**
     * Detect the mime-type of the given file using FileInfo extension.
     *
     * @param string $filename the file name to be checked.
     * @return mixed the mime-type string, or false if failed.
     * @see http://www.php.net/manual/en/ref.fileinfo.php
     */
    function _detectMimeWithFileinfo($filename)
    {
        $info = finfo_open(FILEINFO_MIME);
        $mime = finfo_file($info, $filename);
        finfo_close($info);
        return $mime;
    }

    // }}}
    // {{{ _initialize()

    /**
     * Initializes properties.
     */
    function _initialize()
    {
        $this->_addRule('maxSize', null);
        $this->_addRule('minSize', 0);
        $this->_addRule('mimetype', null);
        $this->_addRule('useMagic', false);
        $this->_addRule('messagesByErrorCode', array());
    }

    // }}}
    // {{{ _initialize()

    /**
     * Sets an appropriate message correspoiding to a given error code.
     *
     * @param integer $actualErrorCode
     * @since Method available since Release 1.9.0
     */
    function _setMessageByErrorCode($actualErrorCode)
    {
        $messagesByErrorCode = $this->_getRule('messagesByErrorCode');
        foreach ($GLOBALS['PIECE_RIGHT_Validator_File_ErrorCodes'] as $errorCode) {
            if (!defined($errorCode)) {
                continue;
            }

            if ($actualErrorCode != constant($errorCode)) {
                continue;
            }

            if (array_key_exists($errorCode, $messagesByErrorCode)
                && !is_null($messagesByErrorCode[$errorCode])
                && strlen($messagesByErrorCode[$errorCode])
                ) {
                $this->setMessage($messagesByErrorCode[$errorCode]);
            }
        }
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
