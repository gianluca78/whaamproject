<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Gianluca Merlo
 */
namespace WHAAM\PrivateApplication\Common\Tests\Util;

use WHAAM\PrivateApplication\Common\Util\LocaleValidator;

class LocaleValidatorTest extends \PHPUnit_Framework_TestCase {

    public function testIsValidLocale()
    {
        $localeValidator = new LocaleValidator();

        $this->assertTrue($localeValidator->isValidLocale('el_GR'));
        $this->assertTrue($localeValidator->isValidLocale('en_GB'));
        $this->assertTrue($localeValidator->isValidLocale('it_IT'));
        $this->assertTrue($localeValidator->isValidLocale('pt_PT'));
        $this->assertFalse($localeValidator->isValidLocale('invalid_invalid'));
    }
}
 