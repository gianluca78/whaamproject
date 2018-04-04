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
namespace WHAAM\PrivateApplication\Common\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use WHAAM\PrivateApplication\Common\Security\Encoder\OpenSslEncoder;

class EncryptedStringType extends StringType {

    const MTYPE = 'encrypted_string';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $openSslEncoder = new OpenSslEncoder();

        return $openSslEncoder->decrypt($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $openSslEncoder = new OpenSslEncoder();

        return $openSslEncoder->encrypt($value);
    }

    public function getName()
    {
        return self::MTYPE;
    }
} 