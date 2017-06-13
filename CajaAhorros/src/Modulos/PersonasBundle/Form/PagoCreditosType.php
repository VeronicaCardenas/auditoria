<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 19/02/2016
 * Time: 14:07
 */

namespace Modulos\PersonasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PagoCreditosType extends AbstractType
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
                'required'=>false,

            ])
            ->add('numeroRecibo')
            ->add('debe')
            ->add('haber')
            ->add('saldo')
//            ->add('productocontableid')
            ->add('productocontableid','entity',array('class'=>'Modulos\PersonasBundle\Entity\TipoProductoContable','required'=>true,
                'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->where('p.id = 9')
                        ->orderBy('p.tipo','ASC');
                }
            ))
            ->add('persona','entity',array('class'=>'Modulos\PersonasBundle\Entity\Persona','empty_value'=>'--Seleccione--','required'=>true,'attr'=>array('title'=>'Introducir el nombre completo de la persona'),
                'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->where('p.tipo_persona =:id')
                        ->andWhere('p.estadoPersona= 0')
                        ->setParameter('id', 2)
                        ->orderBy('p.primerApellido','ASC');
                }
            ))
            ->add('cuentaid','entity',array('class'=>'Modulos\PersonasBundle\Entity\Cuenta','required'=>true,
                'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->where('p.id = 10')
                        ->orderBy('p.id','ASC');
                }
            ))
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