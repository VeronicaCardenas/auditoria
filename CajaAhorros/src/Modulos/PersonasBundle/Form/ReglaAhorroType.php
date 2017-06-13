<?php

namespace Modulos\PersonasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReglaAhorroType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion')
            ->add('variableRegla','entity',array('class'=>'Modulos\PersonasBundle\Entity\VariablesReglas','required'=>true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $repository) {
                    return $repository->createQueryBuilder('v')
                        ->where('v.estado =:estado or v.estado =:estado2')
                        ->setParameter('estado', '1')
                        ->setParameter('estado2', '3');
                }
            ))
            ->add('operador')
            ->add('valor')
            ->add('operador1')
            ->add('variableRegla1','entity',array('class'=>'Modulos\PersonasBundle\Entity\VariablesReglas','required'=>true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $repository) {
                    return $repository->createQueryBuilder('v')
                        ->where('v.estado =:estado or v.estado =:estado2')
                        ->setParameter('estado', '1')
                        ->setParameter('estado2', '3');
                }
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Modulos\PersonasBundle\Entity\ReglaAhorro'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'modulos_personasbundle_reglaahorro';
    }
}
