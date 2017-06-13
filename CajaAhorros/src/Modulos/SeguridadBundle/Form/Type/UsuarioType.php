<?php
namespace Modulos\SeguridadBundle\Form\Type;
use Modulos\SeguridadBundle\Entity\trazaUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;

class UsuarioType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $opcion)
    {
        $builder->add('nombre','text',array('label'=>'Nombre y Apellidos:','required'=>false,'attr'=>array('class'=>'form-control')))
            ->add('ci','text',array('required'=>false,'attr'=>array('class'=>'form-control')))
            ->add('sexo','entity',array('class'=>'Modulos\\SeguridadBundle\\Entity\\Sexo','empty_value'=>'--Seleccione--','required'=>false,'attr'=>array('class'=>'selectpicker form-control')))
            ->add('direccion','textarea',array('required'=>false,'attr'=>array('class'=>'form-control')))
            ->add('telefono','text',array('required'=>false,'attr'=>array('class'=>'form-control')))
            ->add('email','text',array('required'=>false,'attr'=>array('class'=>'form-control')))
            ->add('usuario','text',array('required'=>true,'attr'=>array('title'=>'Campo obligatorio','class'=>'form-control')))
            ->add('password','repeated',array('type'=>'password',
                    'invalid_message'=>'Las dos contraseÃ±as deben coincidir.','options'=>array('label'=>'ContraseÃ±a'),'attr'=>array('autocomplete' => 'off','required' => true,'class'=>'form-control')))
            ->add('role','entity',array('class'=>'Modulos\\SeguridadBundle\\Entity\\Role', 'attr'=> array('name'=>'RolePost','title'=>'Campo obligatorio','class'=>'form-control'),'expanded'=>true,'multiple'=>true, 'required'=>true))
            ->add('foto','file', array('required' => false,'attr'=>array('class'=>'form-control')))
            ;
       /* $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event){
                $user = $event->getData();
                $form = $event->getForm();

        });*/
    }

    public function getName()
    {
       return 'usuario';
    } 
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'Modulos\SeguridadBundle\Entity\Usuario',
       ));
    }



     
}

?>
