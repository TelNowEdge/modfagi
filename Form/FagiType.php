<?php

namespace TelNowEdge\Module\modfagi\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\Module\modfagi\Model\Fagi;
use TelNowEdge\FreePBX\Base\Form\DestinationType;

class FagiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('displayName')
            ->add('description')
            ->add('host')
            ->add('port')
            ->add('path')
            ->add('query')
            ->add('trueGoto', DestinationType::class, array(
                'label' => _('[AGI] Success')
            ))
            ->add('falseGoto', DestinationType::class, array(
                'label' => _('[AGI] Error')
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
