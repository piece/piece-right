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
 * @see        PEAR_PackageFileManager2
 * @since      File available since Release 0.1.0
 */

require_once 'PEAR/PackageFileManager2.php';

PEAR::staticPushErrorHandling(PEAR_ERROR_CALLBACK, create_function('$error', 'var_dump($error); exit();'));

$releaseVersion = '1.6.0';
$releaseStability = 'stable';
$apiVersion = '1.1.0';
$apiStability = 'stable';
$notes = 'A new release of Piece_Right is now available.

What\'s New in Piece_Right 1.6.0

 * Finals: "Finals" can be used for additional validations after normal validations.
 * Enhanced Filter Mechanism: The filter mechanism can now pass an array to the filter directly if it is arrayable.
 * Unique/UniqueFields validators: "Unique" validator can be used to check that all values in an array do not duplicate. "UniqueFields" validator can be used to check that each value of multiple fields do not duplicate.
 * Improved WithMethod validator: "WithMethod" validator now has a rule "directory" for loading a class from the specified directory if the class file is not loaded before invoking a method. And also a callback can now receive a Piece_Right_Results object as the second argument.
 * NoFile2NULL filter: "NoFile2NULL" filter converts a $_FILES element with UPLOAD_ERR_NO_FILE to NULL.
 * A few Defect Fixes: A few defects in validators are fixed.

See the following release notes for details.

Enhancements
============ 

Kernel:

- Added a feature named "Finals" that can be used for additional validations after normal validations. (Ticket #11)
- Changed factory() to throw an exception if the configuration directory not found. (Ticket #25) (Piece_Right_Config_Factory)
- Added _setRule(), _setRuleMessage(), and _getRule().
  setRule(), setRuleMessage(), and getRule() are deprecated since Piece_Right 1.6.0. (Ticket #21) (Piece_Right_Validator_Common)
- Changed the filter mechanism so as to pass an array to the filter directly if it is arrayable.

Validators:

- Added "Unique" validator which can be used to check that all values in an array do not duplicate. (Ticket #26)
- Added "UniqueFields" validator which can be used to check that each value of multiple fields do not duplicate. (Ticket #26)
- Added a rule "directory" for loading a class from the specified directory if the class file is not loaded before invoking a method. (WithMethod)
- Changed the callback interface so as to pass the Piece_Right_Results object to a method. (WithMethod)

Filters:

- Added "NoFile2NULL" filter which converts a $_FILES element with UPLOAD_ERR_NO_FILE to NULL. (Ticket #28)

Defect Fixes
============

Validators:

- Fixed a defect that caused a payload object to be passed by value to the specified method with PHP4. (Ticket #23) (WithMethod)
- Fixed a defect that caused a validation to be passed regardless of whether or not a year value is within a valid range of Japanese era if the rule "isJapaneseEra" was used. (Ticket #22) (Date)';

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

Piece_Right provides a generic validation system which make it easy to validate input values on Web applications. Piece_Right includes a lot of ready-to-use built-in validators. This can make it a lot faster to get started with Piece_Right in existing web applications and web application frameworks.');
$package->setChannel('pear.piece-framework.com');
$package->setLicense('BSD License (revised)',
                     'http://www.opensource.org/licenses/bsd-license.php'
                     );
$package->setAPIVersion($apiVersion);
$package->setAPIStability($apiStability);
$package->setReleaseVersion($releaseVersion);
$package->setReleaseStability($releaseStability);
$package->setNotes($notes);
$package->setPhpDep('4.3.0');
$package->setPearinstallerDep('1.4.3');
$package->addPackageDepWithChannel('required', 'Cache_Lite', 'pear.php.net', '1.7.0');
$package->addPackageDepWithChannel('required', 'PEAR', 'pear.php.net', '1.4.3');
$package->addPackageDepWithChannel('optional', 'Stagehand_TestRunner', 'pear.piece-framework.com', '0.4.0');
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
