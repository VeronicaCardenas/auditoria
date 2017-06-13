<?php
namespace Modulos\SeguridadBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PasswordType extends AbstractType{
  
    public function buildForm(FormBuilderInterface $builder, array $opcion)
    {
         $builder
         ->add('password','repeated',array('type'=>'password',
              'invalid_message'=>'Las dos contraseñas deben coincidir.','options'=>array('label'=>'Nueva Contraseña:'),'attr'=>array('autocomplete' => 'off','required' => true)))
         ;
    }
    
    public function getName()
    {
       return 'contrasena';
    } 
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'Modulos\SeguridadBundle\Entity\Usuario'
       ));
    }
}

?>
