<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @link       http://piece-framework.com/piece-right/
 * @see        PEAR_PackageFileManager2
 * @since      File available since Release 0.1.0
 */

require_once 'PEAR/PackageFileManager2.php';

PEAR::staticPushErrorHandling(PEAR_ERROR_CALLBACK, create_function('$error', 'var_dump($error); exit();'));

$version = '1.3.0';
$apiVersion = '1.1.0';
$releaseStability = 'stable';
$notes = "This release includes two new validators File and Image, a lot of enhancements, and several fixes. See the following release notes for details.

## Enhancements ##

### Validators ###

##### Range, Numeric #####

- Added the code so as to check whether a value is a numeric, and extracted its features as Numeric validator. Thanks to Chihiro Sakatoku <csakatoku@mac.com> for good advices and the patches.

##### File #####

- A validator which is used to validate a file.

##### Image #####

- A validator to check files are valid image files.

### Kernel ###

##### Piece_Right #####

- Changed support file uploads in Piece_Right.
- Added support for filtering all values in an array. (Ticket #3)

##### Piece_Right_Config_Factory #####

- Changed so as to raise an exception if the file corresponding to a validation set not found or not readable. (Ticket #3)
- Removed the feature of defining message variables in validation definition file. (Ticket #7)

##### Piece_Right_Validation_Script #####

- Changed so as to pass a Piece_Right_Results object to the post run callback by reference, and return the Piece_Right_Results object by reference. (Ticket #9)
- Added setPayload() and changed so as to be able to pass a user defined payload to validators.

##### Piece_Right_Validator_Common #####

- Added a feature to replace message variables in a error message with
  the value of each rule if the value is not an array. (Ticket #8)

### Filters ###

##### Empty2NULL #####

- A filter which is used to replace an empty string with null.

## Defect Fixes ##

### Kernel ###

##### Piece_Right_Results #####

- Fixed a fatal error where 'Object of class Piece_Right_Validation_Error could not be converted to string' raised with PHP 5.2.0 RC5. Thanks to Chihiro Sakatoku <csakatoku@mac.com> for the patches.

##### Piece_Right_Validator_Common #####

- Removed unused argument \$payload from validate() method. Thanks to Chihiro Sakatoku <csakatoku@mac.com> for the patches.

##### Piece_Right #####

- Fixed the problem that the fields which are specified to turn off always turn off even though what the target field values are.
- Fixed so as to set an error if a field value is an array and a validator is not arrayable.";

$package = new PEAR_PackageFileManager2();
$package->setOptions(array('filelistgenerator' => 'svn',
                           'changelogoldtonew' => false,
                           'simpleoutput'      => true,
                           'baseinstalldir'    => '/',
                           'packagefile'       => 'package2.xml',
                           'packagedirectory'  => '.',
                           'dir_roles'         => array('data' => 'data',
                                                        'tests' => 'test',
                                                        'docs' => 'doc'))
                     );

$package->setPackage('Piece_Right');
$package->setPackageType('php');
$package->setSummary('A validation framework for PHP');
$package->setDescription('Piece_Right is a validation framework for PHP.
The following is a list of features of Piece_Right.
o Extensible validator system
o Extensible filter system
o A lot of built-in validators
o Pseudo fields
o Watching the specified fields
o Message variables
o Force validation
o YAML based configuration
o Dynamic configuration

The following is a list of built-in validators.
o Compare
o Date, FutureDate, PastDate (including Japanese date support)
o Length
o List
o Range
o Regex
o WithMethod
o Email
o File
o Image');
$package->setChannel('pear.piece-framework.com');
$package->setLicense('BSD License (revised)',
                     'http://www.opensource.org/licenses/bsd-license.php'
                     );
$package->setAPIVersion($apiVersion);
$package->setAPIStability('stable');
$package->setReleaseVersion($version);
$package->setReleaseStability($releaseStability);
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
