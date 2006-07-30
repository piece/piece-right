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
 * @see        PEAR_PackageFileManager2
 * @since      File available since Release 0.1.0
 */

require_once 'PEAR/PackageFileManager2.php';

PEAR::staticPushErrorHandling(PEAR_ERROR_CALLBACK, create_function('$error', 'var_dump($error); exit();'));

$version = '0.3.0';
$apiVersion = '0.3.0';
$notes = "The first beta release of Piece_Right.
This release includes a lot of enhancemens and several defect fixes as follows:

<<< Enhancements >>>

Kernel:

* Piece_Right
- Added support for filters.
- Added support for 'watcher'.
- Added array support.
- Added the code so as to set a Piece_Right_Results object to a validator object.
- Added error handling for invalid configuration.
- Added error handling for the factories of filters and validators.
- Added support for forcing validation.
- Added support for pseudo fields.
- Added support for message variables.

* Piece_Right_Config
- Added setRequired()/isRequired()/getRequiredMessage() methods for handling required stuff.
- Added support for filters.
- Added support for merging filters and required fileds.
- Added support for watchers.
- Added addField() method for adding fields which will be validated.
- Added setForceValidation() and forceValidation() method for forcing validation.
- Added support for pseudo fields.
- Added support for message variables.
- Added missing support for force validations.

* Piece_Right_Config_Factory
- Added support for filters.
- Added support for watchers.
- Added error handling for invalid configuration.
- Changed the code so as to call addField() method first.
- Added support for pseudo fields.
- Added support for message variables.
- Added missing support for force validations.

* Piece_Right_Validator_Common
- Added setResults() method for setting a Piece_Right_Results object.
- Added the constructor for initializing properties.
- Added _initialize() method for initializing properties.
- clear(): Added the code so as to call _initialize() method.
- Changed the location of the rules from each property to \$_rules.
- Changed the method name from addRule() to setRule().
- Added getRule() method for getting the validation rule of the given rule name.
- Added _addRule() method for defining/initializing validation rules.
- Added support for the error message of each rule.
- Added support for the error message of the current validation.
- Added \$_arrayable property for indicating the validator is arrayable or not.
- Added isArrayable() method for checking whether the validator is arrayable or not.

* Piece_Right_Filter_Factory
- A factory class for creating filter objects.

* Piece_Right_Error
- Added PIECE_RIGHT_ERROR_INVALID_CONFIGURATION constant.
- Added PIECE_RIGHT_ERROR_NOT_ARRAYABLE constant.

* Piece_Right_Results
- Added getFieldNames() method to getting all field names of the current validation.

Validators:

* List
- A validator which is used to check whether values are included in the definition.

* Piece_Right_Validator_Date
- A validator which is used to check whether a value is a valid date.

* Piece_Right_Validator_JapaneseDate
- A validator which is used to check whether a value is a valid japanese date.

* Piece_Right_Validator_Compare
- A validator which is used to compare the value of a field to the value of another field.

* WithMethod
- A validator which is used to validate the value of a field with an arbitrary method.

* Regex
- Changed the return value to false when the pattern is invalid.

* Required
- Removed.

Filters:

* JapaneseH2Z
- A filter which is used to converts Japanese JIS X0201 kana to JIS X0208 kana.

* JapaneseAlphaNumeric
- A filter which is used to converts Japanese JISX 0208 alphabet characters and numeric characters to ASCII characters.

<<< Defect fixes >>>

* Piece_Right
- Fixed the problem that the validation is influenced by the validation order.

* Piece_Right_Config
- setRequired(): Changed the code so as to keep the message on the filed if the given message is null.
- Fixed the problem that non required fields cannot be turned required on.

* Piece_Right_Filter_Factory
* Piece_Right_Validator_Factory
- Removed '@' operator from include_once.";

$package = new PEAR_PackageFileManager2();
$package->setOptions(array('filelistgenerator' => 'svn',
                           'changelogoldtonew' => false,
                           'simpleoutput'      => true,
                           'baseinstalldir'    => '/',
                           'packagefile'       => 'package2.xml',
                           'packagedirectory'  => '.')
                     );

$package->setPackage('Piece_Right');
$package->setPackageType('php');
$package->setSummary('A validation framework for PHP');
$package->setDescription('Piece_Right is a validation framework for PHP.
The following is a list of features of Piece_Right.
o Extensible validator system
o Extensible filter system
o A lot of built-in validators
o Another field watchers
o Pseudo fields
o Message variables
o Force validation
o YAML based configuration
o Dynamic configuration

The following is a list of built-in validators.
o Compare
o Date
o JapaneseDate
o Length
o List
o Range
o Regex
o WithMethod');
$package->setChannel('pear.hatotech.org');
$package->setLicense('BSD License (revised)',
                     'http://www.opensource.org/licenses/bsd-license.php'
                     );
$package->setAPIVersion($apiVersion);
$package->setAPIStability('beta');
$package->setReleaseVersion($version);
$package->setReleaseStability('beta');
$package->setNotes($notes);
$package->setPhpDep('4.3.0');
$package->setPearinstallerDep('1.4.3');
$package->addMaintainer('lead', 'iteman', 'KUBO Atsuhiro', 'iteman@users.sourceforge.net');
$package->addIgnore(array('package.php', 'package.xml', 'package2.xml'));
$package->addGlobalReplacement('package-info', '@package_version@', 'version');
$package->generateContents();
$package1 = &$package->exportCompatiblePackageFile1();

if (array_key_exists(1, $_SERVER['argv'])
    && $_SERVER['argv'][1] == 'make'
    ) {
    $package->writePackageFile();
    $package1->writePackageFile();
} else {
    $package->debugPackageFile();
    $package1->debugPackageFile();
}

exit();

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
