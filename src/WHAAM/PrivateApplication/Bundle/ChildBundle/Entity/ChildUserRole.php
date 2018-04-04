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
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\RoleBase;

/**
 * @ORM\Entity()
 * @ORM\Table(name="children_users_roles")
 * @Gedmo\TranslationEntity(class="WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Translation\ChildUserRoleTranslation")
 */
class ChildUserRole extends RoleBase
{
    /**
     * @var string
     *
     * @Gedmo\Locale
     */
    private $locale;

    /**
     * Set the locale for the translatable behavior
     *
     * @param string $locale
     */
    public function setTranslatableLocale($locale) {
        $this->locale = $locale;
    }
}
