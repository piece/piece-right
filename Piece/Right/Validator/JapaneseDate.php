<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006, KUBO Atsuhiro <iteman@users.sourceforge.net>
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
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://iteman.typepad.jp/piece/
 * @since      File available since Release 0.3.0
 */

require_once 'Piece/Right/Validator/Common.php';

// {{{ Piece_Right_Validator_JapaneseDate

/**
 * A validator which is used to check whether a value is a valid japanese
 * date.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://iteman.typepad.jp/piece/
 * @since      Class available since Release 0.3.0
 */
class Piece_Right_Validator_JapaneseDate extends Piece_Right_Validator_Common
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
        $era = $this->getRule('era');
        $eraMapping = $this->getRule('eraMapping');
        $year = $this->getRule('year');
        $month = $this->getRule('month');
        $day = $this->getRule('day');
        if (is_null($era) || is_null($year) || is_null($month) || is_null($day)) {
            return false;
        }

        $eraValue = $this->_results->getFieldValue($era);
        $eraMapping = array_flip($eraMapping);
        if (!array_key_exists($eraValue, $eraMapping)) {
            return false;
        }

        $yearValue = $this->_results->getFieldValue($year);
        switch ($eraMapping[$eraValue]) {
        case 'showa':
            $adYear = 1926 - 1 + $yearValue;
            break;
        case 'heisei':
            $adYear = 1989 - 1 + $yearValue;
            break;
        case 'taisho':
            $adYear = 1912 - 1 + $yearValue;
            break;
        case 'meiji':
            $adYear = 1868 - 1 + $yearValue;
            break;
        }

        return checkdate($this->_results->getFieldValue($month),
                         $this->_results->getFieldValue($day),
                         $adYear
                         );
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
        $this->_addRule('era');
        $this->_addRule('eraMapping', array('meiji'  => 1,
                                            'taisho' => 2,
                                            'showa'  => 3,
                                            'heisei' => 4)
                        );
        $this->_addRule('year');
        $this->_addRule('month');
        $this->_addRule('day');
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
