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

class ChildMedicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null, array(
                    'label' => 'medication.label',
                    'required' => true
                )
            )
            ->add('dosage', null, array(
                    'label' => 'dosage.label',
                    'required' => true
                )
            )
            ->add('frequency', 'textarea', array(
                    'label' => 'observation_type_frequency.option',
                    'required' => true
                )
            )
            ->add('startDate','date', array(
                    'label' => 'start_date.label',
                    'years' => range(date('Y')-20, date('Y'), 1),
                    'empty_value' => array(
                        'year' => 'year_empty_value.label',
                        'month' => 'month_empty_value.label',
                        'day' => 'day_empty_value.label'
                    ),
                    'required' => true
                )
            )
            ->add('endDate','date', array(
                    'label' => 'end_date.label',
                    'years' => range(date('Y')-20, date('Y'), 1),
                    'empty_value' => array(
                        'year' => 'year_empty_value.label',
                        'month' => 'month_empty_value.label',
                        'day' => 'day_empty_value.label'
                    ),
                    'required' => false
                )
            )

            ->add('submit', 'submit', array(
                    'label' => 'save.label'
                )
            );
    }

    public function getName()
    {
        return 'childMedication';
    }
}