<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\ProductoDeCreditos;
use Modulos\PersonasBundle\Form\ProductoDeCreditosType;

/**
 * ProductoDeCreditos controller.
 *
 */
class ProductoDeCreditosController extends Controller
{

    /**
     * Lists all ProductoDeCreditos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->findAll();

        return $this->render('ModulosPersonasBundle:ProductoDeCreditos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ProductoDeCreditos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ProductoDeCreditos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el tipo de crédito'
            ); 
            return $this->redirect($this->generateUrl('productodecreditos', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:ProductoDeCreditos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ProductoDeCreditos entity.
     *
     * @param ProductoDeCreditos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProductoDeCreditos $entity)
    {
        $form = $this->createForm(new ProductoDeCreditosType(), $entity, array(
            'action' => $this->generateUrl('productodecreditos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProductoDeCreditos entity.
     *
     */
    public function newAction()
    {
        $entity = new ProductoDeCreditos();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:ProductoDeCreditos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ProductoDeCreditos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoDeCreditos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ProductoDeCreditos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ProductoDeCreditos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoDeCreditos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ProductoDeCreditos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ProductoDeCreditos entity.
    *
    * @param ProductoDeCreditos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ProductoDeCreditos $entity)
    {
        $form = $this->createForm(new ProductoDeCreditosType(), $entity, array(
            'action' => $this->generateUrl('productodecreditos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ProductoDeCreditos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoDeCreditos entity.');
        }

        $editForm = $this->createForm(new ProductoDeCreditosType(),$entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el tipo de crédito'
            );

            return $this->redirect($this->generateUrl('productodecreditos', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:ProductoDeCreditos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a ProductoDeCreditos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el tipo de crédito'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('productodecreditos'));
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
        return $this->redirect($this->generateUrl('productodecreditos'));
    }
    
    /**
     * Creates a form to delete a ProductoDeCreditos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productodecreditos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
