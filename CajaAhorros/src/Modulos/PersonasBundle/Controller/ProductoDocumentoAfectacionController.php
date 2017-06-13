<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\ProductoDocumentoAfectacion;
use Modulos\PersonasBundle\Form\ProductoDocumentoAfectacionType;



/**
 * ProductoDocumentoAfectacion controller.
 *
 */
class ProductoDocumentoAfectacionController extends Controller
{

    /**
     * Lists all ProductoDocumentoAfectacion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:ProductoDocumentoAfectacion')->findAll();

        return $this->render('ModulosPersonasBundle:ProductoDocumentoAfectacion:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ProductoDocumentoAfectacion entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ProductoDocumentoAfectacion();
        $form = $this->createForm(new ProductoDocumentoAfectacionType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el producto documento afectacion'
            );   
            return $this->redirect($this->generateUrl('productodocumentoafectacion', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:ProductoDocumentoAfectacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ProductoDocumentoAfectacion entity.
     *
     * @param ProductoDocumentoAfectacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createCreateForm(ProductoDocumentoAfectacion $entity)
    {
        $form = $this->createForm(new ProductoDocumentoAfectacionType(), $entity, array(
            'action' => $this->generateUrl('productodocumentoafectacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new ProductoDocumentoAfectacion entity.
     *
     */
   /* public function newAction()
    {
        $entity = new ProductoDocumentoAfectacion();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:ProductoDocumentoAfectacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a ProductoDocumentoAfectacion entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoDocumentoAfectacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoDocumentoAfectacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ProductoDocumentoAfectacion:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ProductoDocumentoAfectacion entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoDocumentoAfectacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoDocumentoAfectacion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ProductoDocumentoAfectacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ProductoDocumentoAfectacion entity.
    *
    * @param ProductoDocumentoAfectacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ProductoDocumentoAfectacion $entity)
    {
        $form = $this->createForm(new ProductoDocumentoAfectacionType(), $entity, array(
            'action' => $this->generateUrl('productodocumentoafectacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ProductoDocumentoAfectacion entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoDocumentoAfectacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoDocumentoAfectacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el producto documento afectacion'
            );   

            return $this->redirect($this->generateUrl('productodocumentoafectacion', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:ProductoDocumentoAfectacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ProductoDocumentoAfectacion entity.
     *
     */
     public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:ProductoDocumentoAfectacion')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el producto documento afectacion'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('productodocumentoafectacion'));
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
        return $this->redirect($this->generateUrl('productodocumentoafectacion'));
    }


    /**
     * Creates a form to delete a ProductoDocumentoAfectacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productodocumentoafectacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
