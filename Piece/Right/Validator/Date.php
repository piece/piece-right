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
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @see        http://ja.wikipedia.org/wiki/%E5%B9%B3%E6%88%90
 * @see        http://ja.wikipedia.org/wiki/%E6%98%AD%E5%92%8C
 * @see        http://ja.wikipedia.org/wiki/%E5%A4%A7%E6%AD%A3
 * @see        http://ja.wikipedia.org/wiki/%E6%98%8E%E6%B2%BB
 * @since      File available since Release 0.3.0
 */

require_once 'Piece/Right/Validator/Common.php';
require_once 'Date.php';

// {{{ Piece_Right_Validator_Date

/**
 * A validator which is used to check whether a value is a valid date.
 *
 * @package    Piece_Right
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @see        http://ja.wikipedia.org/wiki/%E5%B9%B3%E6%88%90
 * @see        http://ja.wikipedia.org/wiki/%E6%98%AD%E5%92%8C
 * @see        http://ja.wikipedia.org/wiki/%E5%A4%A7%E6%AD%A3
 * @see        http://ja.wikipedia.org/wiki/%E6%98%8E%E6%B2%BB
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
        $pattern = $this->_getRule('pattern');
        if (!preg_match($pattern, $value, $matches)) {
            return false;
        }

        $this->_month = $matches[ $this->_getRule('patternMonthPosition') ];
        $this->_day = $matches[ $this->_getRule('patternDayPosition') ];

        $isJapaneseEra = $this->_getRule('isJapaneseEra');
        if (!$isJapaneseEra) {
            $this->_year = $matches[ $this->_getRule('patternYearPosition') ];
            return checkdate($this->_month, $this->_day, $this->_year);
        } else {
            return $this->_validateDateOfJapaneseEra($matches[ $this->_getRule('patternEraPosition') ],
                                                     $matches[ $this->_getRule('patternYearPosition') ]
                                                     );
        }
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
        $this->_addRule('allowCurrentDate', false);
        $this->_year = null;
        $this->_month = null;
        $this->_day = null;
    }

    // }}}
    // {{{ _validateDateOfJapaneseEra()

    /**
     * Validates a date of Japanese era.
     *
     * @param string $era
     * @param string $year
     * @return boolean
     * @since Method available since Release 1.6.0
     */
    function _validateDateOfJapaneseEra($era, $year)
    {
        $eraMapping = array_flip($this->_getRule('eraMapping'));
        if (!array_key_exists($era, $eraMapping)) {
            return false;
        }

        switch ($eraMapping[$era]) {
        case 'heisei':
            $this->_year = 1989 - 1 + $year;
            break;
        case 'showa':
            $this->_year = 1926 - 1 + $year;
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

        if (checkdate($this->_month, $this->_day, $this->_year)) {
            $dateForComparison = sprintf('%04d%02d%02d', $this->_year, $this->_month, $this->_day);
            switch ($eraMapping[$era]) {
            case 'heisei':
                if ($dateForComparison < '19890108') {
                    return false;
                }

                break;
            case 'showa':
                if ($dateForComparison < '19261225') {
                    return false;
                }

                if ($dateForComparison > '19890107') {
                    return false;
                }

                break;
            case 'taisho':
                if ($dateForComparison < '19120730') {
                    return false;
                }

                if ($dateForComparison > '19261225') {
                    return false;
                }

                break;
            case 'meiji':
                if ($dateForComparison < '18680125') {
                    return false;
                }

                if ($dateForComparison > '19120730') {
                    return false;
                }

                break;
            }

            return true;
        } else {
            return false;
        }
    }

    // }}}
    // {{{ _compareGivenDateAndCurrentDate()

    /**
     * Compares the given date and the current date.
     *
     * @return integer
     * @link http://www.php.net/manual/en/function.mktime.php
     */
    function _compareGivenDateAndCurrentDate()
    {
        $givenDate = new Date();
        $givenDate->setYear($this->_year);
        $givenDate->setMonth($this->_month);
        $givenDate->setDay($this->_day);
        $givenDate->setHour(0);
        $givenDate->setMinute(0);
        $givenDate->setSecond(0);
        $currentDate = new Date();
        $currentDate->setHour(0);
        $currentDate->setMinute(0);
        $currentDate->setSecond(0);
        return @Date::compare($givenDate, $currentDate);
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
