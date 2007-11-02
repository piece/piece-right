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
require_once 'Piece/Right/Validator/File.php';

// {{{ constants

if (function_exists('finfo_file')
    || function_exists('mime_content_type')
    || substr(PHP_OS, 0, 3) != 'WIN') {
    define('SKIP_LIBMAGIC_TEST', 0);
} else {
    define('SKIP_LIBMAGIC_TEST', 1);
}

// }}}
// {{{ Piece_Right_Validator_FileTestCase

/**
 * TestCase for Piece_Right_Validator_File
 *
 * @package    Piece_Right
 * @copyright  2006 Chihiro Sakatoku <csakatoku@users.sourceforge.net>
 * @copyright  2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.3.0
 */
class Piece_Right_Validator_FileTestCase extends PHPUnit_TestCase
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

    function testSuccess()
    {
        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'text/plain',
                   'size' => $size,
                   'error' => UPLOAD_ERR_OK
                   );

        $validator = &new Piece_Right_Validator_File();
        $validator->setRules(array('minSize' => $size,
                                   'maxSize' => $size,
                                   'mimetype' => 'text/.+')
                             );
        $this->assertTrue($validator->validate($f));

        $validator = &new Piece_Right_Validator_File();
        $validator->setRules(array(
                        'mimetype' => '^application/.+$'
                    ));
        $this->assertFalse($validator->validate($f));
    }

    function testFailure()
    {
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'text/plain',
                   'size' => 0,
                   'error' => UPLOAD_ERR_NO_FILE
                   );

        $validator = &new Piece_Right_Validator_File();
        $this->assertFalse($validator->validate($f));
    }

    function testFileDoesNotExist()
    {
        $notExistFile = dirname(__FILE__). '/NotExistFile.txt';
        $f = array('tmp_name' => $notExistFile,
                   'name' => $notExistFile,
                   'type' => 'text/plain',
                   'size' => 100,
                   'error' => UPLOAD_ERR_OK
                   );

        $validator = &new Piece_Right_Validator_File();
        $validator->setRules(array('mimetype' => 'text/.+',
                                   'useMagic'=>true)
                             );
        $this->assertFalse($validator->validate($f));
    }

    function testMimeType()
    {
        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'text/plain',
                   'size' => $size,
                   'error' => UPLOAD_ERR_OK
                   );

        $validator = &new Piece_Right_Validator_File();
        $validator->setRules(array('mimetype' => '^text/.+$'));
        $this->assertTrue($validator->validate($f));
    }

    function testUseMagicSuccess()
    {
        if (SKIP_LIBMAGIC_TEST) {
            return;
        }

        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'spam/egg',
                   'size' => $size,
                   'error' => UPLOAD_ERR_OK
                   );

        $validator = &new Piece_Right_Validator_File();
        $validator->setRules(array('mimetype' => '^text/.+$',
                                   'useMagic'=>true)
                             );
        $this->assertTrue($validator->validate($f));
    }

    function testUseMagicFail()
    {
        if (SKIP_LIBMAGIC_TEST) {
            return;
        }

        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'application/spam',
                   'size' => $size,
                   'error' => UPLOAD_ERR_OK
                   );

        $validator = &new Piece_Right_Validator_File();
        $validator->setRules(array('mimetype' => '^application/.+$',
                                   'useMagic'=>true)
                             );
        $this->assertFalse($validator->validate($f));
    }

    function testMultipleFiles()
    {
        $size = filesize(__FILE__);
        $files = array();
        for ($i = 0; $i < 5; ++$i) {
            $files['tmp_name'][$i] = __FILE__;
            $files['name'][$i] = __FILE__;
            $files['type'][$i] = 'text/plain';
            $files['size'][$i] = $size;
            $files['error'][$i] = UPLOAD_ERR_OK;
        }

        $validator = &new Piece_Right_Validator_File();
        $validator->setRules(array('maxSize' => $size,
                                   'minSize' => $size,
                                   'mimetype' => '^text/.+$')
                             );
        $this->assertTrue($validator->validate($files));

        $files['tmp_name'][] = __FILE__;
        $files['name'][] = basename(__FILE__);
        $files['type'][] = 'image/jpeg';
        $files['size'][] = $size;
        $files['error'][] = UPLOAD_ERR_OK;

        $validator = &new Piece_Right_Validator_File();
        $validator->setRules(array('maxSize' => $size,
                                   'minSize' => $size,
                                   'mimetype' => '^text/.+$')
                             );
        $this->assertFalse($validator->validate($files));
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
