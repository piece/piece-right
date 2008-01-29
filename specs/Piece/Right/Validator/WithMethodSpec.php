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

use Piece::Right::Validator::WithMethod;
use Stagehand::Autoload;
use Piece::Right::Results;

require_once dirname(__FILE__) . '/../../../prepare.php';

// {{{ DescribeRightValidatorWithMethod

/**
 * Some specs for Piece::Right::Validator::WithMethod.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightValidatorWithMethod extends PHPSpec_Context
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
    private $_oldInlucdePath;

    /**#@-*/

    /**#@+
     * @access public
     */

    public function beforeAll()
    {
        $this->_validator = new WithMethod();
        Autoload::register('Foo');
        $this->_oldInlucdePath = set_include_path(dirname(__FILE__) . '/' . basename(__FILE__, '.php') .
                                                  PATH_SEPARATOR . get_include_path()
                                                  );
    }

    public function afterAll()
    {
        set_include_path($this->_oldInlucdePath);
        Autoload::unregister('Foo');
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
        $this->_validator->setRules(array('class' => 'Foo::WithMethod',
                                          'method' => 'isValid')
                                    );

        $this->spec($this->_validator->validate('foo'))->should->beTrue();
        $this->spec($this->_validator->validate(array('foo', 'foo')))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('class' => 'Foo::WithMethod',
                                          'method' => 'isValid',
                                          'isStatic' => true)
                                    );

        $this->spec($this->_validator->validate('foo'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('class' => 'Foo::WithMethod',
                                          'method' => 'isFoo',
                                          'isStatic' => false)
                                    );

        $this->spec($this->_validator->validate('foo'))->should->beTrue();

        $this->_validator->clear();
        $this->_validator->setRules(array('class' => 'Foo::WithMethod',
                                          'method' => 'isValidAndSetFoo',
                                          'isStatic' => false)
                                    );
        $payload = array('bar' => 'baz');
        $this->_validator->setPayload($payload);

        $this->spec($this->_validator->validate('foo'))->should->beTrue();
        $this->spec(count($payload))->should->beEqualTo(2);
        $this->spec(array_key_exists('foo', $payload))->should->beTrue();
        $this->spec(array_key_exists('bar', $payload))->should->beTrue();
        $this->spec($payload['foo'])->should->be('bar');
        $this->spec($payload['bar'])->should->be('baz');
    }

    public function itShouldFail()
    {
        $this->_validator->setRules(array('class' => 'Foo::WithMethod',
                                          'method' => 'isValid')
                                    );

        $this->spec($this->_validator->validate('bar'))->should->beFalse();
        $this->spec($this->_validator->validate(array('foo', 'bar')))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('class' => 'Foo::WithMethod',
                                          'method' => 'isValid',
                                          'isStatic' => true)
                                    );

        $this->spec($this->_validator->validate('bar'))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('class' => 'Foo::WithMethod',
                                          'method' => 'isFoo',
                                          'isStatic' => false)
                                    );

        $this->spec($this->_validator->validate('bar'))->should->beFalse();

        $this->_validator->clear();
        $this->_validator->setRules(array('class' => 'Foo::WithMethod',
                                          'method' => 'isValidAndSetFoo',
                                          'isStatic' => false)
                                    );
        $payload = array('bar' => 'baz');
        $this->_validator->setPayload($payload);

        $this->spec($this->_validator->validate('bar'))->should->beFalse();
        $this->spec(count($payload))->should->beEqualTo(2);
        $this->spec(array_key_exists('foo', $payload))->should->beTrue();
        $this->spec(array_key_exists('bar', $payload))->should->beTrue();
        $this->spec($payload['foo'])->should->be('bar');
        $this->spec($payload['bar'])->should->be('baz');
    }

    public function itShouldRaiseAnExceptionWhenTheFileIsNotFound()
    {
        try {
            $this->_validator->setRules(array('class' => 'Foo::Bar',
                                              'method' => 'baz')
                                        );
            $this->_validator->validate('foo');
        } catch (Piece::Right::Exception $e) {
            return;
        }

        $this->fail();
    }

    public function itShouldPassTheResultsToAMethod()
    {
        $results = new Results();
        $results->setFieldValue('bar', 'baz');
        $this->_validator->setResults($results);
        $this->_validator->setRules(array('class' => 'Foo::WithMethod',
                                          'method' => 'compare')
                                    );

        $this->spec($this->_validator->validate('baz'))->should->beTrue();
        $this->spec(array_key_exists('foo', $results))->should->beTrue();
        $this->spec($results->foo)->should->be('bar');
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
