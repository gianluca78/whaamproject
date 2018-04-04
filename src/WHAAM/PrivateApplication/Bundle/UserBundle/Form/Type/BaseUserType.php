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
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\True;

class BaseUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('surname', null, array(
                    'label' => 'surname.label',
                    'required' => false
                )
            )
            ->add('name', null, array(
                    'label' => 'name.label',
                    'required' => false
                )
            )
            ->add('sex', 'translatedEntity', array(
                    'label' => 'sex.label',
                    'empty_value' => 'sex_empty_value.label',
                    'class' => 'WHAAMPrivateApplicationUserBundle:Sex',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s');
                    },
                    'required' => true
                )
            )
            ->add('dateOfBirth', null, array(
                    'label' => 'date_of_birth.label',
                    'years' => range(1920, date('Y')-10, 1),
                    'empty_value' => array(
                        'year' => 'year_empty_value.label',
                        'month' => 'month_empty_value.label',
                        'day' => 'day_empty_value.label'
                    )
                )
            )
            ->add('nation', 'translatedEntity', array(
                    'label' => 'nation.label',
                    'empty_value' => 'nation_empty_value.label',
                    'class' => 'WHAAMPrivateApplicationUserBundle:Nation',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('n');
                        }
                )
            )
            ->add('otherNation', null, array(
                    'label' => 'other_nation.label',
                    'required' => false
                )
            )
            ->add('isHealthProfessional', null, array(
                    'label' => 'is_health_professional.label',
                    'required' => false
                )
            )
            ->add('healthProfessionalClientsAgeRange', null, array(
                    'label' => 'hp_clients_age_range.label',
                    'multiple' => true,
                    'expanded' => true,
                    'required' => true
                )
            )
            ->add('healthProfessionalSpecialties', 'translatedEntity', array(
                    'label' => 'hp_specialties.label',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => 'WHAAMPrivateApplicationUserBundle:HealthProfessionalSpecialty',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('s')
                                ->orderBy('s.specialty', 'ASC');
                        },
                    'required' => true
                )
            )
            ->add('healthProfessionalTreatmentApproaches', 'translatedEntity', array(
                    'label' => 'hp_treatment_approaches.label',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => 'WHAAMPrivateApplicationUserBundle:HealthProfessionalTreatmentApproach',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('t')
                                ->orderBy('t.approach', 'ASC');
                        },
                    'required' => true
                )
            )
            ->add('healthProfessionalTreatmentModalities', 'translatedEntity', array(
                    'label' => 'hp_treatment_modalities.label',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => 'WHAAMPrivateApplicationUserBundle:HealthProfessionalTreatmentModality',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('t')
                                ->orderBy('t.modality', 'ASC');
                        },
                    'required' => true
                )
            );
    }

    public function getName()
    {
        return 'base_user';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true
        ));
    }

}