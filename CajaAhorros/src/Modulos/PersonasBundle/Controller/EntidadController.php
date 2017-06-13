<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\Entidad;
use Modulos\PersonasBundle\Form\EntidadType;

/**
 * Entidad controller.
 *
 */
class EntidadController extends Controller
{

    /**
     * Lists all Entidad entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Entidad')->findAll();


        if(count($entities)>0){
            $entity = $entities[0];
        }
        else{
            $entity = "NO ESPECIFICADO";
        }


        return $this->render('ModulosPersonasBundle:Entidad:index.html.twig', array(
            'entity' => $entity,
        ));
    }
    /**
     * Creates a new Entidad entity.
     *
     */
    public function createAction(Request $request)
    {
            $entity = new Entidad();
            $form = $this->createCreateForm($entity,new EntidadType());
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $entity->subirLogo($this->container->getParameter('modulos.directorio.logo'));
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado la cooperativa'
            ); 
                return $this->redirect($this->generateUrl('entidad', array('id' => $entity->getId())));
            }

            return $this->render('ModulosPersonasBundle:Entidad:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),

            ));


    }

    /**
     * Creates a form to create a Entidad entity.
     *
     * @param Entidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Entidad $entity)
    {
        $form = $this->createForm(new EntidadType(), $entity, array(
            'action' => $this->generateUrl('entidad_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Entidad entity.
     *
     */
    public function newAction()
    {
        $entity = new Entidad();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:Entidad:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Entidad entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Entidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entidad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Entidad:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Entidad entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Entidad')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entidad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Entidad:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Entidad entity.
    *
    * @param Entidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Entidad $entity)
    {
        $form = $this->createForm(new EntidadType(), $entity, array(
            'action' => $this->generateUrl('entidad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Entidad entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Entidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entidad entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EntidadType(),$entity);
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {

            //echo $entity->getId();
            $entity->subirLogo($this->container->getParameter('modulos.directorio.logo'));
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado la cooperativa'
            );

            return $this->redirect($this->generateUrl('entidad', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:Entidad:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),

        ));
    }
    /**
     * Deletes a Entidad entity.
     *
     */
     public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Entidad')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado la cooperativa'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('entidad'));
                }
                else
                {
                    throw $e;
                }
            }
            else
            {
                throw $e;
            }
        }
        return $this->redirect($this->generateUrl('entidad'));
    }

    
    /**
     * Creates a form to delete a Entidad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('entidad_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
