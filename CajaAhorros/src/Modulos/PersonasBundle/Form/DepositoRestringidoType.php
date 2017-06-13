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

class DepositoRestringidoType extends AbstractType
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
            ->add('productocontableid','entity',array('class'=>'Modulos\PersonasBundle\Entity\TipoProductoContable','required'=>true,
                'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->where('p.id = 14')
                        ->orderBy('p.tipo','ASC');
                }
            ))
            ->add('persona')
            ->add('cuentaid','entity',array('class'=>'Modulos\PersonasBundle\Entity\Cuenta','required'=>true,
                'query_builder'=>function(\Doctrine\ORM\EntityRepository $repository){
                    return $repository->createQueryBuilder('p')
                        ->where('p.id = 32')
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