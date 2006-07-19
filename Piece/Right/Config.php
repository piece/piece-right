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
 * @since      File available since Release 0.1.0
 */

// {{{ Piece_Right_Config

/**
 * The configuration container for Piece_Right validation sets.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://iteman.typepad.jp/piece/
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_Config
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_validations = array();

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ addValidation()

    /**
     * Adds a validation to a validation point with the given rules.
     *
     * @param string $validationPoint
     * @param string $validator
     * @param array  $rules
     */
    function addValidation($validationPoint, $validator, $rules = array())
    {
        if (!array_key_exists($validationPoint, $this->_validations)) {
            $this->_validations[$validationPoint] = array();
        }

        array_push($this->_validations[$validationPoint],
                   array('validator' => $validator, 'rules' => $rules)
                   );
    }

    // }}}
    // {{{ getValidations()

    /**
     * Gets the array of the validations.
     *
     * @return array
     */
    function getValidations()
    {
        return $this->_validations;
    }

    // }}}
    // {{{ merge()

    /**
     * Merges the given configuretion into the existing configuration.
     *
     * @param Piece_Right_Config &$config
     */
    function merge(&$config)
    {
        $validations = $config->getValidations();
        array_walk($validations, array(&$this, 'mergeValidations'));
    }

    // }}}
    // {{{ mergeExtensions()

    /**
     * A callback that will be called by array_walk() function in merge()
     * method.
     *
     * @param string $validations
     * @param string $validationPoint
     */
    function mergeValidations($validations, $validationPoint)
    {
        foreach ($validations as $validation) {
            $this->addValidation($validationPoint, $validation['validator'], $validation['rules']);
        }
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
