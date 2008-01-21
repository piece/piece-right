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

use Piece::Right::FilterFactory;
use Stagehand::Autoload;

require_once dirname(__FILE__) . '/../../prepare.php';

// {{{ DescribeRightFilterfactory

/**
 * Some specs for Piece::Right::FilterFactory.
 *
 * @package    Piece_Right
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 2.0.0
 */
class DescribeRightFilterfactory extends PHPSpec_Context
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

    private $_oldInlucdePath;

    /**#@-*/

    /**#@+
     * @access public
     */

    public function beforeAll()
    {
        Autoload::register('FooBar');
        $this->_oldInlucdePath = set_include_path(dirname(__FILE__) . '/' . basename(__FILE__, '.php') .
                                                  PATH_SEPARATOR . get_include_path()
                                                  );
    }

    public function afterAll()
    {
        set_include_path($this->_oldInlucdePath);
        Autoload::unregister('FooBar');
    }

    public function after()
    {
        FilterFactory::clearInstances();
    }

    public function itShouldRaiseAnExceptionWhenTheFileIsNotFound()
    {
        try {
            FilterFactory::factory('FileNotFoundAction');
        } catch (Piece::Right::Exception $e) {
            return;
        }

        $this->fail();
    }

    public function itShouldCreateAnObjectByAGivenClass()
    {
        $filter = FilterFactory::factory('Empty2NULL');

        $this->spec($filter)->should->beAnInstanceOf('Piece::Right::Filter::Empty2NULL');
    }

    public function itShouldReturnTheExistingObjectIfItExists()
    {
        $filter1 = FilterFactory::factory('Empty2NULL');
        $filter2 = FilterFactory::factory('Empty2NULL');

        $this->spec(spl_object_hash($filter2))->should->beEqualTo(spl_object_hash($filter1));
    }

    public function itShouldSupportNamespaces()
    {
        FilterFactory::addNamespace('FooBar');
        $filter = FilterFactory::factory('BarBaz');

        $this->spec($filter)->should->beAnInstanceOf('FooBar::BarBaz');
    }

    public function itShouldSupportEmptyNamespace()
    {
        require dirname(__FILE__) . '/' . basename(__FILE__, '.php') . '/BazQux.php';
        FilterFactory::addNamespace('');
        $filter = FilterFactory::factory('BazQux');

        $this->spec($filter)->should->beAnInstanceOf('BazQux');
    }

    public function itShouldReplaceAExistingFilterWithAnotherClass()
    {
        FilterFactory::addNamespace('FooBar');
        $filter = FilterFactory::factory('Empty2NULL');

        $this->spec($filter)->shouldNot->beAnInstanceOf('Piece::Right::Filter::Empty2NULL');
        $this->spec($filter)->should->beAnInstanceOf('FooBar::Empty2NULL');
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
