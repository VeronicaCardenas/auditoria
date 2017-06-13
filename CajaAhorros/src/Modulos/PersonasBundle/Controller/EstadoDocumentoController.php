<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\EstadoDocumento;
use Modulos\PersonasBundle\Form\EstadoDocumentoType;

/**
 * EstadoDocumento controller.
 *
 */
class EstadoDocumentoController extends Controller
{

    /**
     * Lists all EstadoDocumento entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:EstadoDocumento')->findAll();

        return $this->render('ModulosPersonasBundle:EstadoDocumento:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new EstadoDocumento entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EstadoDocumento();
        $form = $this->createForm(new EstadoDocumentoType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el estado documento'
            ); 

            return $this->redirect($this->generateUrl('estadodocumento', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:EstadoDocumento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a EstadoDocumento entity.
     *
     * @param EstadoDocumento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createCreateForm(EstadoDocumento $entity)
    {
        $form = $this->createForm(new EstadoDocumentoType(), $entity, array(
            'action' => $this->generateUrl('estadodocumento_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new EstadoDocumento entity.
     *
     */
   /* public function newAction()
    {
        $entity = new EstadoDocumento();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:EstadoDocumento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a EstadoDocumento entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:EstadoDocumento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoDocumento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:EstadoDocumento:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing EstadoDocumento entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:EstadoDocumento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoDocumento entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:EstadoDocumento:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a EstadoDocumento entity.
    *
    * @param EstadoDocumento $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EstadoDocumento $entity)
    {
        $form = $this->createForm(new EstadoDocumentoType(), $entity, array(
            'action' => $this->generateUrl('estadodocumento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing EstadoDocumento entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:EstadoDocumento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoDocumento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el estado documento'
            );

            return $this->redirect($this->generateUrl('estadodocumento', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:EstadoDocumento:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a EstadoDocumento entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:EstadoDocumento')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el documento contable'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('estadodocumento'));
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
        return $this->redirect($this->generateUrl('estadodocumento'));
    }

    
    /**
     * Creates a form to delete a EstadoDocumento entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('estadodocumento_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
