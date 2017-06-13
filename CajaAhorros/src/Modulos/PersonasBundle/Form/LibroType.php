<?php

namespace Modulos\PersonasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LibroType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha','datetime',[
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm:ss',
                'attr' => [
                    'class' => 'form-control datetimepicker1',
                    'data-date-format' => 'DD-MM-YYYY HH:mm:ss'
                ],
                'required'=>true,

            ])
            ->add('numeroRecibo')
            ->add('debe')
            ->add('haber')
            ->add('saldo')
//            ->add('productocontableid')
            ->add('productocontableid','entity',array('class'=>'Modulos\PersonasBundle\Entity\TipoProductoContable','empty_value'=>'--Seleccione una Transacción--','required'=>true,
                  'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->orderBy('p.tipo','ASC');
                }
                 ))
            ->add('persona','entity',array('class'=>'Modulos\PersonasBundle\Entity\Persona','empty_value'=>'--Seleccione--','required'=>false,'attr'=>array('title'=>'Introducir el nombre completo de la persona'),
                  'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->orderBy('p.primerApellido','ASC');
                }
                 ))
            ->add('cuentaid')
            ->add('info')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Modulos\PersonasBundle\Entity\Libro'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'modulos_personasbundle_libro';
    }
}
