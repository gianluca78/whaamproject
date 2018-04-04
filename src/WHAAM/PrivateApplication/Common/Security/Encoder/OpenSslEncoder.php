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
namespace WHAAM\PrivateApplication\Common\Security\Encoder;

class OpenSslEncoder {

    const CYPHER_METHOD = 'aes128';
    const IV = 'hou7gcd3987yhngr';
    const PRIVATE_KEY = 'MIICWwIBAAKBgGS3UxakZD1cKiBbAOaATnaokZaICvYelPCUb14Uufx0qeC6iuZn
        FVTxQqplFh9+z688+hLkRMdTlW5PLY8BFLXktxDHHjnKkKHARoRZ7UM8FyWY9Yyv
        LRpxzcdHECgcrUcO1n1rfE11H3MIvcc8pV5cpO8OChnnbwvXIb7jDtRTAgMBAAEC
        gYAR7fzxs/RQZB7vwaOoZUESqLG6XZ+t7wXOym4FDqWJhu9THYJqwAExLP4UPYix
        tK6eYLmYMWD5Jy0cBSZ0Jdocyk1DeMT+lHhBrTMVfPwfA7Py+8RQJa6uusTypZh4
        VMSkv3od9GuMgDY2fu3X68YCNbLrbbehDawWgQ3O81UBsQJBAKhmWfSXGfjnnUTu
        D/LQxJm0sIBpv/rB5ABOwNKP1lfj6uhWQ7izlmnGXA2QCKBMvDroRLP0rqkANtGH
        oz4QXOkCQQCZG5jTnV21NGnNEEsmXkynXcTGO/nGMhwlig2TCtIEC+vejiYzrwAe
        AVPHrD6siofDfgR/oCSKFi5/ypmn2PHbAkA2idduYJV3yENl9JfTswtJIHzdSeJ0
        KuFVvCu0xgLMOjN1BaMvKXJ4VVawycoRaGi5x0mg4ojCkSAv4fbbfd8pAkAWqBDL
        b8QIJoNphvm36chqE+QkeYeSnqOvomgz1CwT0TfMLTjV+RWJWyTsrT5xBeblNOax
        hB8kF/g1jOOEBQR1AkEAhdRn6Y/RRU5MCrFb6wO45LaZ5z2pSg9kzgYhkez6+duQ
        cq12MOKcWtNY7LFJ/MoXrmIpx4sDxsh27PyHV99NUA==';

    public function encrypt($value)
    {
        return openssl_encrypt($value, self::CYPHER_METHOD, self::PRIVATE_KEY, false, self::IV);
    }

    public function decrypt($value)
    {
        return openssl_decrypt($value, self::CYPHER_METHOD, self::PRIVATE_KEY, false, self::IV);
    }

} 