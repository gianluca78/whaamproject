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

/**
 * Compose an array with ABC data
 * @package WHAAM\PrivateApplication\Common\Util
 */
class ABCDataComposer {

    /**
     * Compose the ABC data array
     *
     * @param array $ABCs
     */
    public function compose(array $ABCs)
    {
        $result = array();

        foreach ($ABCs as $index => $ABC) {
            foreach ($ABC[0] as $key => $value) {
                $result[$index][$key] = $value;
                $result[$index]['abcCreatorName'] = $ABC['abcCreatorName'];
                $result[$index]['abcCreatorSurname'] = $ABC['abcCreatorSurname'];
                $result[$index]['abcCreatorUsername'] = $ABC['abcCreatorUsername'];
            }
        }

        return $result;
    }
}