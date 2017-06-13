<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 7/07/14
 * Time: 10:37
 */

namespace Modulos\SeguridadBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ldapType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $opcion)
    {
        $builder->add('nombre','text',array('label'=>'Servidor ldap','required'=>'required','attr'=>array('title'=>'Campo obligatorio','class'=>'form-control')))
            ->add('puerto','text',array('label'=>'Puerto','required'=>true,'attr'=>array('title'=>'Campo obligatorio','class'=>'form-control')))
            ->add('basedn','textarea',array('label'=>'Base_DN','required'=>true,'attr'=>array('title'=>'Campo obligatorio','class'=>'form-control')))
            ->add('user','text',array('label'=>'Usuario','required'=>true,'attr'=>array('title'=>'Campo obligatorio','class'=>'form-control')))
            ->add('pass','password',array('label'=>'Contraseña','required'=>false,'attr'=>array('title'=>'Campo obligatorio','class'=>'form-control','autocomplete' => 'off')))
            ->add('dominio','text',array('label'=>'Dominio','required'=>true,'attr'=>array('title'=>'Campo obligatorio','class'=>'form-control')))
            ;
    }
    public function getName()
    {
        return 'ldap';
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Modulos\SeguridadBundle\Entity\ldap',
            ));
    }
} 