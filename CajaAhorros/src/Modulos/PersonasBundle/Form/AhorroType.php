<?php

namespace Modulos\PersonasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AhorroType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('fechaSolicitud','datetime',[
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm:ss',
                'attr' => [
                    'class' => 'form-control datetimepicker1',
                    'data-date-format' => 'DD-MM-YYYY HH:mm:ss'
                ],
                'required'=>true,

            ])

            ->add('fechafinal','datetime',[
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm:ss',
                'attr' => [
                    'class' => 'form-control datetimepicker1',
                    'data-date-format' => 'DD-MM-YYYY HH:mm:ss'
                ],
                'required'=>true,

            ])

            ->add('persona','entity',array('class'=>'Modulos\PersonasBundle\Entity\Persona','empty_value'=>'--Seleccione--','required'=>true,'attr'=>array('title'=>'Campo obligatorio'),
                'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->where('p.tipo_persona =:id')
                        ->andWhere('p.estadoPersona= 0')
                        ->setParameter('id', 2)
                        ->orderBy('p.primerApellido','ASC');
                }
            ))
            ->add('tipoAhorro','entity',array('class'=>'Modulos\\PersonasBundle\\Entity\\TipoAhorro','empty_value'=>'--Seleccione--','required'=>true))
            ->add('valorAhorrar','number',array('required'=>true))
            //->add('cuotas','integer',['required'=>true])
            ->add('valorEnCaja','number',array('required'=>true))
            ->add('tasaInteres')
            ->add('frecuenciadepago','entity',array('class'=>'Modulos\\PersonasBundle\\Entity\\FrecuenciaDePagos','required'=>true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $repository) {
                    return $repository->createQueryBuilder('v')
                        ->where('v.id =:id')
                        ->setParameter('id', 2);
                }
            ))
            ->add('estadoAhorro','entity',array('class'=>'Modulos\\PersonasBundle\\Entity\\EstadoAhorro','required'=>true,
                'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->orderBy('p.id','ASC');
                }))
            ->add('id')

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Modulos\PersonasBundle\Entity\Ahorro'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'modulos_personasbundle_ahorro';
    }
}
