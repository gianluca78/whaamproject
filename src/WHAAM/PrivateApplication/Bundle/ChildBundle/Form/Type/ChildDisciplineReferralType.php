<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Giuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

class ChildDisciplineReferralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date','date', array(
                    'label' => 'date_of_discipline_referral.label',
                    'years' => range(date('Y')-20, date('Y'), 1),
                    'empty_value' => array(
                        'year' => 'year_empty_value.label',
                        'month' => 'month_empty_value.label',
                        'day' => 'day_empty_value.label'
                    ),
                    'required' => true
                )
            )
            ->add('disciplineReferralType', 'translatedEntity', array(
                    'attr' => array(
                        'class' => 'select-width'
                    ),
                    'label' => 'discipline_referral.label',
                    'empty_value' => 'discipline_referral_value.label',
                    'class' => 'WHAAMPrivateApplicationChildBundle:DisciplineReferralType',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('s')
                                ->orderBy('s.type', 'ASC');
                        }
                )
            )
            ->add('motivation', 'textarea', array(
                    'label' => 'motivation.label',
                    'required' => true
                )
            )
            ->add('submit', 'submit', array(
                    'label' => 'save.label'
                )
            );
    }

    public function getName()
    {
        return 'childDisciplineReferral';
    }
}