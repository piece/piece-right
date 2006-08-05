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
 * @link       http://www.ecodebank.com/details/?catid=8&catsubid=13&nid=7
 * @link       http://www.ietf.org/rfc/rfc0822.txt?number=822
 * @since      File available since Release 0.5.0
 */

require_once 'Piece/Right/Validator/Common.php';

// {{{ Piece_Right_Validator_Email

/**
 * A validator which is used to check whether a value is valid email address
 * or not. This validator checks only addr-spec defined in RFC822.
 *
 * This code is based on Clay Loveless's validateEmailFormat.php.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://iteman.typepad.jp/piece/
 * @link       http://www.ecodebank.com/details/?catid=8&catsubid=13&nid=7
 * @link       http://www.ietf.org/rfc/rfc0822.txt?number=822
 * @since      Class available since Release 0.5.0
 */
class Piece_Right_Validator_Email extends Piece_Right_Validator_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_addrSpec;
    var $_addrSpecForDotBeforeAtmark;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * Initializes properties.
     */
    function Piece_Right_Validator_Email()
    {
        parent::Piece_Right_Validator_Common();

        $esc        = '\\\\';
        $period     = '\.';
        $space      = '\040';
        $tab        = '\t';
        $openBR     = '\[';
        $closeBR    = '\]';
        $openParen  = '\(';
        $closeParen = '\)';
        $nonASCII   = '\x80-\xff'; 
        $ctrl       = '\000-\037';
        $crList     = '\n\015';
        $qtext      = "[^$esc$nonASCII$crList\"]";
        $dtext      = "[^$esc$nonASCII$crList$openBR$closeBR]";
        $quotedPair = " $esc [^$nonASCII] ";
        $ctext      = " [^$esc$nonASCII$crList()] ";
        $cnested    = "$openParen$ctext*(?: $quotedPair $ctext* )*$closeParen";
        $comment    = "$openParen$ctext*(?:(?: $quotedPair | $cnested )$ctext*)*$closeParen";
        $x          = "[$space$tab]*(?: $comment [$space$tab]* )*";
        $atomChar   = "[^($space)<>\@,;:\".$esc$openBR$closeBR$ctrl$nonASCII]";
        $atom       = "$atomChar+(?!$atomChar)";
        $quotedStr  = "\"$qtext *(?: $quotedPair $qtext * )*\"";
        $word       = "(?:$atom|$quotedStr)";
        $domainRef  = $atom;
        $domainLit  = "$openBR(?: $dtext | $quotedPair )*$closeBR";
        $subDomain  = "(?:$domainRef|$domainLit)$x";
        $domain     = "$subDomain(?:$period $x $subDomain)*";
        $localPart  = "$word $x(?:$period $x $word $x)*";
        $localPartForDotBeforeAtmark = "$word $x(?:$period $x $word $x|$period)*";

        $this->_addrSpec = "$localPart \@ $x $domain";
        $this->_addrSpecForDotBeforeAtmark = "$localPartForDotBeforeAtmark \@ $x $domain";
    }

    // }}}
    // {{{ validate()

    /**
     * Checks whether a value is valid email address or not.
     *
     * @param string $value
     * @return boolean
     */
    function validate($value)
    {
        $allowDotBeforeAtmark = $this->getRule('allowDotBeforeAtmark');
        if (!$allowDotBeforeAtmark) {
            return preg_match("/^{$this->_addrSpec}$/xS", $value);
        } else {
            return preg_match("/^{$this->_addrSpecForDotBeforeAtmark}$/xS", $value);
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
        $this->_addRule('allowDotBeforeAtmark', false);
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
