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
namespace WHAAM\PrivateApplication\Common\Util;

class LocaleValidator {

    private $validOptions = array(
        'el_GR',
        'en_GB',
        'it_IT',
        'pt_PT'
    );

    /**
     * Check if the locale if valid
     *
     * @param $locale
     * @return bool
     */
    public function isValidLocale($locale)
    {
        return in_array($locale, $this->validOptions);
    }
} 