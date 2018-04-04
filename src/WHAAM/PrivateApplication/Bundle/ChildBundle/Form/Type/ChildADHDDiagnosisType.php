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

class ChildADHDDiagnosisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('diagnosisDate','date', array(
                    'label' => 'date_of_diagnosis.label',
                    'years' => range(date('Y')-20, date('Y'), 1),
                    'empty_value' => array(
                        'year' => 'year_empty_value.label',
                        'month' => 'month_empty_value.label',
                        'day' => 'day_empty_value.label'
                    ),
                    'required' => true
                )
            )
            ->add('onsetAge', 'number', array(
                    'label' => 'age_of_onset.label',
                    'required' => true,
                    'attr' => array(
                        'class' => 'small-input'
                    )
                )
            )
            ->add('subtype', 'translatedEntity', array(
                    'label' => 'subtype.label',
                    'empty_value' => 'subtype_empty_value.label',
                    'class' => 'WHAAMPrivateApplicationChildBundle:ADHDSubtype',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('s')
                                ->orderBy('s.subtype', 'ASC');
                        },
                    'attr' => array('class' => 'select-width')
                )
            )

            ->add('isSecondaryDisorder', null, array(
                    'label' => 'is_adhd_secondary.label',
                    'required' => false
                )
            )
            ->add('comorbidities', 'translatedEntity', array(
                    'label' => 'comorbidity.label',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'empty_value' => 'comorbidity_empty_value.label',
                    'class' => 'WHAAMPrivateApplicationChildBundle:ADHDComorbidity',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('s')
                                ->orderBy('s.comorbidity', 'ASC');
                        }
                )
            )
            ->add('submit', 'submit', array(
                    'label' => 'save.label'
                )
            );
    }

    public function getName()
    {
        return 'childADHDDiagnosis';
    }
}