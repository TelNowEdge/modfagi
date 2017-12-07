<?php

namespace TelNowEdge\Module\modfagi\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('port', NumberType::class)
            ->add('path')
            ->add('query')
            ->add('trueGoto', DestinationType::class, array(
                'label' => _('[AGI] Success'),
            ))
            ->add('falseGoto', DestinationType::class, array(
                'label' => _('[AGI] Error'),
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
