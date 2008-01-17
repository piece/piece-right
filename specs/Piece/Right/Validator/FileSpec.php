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

use Piece::Right::Validator::File;

require_once dirname(__FILE__) . '/../../../prepare.php';

// {{{ DescribeRightValidatorFile

/**
 * Some specs for Piece::Right::Validator::File.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightValidatorFile extends PHPSpec_Context
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

    private $_validator;
    private $_runMagicExamples;

    /**#@-*/

    /**#@+
     * @access public
     */

    public function beforeAll()
    {
        $this->_validator = new File();
        $this->_runMagicExamples = function_exists('finfo_file') || substr(PHP_OS, 0, 3) != 'WIN';
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
        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'text/plain',
                   'size' => $size,
                   'error' => UPLOAD_ERR_OK
                   );
        $this->_validator->setRules(array('minSize' => $size,
                                          'maxSize' => $size,
                                          'mimetype' => 'text/.+')
                                    );

        $this->spec($this->_validator->validate($f))->should->beTrue();
    }

    public function itShouldFail()
    {
        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'text/plain',
                   'size' => $size,
                   'error' => UPLOAD_ERR_OK
                   );
        $this->_validator->setRules(array('mimetype' => '^application/.+$'));

        $this->spec($this->_validator->validate($f))->should->beFalse();

        $this->_validator->clear();
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'text/plain',
                   'size' => 0,
                   'error' => UPLOAD_ERR_NO_FILE
                   );

        $this->spec($this->_validator->validate($f))->should->beFalse();
    }

    public function itShouldFailIfAFileIsNotFound()
    {
        $notExistFile = dirname(__FILE__). '/NotExistFile.txt';
        $f = array('tmp_name' => $notExistFile,
                   'name' => $notExistFile,
                   'type' => 'text/plain',
                   'size' => 100,
                   'error' => UPLOAD_ERR_OK
                   );

        $this->_validator->setRules(array('mimetype' => 'text/.+',
                                          'useMagic' => true)
                                    );

        $this->spec($this->_validator->validate($f))->should->beFalse();
    }

    public function itShouldSupportToValidateByMimetype()
    {
        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'text/plain',
                   'size' => $size,
                   'error' => UPLOAD_ERR_OK
                   );

        $this->_validator->setRules(array('mimetype' => '^text/.+$'));

        $this->spec($this->_validator->validate($f))->should->beTrue();
    }

    public function itShouldSupportToValidateByMagicFile()
    {
        if (!$this->_runMagicExamples) {
            $this->pending('Fileinfo extension is not available or the specs is running on Windows.');
            return;
        }

        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'spam/egg',
                   'size' => $size,
                   'error' => UPLOAD_ERR_OK
                   );

        $this->_validator->setRules(array('mimetype' => '^text/.+$',
                                          'useMagic'=>true)
                                    );

        $this->spec($this->_validator->validate($f))->should->beTrue();

        $this->_validator->clear();
        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'application/spam',
                   'size' => $size,
                   'error' => UPLOAD_ERR_OK
                   );
        $this->_validator->setRules(array('mimetype' => '^application/.+$',
                                          'useMagic'=>true)
                                    );

        $this->spec($this->_validator->validate($f))->should->beFalse();
    }

    public function itShouldSupportMultipleFiles()
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

        $this->_validator->setRules(array('maxSize' => $size,
                                          'minSize' => $size,
                                          'mimetype' => '^text/.+$')
                                    );

        $this->spec($this->_validator->validate($files))->should->beTrue();

        $this->_validator->clear();
        $files['tmp_name'][] = __FILE__;
        $files['name'][] = basename(__FILE__);
        $files['type'][] = 'image/jpeg';
        $files['size'][] = $size;
        $files['error'][] = UPLOAD_ERR_OK;
        $this->_validator->setRules(array('maxSize' => $size,
                                          'minSize' => $size,
                                          'mimetype' => '^text/.+$')
                                    );

        $this->spec($this->_validator->validate($files))->should->beFalse();
    }

    public function itShouldProvideAMessageCorrespondingToAnErrorCode()
    {
        $size = filesize(__FILE__);
        $f = array('tmp_name' => __FILE__,
                   'name' => basename(__FILE__),
                   'type' => 'text/plain',
                   'size' => $size,
                   'error' => UPLOAD_ERR_FORM_SIZE
                   );
        $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
        $this->_validator->setRules(array('minSize' => $size,
                                          'maxSize' => $size,
                                          'mimetype' => 'text/.+',
                                          'messagesByErrorCode' => array('UPLOAD_ERR_FORM_SIZE' => $message)
                                          )
                                    );

        $this->spec($this->_validator->validate($f))->should->beFalse();
        $this->spec($this->_validator->getMessage())->should->be($message);
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
?>
