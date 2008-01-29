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

use Piece::Right::Config::Factory;

require_once dirname(__FILE__) . '/../../../prepare.php';

// {{{ DescribeRightConfigFactory

/**
 * Some specs for Piece::Right::Config::Factory.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightConfigFactory extends PHPSpec_Context
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

    private $_cacheDirectory;

    /**#@-*/

    /**#@+
     * @access public
     */

    public function beforeAll()
    {
        $this->_cacheDirectory = dirname(__FILE__) . '/' . basename(__FILE__, '.php');
    }

    public function afterAll()
    {
        clearCache($this->_cacheDirectory);
    }

    public function itShouldReadConfigurationFromAFile()
    {
        $config = Factory::factory('Example',
                                   $this->_cacheDirectory,
                                   $this->_cacheDirectory
                                   );

        $this->spec($config)->should->beAnInstanceOf('Piece::Right::Config');

        $validations = $config->getValidations('first_name');

        $this->spec(is_array($validations))->should->beTrue();
        $this->spec($config->isRequired('first_name'))->should->beTrue();
        $this->spec($validations[0]['validator'])->should->be('Length');
        $this->spec($validations[0]['rules'])->should->be(array('min' => 1, 'max' => 255));
        $this->spec($config->getRequiredMessage('first_name'))->should->be('foo');
        $this->spec($validations[0]['message'])->should->be('bar');

        $validations = $config->getValidations('last_name');

        $this->spec(is_array($validations))->should->beTrue();
        $this->spec($config->isRequired('last_name'))->should->beTrue();
        $this->spec($validations[0]['rules'])->should->be(array('min' => 1, 'max' => 255));
        $this->spec($config->getRequiredMessage('last_name'))->should->be('baz');
        $this->spec($validations[0]['message'])->should->be('bar');

        $validations = $config->getValidations('country');

        $this->spec(is_array($validations))->should->beTrue();
        $this->spec($config->isRequired('country'))->should->beFalse();
        $this->spec($validations[0]['validator'])->should->be('Length');
        $this->spec($validations[0]['rules'])->should->be(array('min' => 1, 'max' => 255));
        $this->spec($validations[0]['message'])->should->beNull();
    }

    public function itShouldRaiseAnExceptionIfAConfigurationDirectoryIsNotFound()
    {
        try {
            Factory::factory('Example',
                             dirname(__FILE__) . '/foo',
                             $this->_cacheDirectory
                             );
        } catch (Piece::Right::Exception $e) {
            return;
        }

        $this->fail();
    }

    public function itShouldRaiseAnExceptionIfACacheDirectoryIsNotFound()
    {
        try {
            Factory::factory('Example',
                             $this->_cacheDirectory,
                             dirname(__FILE__) . '/foo'
                             );
        } catch (Piece::Right::Exception $e) {
            return;
        }

        $this->fail();
    }

    public function itShouldRaiseAnExceptionIfAFileIsNotFound()
    {
        try {
            Factory::factory('FileNotFound',
                             $this->_cacheDirectory,
                             $this->_cacheDirectory
                             );
        } catch (Piece::Right::Exception $e) {
            return;
        }

        $this->fail();
    }

    public function itShouldRaiseAnExceptionIfAFileIsInvalid()
    {
        try {
            Factory::factory('InvalidConfiguration',
                             $this->_cacheDirectory,
                             $this->_cacheDirectory
                             );
        } catch (Piece::Right::Exception $e) {
            return;
        }

        $this->fail();
    }

    public function itShouldCreateUniqueCacheIdsInOneCacheDirectory()
    {
        clearCache($this->_cacheDirectory);

        Factory::factory('CacheIDsShouldBeUniqueInOneCacheDirectory',
                         "{$this->_cacheDirectory}/CacheIDsShouldBeUniqueInOneCacheDirectory1",
                         $this->_cacheDirectory
                         );

        $this->spec($this->_getCacheFileCount($this->_cacheDirectory))->should->be(1);

        Factory::factory('CacheIDsShouldBeUniqueInOneCacheDirectory',
                         "{$this->_cacheDirectory}/CacheIDsShouldBeUniqueInOneCacheDirectory2",
                         $this->_cacheDirectory
                         );

        $this->spec($this->_getCacheFileCount($this->_cacheDirectory))->should->be(2);
    }

    public function itShouldSupportTemplate()
    {
        $config = Factory::factory('TemplateShouldBeUsedIfFileIsSetAndBasedOnElementIsSpecified',
                                   $this->_cacheDirectory,
                                   $this->_cacheDirectory,
                                   'Common'
                                   );

        $fieldNames = $config->getFieldNames();

        $this->spec(count($fieldNames))->should->be(2);
        $this->spec(in_array('firstName', $fieldNames))->should->beTrue();
        $this->spec(in_array('lastName', $fieldNames))->should->beTrue();

        foreach (array('firstName', 'lastName') as $fieldName) {
            $this->spec($config->isRequired($fieldName))->should->beTrue();

            $validations = $config->getValidations($fieldName);

            $this->spec(count($validations))->should->be(1);
            $this->spec($validations[0]['validator'])->should->be('Length');
            $this->spec($validations[0]['rules'])->should->be(array('min' => 1, 'max' => 255));
            $this->spec($config->getRequiredMessage('firstName'))->should->be('%_description% is required');
            $this->spec($validations[0]['message'])->should->be('The length of %_description% must be less than %max% characters');
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

    protected function _readConfig($validationSetName)
    {
        return Factory::factory($validationSetName,
                                $this->_cacheDirectory,
                                $this->_cacheDirectory
                                );
    }

    private function _getCacheFileCount($directory)
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
