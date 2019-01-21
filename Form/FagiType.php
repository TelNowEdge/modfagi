<?php

/*
 * Copyright 2016 TelNowEdge
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace TelNowEdge\Module\modfagi\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\FreePBX\Base\Form\DestinationType;
use TelNowEdge\Module\modfagi\Model\Fagi;

class FagiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('displayName')
            ->add('description')
            ->add('host')
            ->add('port', IntegerType::class)
            ->add('path',null, array(
                'label' => 'Function',
                'label_attr' => array(
                    'fpbx_help' => 'Parametres send to FAGI at create.Format is ',
                ),
            ))
            ->add('query',null, array(
                'label_attr' => array(
                    'fpbx_help' => 'Parametres send to FAGI at create.Format is var1=value&var2=value&...',
                ),
            ))
            ->add('fagiResults', CollectionType::class, array(
                'label_attr' => array(
                    'fpbx_help' => 'FAGI set channel variable ${FAGIRUN} and goto destination after test.',
                ),
                'entry_type' => FagiResultType::class,
                'entry_options' => array(
                    'label' => false,
                    'attr' => array(
                        'data-child' => true,
                    ),
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => true,
            ))
            ->add('fallback', DestinationType::class, array(
                'label_attr' => array(
                    'fpbx_help' => 'Destination if ${FAGIRUN} not match.',
                ),
                'required' => true,
            ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => Fagi::class,
            ));
    }
}
