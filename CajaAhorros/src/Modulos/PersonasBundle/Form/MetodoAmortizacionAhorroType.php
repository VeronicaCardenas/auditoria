<?php

namespace Modulos\PersonasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MetodoAmortizacionAhorroType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('metodo')
            ->add('nombreMetodo')
            ->add('descripcion','textarea')
            ->add('activo')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Modulos\PersonasBundle\Entity\MetodoAmortizacionAhorro'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'modulos_personasbundle_metodoamortizacionahorro';
    }
}
