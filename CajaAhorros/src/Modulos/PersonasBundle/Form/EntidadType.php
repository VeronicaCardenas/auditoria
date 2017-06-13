<?php

namespace Modulos\PersonasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntidadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('razonSocial')
            ->add('nombreFantasia')
            ->add('direccion')
            ->add('telefono')
            ->add('correo')
            ->add('ruc')            
            ->add('fechaCreacion','datetime',[
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control datetimepicker1',
                    'data-date-format' => 'DD-MM-YYYY'
                ],
                'required'=>false,

            ])
            ->add('descripcion',"textarea")
            ->add('logo','file', array('required' => false,'attr'=>array('class'=>'form-control')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Modulos\PersonasBundle\Entity\Entidad'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'modulos_personasbundle_entidad';
    }
}
