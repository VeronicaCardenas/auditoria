<?php

namespace Modulos\PersonasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text', ['required'=>true])
            ->add('primerApellido', 'text', ['required'=>true])
            ->add('segundoApellido')
            ->add('correo')
            ->add('telefonoFijo')
            ->add('telefonoMovil')
            ->add('profecion')
//            ->add('fechaNacimiento')
            ->add('fechaNacimiento','datetime',[
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control datetimepicker1',
                    'data-date-format' => 'DD-MM-YYYY'
                ],
                'required'=>true,

            ])
            ->add('ci', 'text', ['required'=>true])
//            ->add('fechaCreacion')
            ->add('fechaCreacion','datetime',[
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control datetimepicker1',
                    'data-date-format' => 'DD-MM-YYYY'
                ],
                'required'=>false,

            ])
//            ->add('fechaActualizacion')
            ->add('fechaActualizacion','datetime',[
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control datetimepicker1',
                    'data-date-format' => 'DD-MM-YYYY'
                ],
                'required'=>false,

            ])
            ->add('lugar_nacimiento_e')
            ->add('convive')
            ->add('tipo_persona','entity',array('class'=>'Modulos\PersonasBundle\Entity\TipoPersona','required'=>true))
            ->add('region','entity',array('class'=>'Modulos\PersonasBundle\Entity\Region','required'=>true,
                        'query_builder' => function(\Doctrine\ORM\EntityRepository $repository) {
                            return $repository->createQueryBuilder('v')
                                ->where('v.id =:id')
                                ->setParameter('id', 1);
                })
            )
            ->add('provincia','entity',array('class'=>'Modulos\PersonasBundle\Entity\Provincia','required'=>true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $repository) {
                    return $repository->createQueryBuilder('v')
                        ->where('v.id =:id')
                        ->setParameter('id', 17);
                }))
            ->add('ciudad','entity',array('class'=>'Modulos\PersonasBundle\Entity\Ciudad','required'=>true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $repository) {
                    return $repository->createQueryBuilder('v')
                        ->where('v.id =:id')
                        ->setParameter('id', 178);
                }))
            ->add('parroquia','entity',array('class'=>'Modulos\PersonasBundle\Entity\Parroquia','required'=>true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $repository) {
                    return $repository->createQueryBuilder('v')
                        ->where('v.ciudad =178')
                        ->orderBy('v.id','ASC');
                }))
            ->add('tipo_identificacion','entity',array('class'=>'Modulos\PersonasBundle\Entity\TipoDocIdentificacion','required'=>true))
            ->add('genero','entity',array('class'=>'Modulos\PersonasBundle\Entity\Genero','required'=>true))
            ->add('regimen_fiscal')
            ->add('regimen_matrimonial','entity',array('class'=>'Modulos\PersonasBundle\Entity\RegimenMatrimonial','required'=>true))
            ->add('empresa')
            ->add('entidad')
            ->add('lugardeRecidencia',"textarea", ['required'=>false])
            ->add('foto','file', array('required' => false,'attr'=>array('class'=>'form-control')))
            ->add('descripcionConvive',"textarea")
            ->add('cargo')
            ->add('salarioMensual', 'text', ['required'=>false])
            ->add('estadoPersona', 'checkbox', array(
                'label'    => 'Activo o Inactivo',
                'required' => false,))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Modulos\PersonasBundle\Entity\Persona'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'modulos_personasbundle_persona';
    }
}
