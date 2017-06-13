<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\DocumentoContable;
use Modulos\PersonasBundle\Form\DocumentoContableType;

use Modulos\PersonasBundle\Entity\EstadoDocumento;
use Modulos\PersonasBundle\Entity\TipoProductoContable;



/**
 * DocumentoContable controller.
 *
 */
class DocumentoContableController extends Controller
{

    /**
     * Lists all DocumentoContable entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:DocumentoContable')->findAll();

        return $this->render('ModulosPersonasBundle:DocumentoContable:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new DocumentoContable entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new DocumentoContable();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el documento contable'
            );   
            return $this->redirect($this->generateUrl('documentocontable', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:DocumentoContable:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a DocumentoContable entity.
     *
     * @param DocumentoContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DocumentoContable $entity)
    {
        $form = $this->createForm(new DocumentoContableType(), $entity, array(
            'action' => $this->generateUrl('documentocontable_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new DocumentoContable entity.
     *
     */
    public function newAction()
    {
        $entity = new DocumentoContable();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:DocumentoContable:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DocumentoContable entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:DocumentoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DocumentoContable entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:DocumentoContable:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DocumentoContable entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:DocumentoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DocumentoContable entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:DocumentoContable:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a DocumentoContable entity.
    *
    * @param DocumentoContable $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(DocumentoContable $entity)
    {
        $form = $this->createForm(new DocumentoContableType(), $entity, array(
            'action' => $this->generateUrl('documentocontable_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing DocumentoContable entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:DocumentoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DocumentoContable entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el documento contable'
            );
            return $this->redirect($this->generateUrl('documentocontable', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:DocumentoContable:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a DocumentoContable entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:DocumentoContable')->find($id);
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
                    return $this->redirect($this->generateUrl('documentocontable'));
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
        return $this->redirect($this->generateUrl('documentocontable'));
    }

   
    /**
     * Creates a form to delete a DocumentoContable entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('documentocontable_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
