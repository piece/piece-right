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
 * @since      File available since Release 0.1.0
 */

// {{{ Piece_Right_Validation_Error

/**
 * The holder of validation results.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-right/
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_Validation_Error
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_errors = array();

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ add()

    /**
     * Adds an error to the result.
     *
     * @param string   $validator
     * @param string   $message
     */
    function add($validator, $message = null)
    {
        array_push($this->_errors,
                   array('validator' => $validator,
                         'message' => $message)
                   );
    }

    // }}}
    // {{{ countErrors()

    /**
     * Returns the number of the errors.
     *
     * @return integer
     */
    function countErrors()
    {
        return count($this->_errors);
    }

    // }}}
    // {{{ getMessage()

    /**
     * Gets the message of the first error.
     *
     * @return string
     */
    function getMessage()
    {
        if ($this->countErrors()) {
            return $this->_errors[0]['message'];
        }
    }

    // }}}
    // {{{ getMessages()

    /**
     * Gets an array of all messages.
     *
     * @return string
     */
    function getMessages()
    {
        $messages = array();
        foreach ($this->_errors as $error) {
            array_push($messages, $error['message']);
        }

        return $messages;
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
