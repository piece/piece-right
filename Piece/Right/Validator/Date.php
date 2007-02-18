<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://piece-framework.com/piece-right/
 * @since      File available since Release 0.3.0
 */

require_once 'Piece/Right/Validator/Common.php';

// {{{ Piece_Right_Validator_Date

/**
 * A validator which is used to check whether a value is a valid date.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @since      Class available since Release 0.3.0
 */
class Piece_Right_Validator_Date extends Piece_Right_Validator_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_year;
    var $_month;
    var $_day;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ validate()

    /**
     * Checks whether a value is a valid date.
     *
     * @param string $value
     * @return boolean
     */
    function validate($value)
    {
        $pattern = $this->getRule('pattern');
        if (!preg_match($pattern, $value, $matches)) {
            return false;
        }

        $this->_month = $matches[ $this->getRule('patternMonthPosition') ];
        $this->_day = $matches[ $this->getRule('patternDayPosition') ];

        $isJapaneseEra = $this->getRule('isJapaneseEra');
        if (!$isJapaneseEra) {
            $this->_year = $matches[ $this->getRule('patternYearPosition') ];
        } else {
            $era = $matches[ $this->getRule('patternEraPosition') ];
            $eraMapping = array_flip($this->getRule('eraMapping'));
            if (!array_key_exists($era, $eraMapping)) {
                return false;
            }

            $year = $matches[ $this->getRule('patternYearPosition') ];
            switch ($eraMapping[$era]) {
            case 'showa':
                $this->_year = 1926 - 1 + $year;
                break;
            case 'heisei':
                $this->_year = 1989 - 1 + $year;
                break;
            case 'taisho':
                $this->_year = 1912 - 1 + $year;
                break;
            case 'meiji':
                $this->_year = 1868 - 1 + $year;
                break;
            default:
                return false;
            }
        }

        return checkdate($this->_month, $this->_day, $this->_year);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _initialize()

    /**
     * Initializes properties.
     *
     * @since Method available since Release 0.3.0
     */
    function _initialize()
    {
        $this->_addRule('pattern', '/^(\d+)-(\d+)-(\d+)$/');
        $this->_addRule('patternYearPosition', 1);
        $this->_addRule('patternMonthPosition', 2);
        $this->_addRule('patternDayPosition', 3);
        $this->_addRule('isJapaneseEra', false);
        $this->_addRule('eraMapping', array('meiji'  => 1,
                                            'taisho' => 2,
                                            'showa'  => 3,
                                            'heisei' => 4)
                        );
        $this->_addRule('patternEraPosition', 4);
        $this->_year = null;
        $this->_month = null;
        $this->_day = null;
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
?>
