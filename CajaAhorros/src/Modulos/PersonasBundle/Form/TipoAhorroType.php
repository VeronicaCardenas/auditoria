<?php

namespace Modulos\PersonasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TipoAhorroType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
//            ->add('plazo')
            //->add('montoMinimo')
            //->add('montoMaximo')
            ->add('tasaInteres')
            ->add('metodoAmortizacionAhorro','entity',array('class'=>'Modulos\PersonasBundle\Entity\MetodoAmortizacionAhorro','required'=>true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $repository) {
                    return $repository->createQueryBuilder('m')
                        ->where('m.activo =:activo')
                        ->setParameter('activo', true);
                }
            ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Modulos\PersonasBundle\Entity\TipoAhorro'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'modulos_personasbundle_tipoahorro';
    }
}
