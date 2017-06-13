<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 05/02/2016
 * Time: 10:42
 */
namespace Modulos\PersonasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreditosSimularType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('fechaSolicitud')
            ->add('fechaSolicitud','datetime',[
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm:ss',
                'attr' => [
                    'class' => 'form-control datetimepicker1',
                    'data-date-format' => 'DD-MM-YYYY HH:mm:ss'
                ],
                'required'=>true,

            ])
            ->add('id')
            ->add('numeroDePagos')
            ->add('montoSolicitado')
            ->add('interesAnual')
            ->add('persona','entity',array('class'=>'Modulos\PersonasBundle\Entity\Persona','empty_value'=>'--Seleccione--','required'=>true,'attr'=>array('title'=>'Campo obligatorio'),
                'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->where('p.tipo_persona =:id')
                        ->andWhere('p.estadoPersona= 0')
                        ->setParameter('id', 2)
                        ->orderBy('p.primerApellido','ASC');
                }
            ))
            ->add('frecuencia_pago','entity',array('class'=>'Modulos\\PersonasBundle\\Entity\\FrecuenciaDePagos','required'=>true))
            ->add('id_productos_de_creditos','entity',array('class'=>'Modulos\\PersonasBundle\\Entity\\ProductoDeCreditos','empty_value'=>'--Seleccione--','required'=>true))


        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Modulos\PersonasBundle\Entity\CreditosSimular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'modulos_personasbundle_creditos';
    }
}
