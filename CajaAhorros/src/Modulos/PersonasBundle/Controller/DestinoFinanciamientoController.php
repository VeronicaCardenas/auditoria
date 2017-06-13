<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\DestinoFinanciamiento;
use Modulos\PersonasBundle\Form\DestinoFinanciamientoType;

/**
 * DestinoFinanciamiento controller.
 *
 */
class DestinoFinanciamientoController extends Controller
{

    /**
     * Lists all DestinoFinanciamiento entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:DestinoFinanciamiento')->findAll();

        return $this->render('ModulosPersonasBundle:DestinoFinanciamiento:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new DestinoFinanciamiento entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new DestinoFinanciamiento();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el destino de financiamiento'
            ); 
            return $this->redirect($this->generateUrl('destinofinanciamiento', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:DestinoFinanciamiento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a DestinoFinanciamiento entity.
     *
     * @param DestinoFinanciamiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DestinoFinanciamiento $entity)
    {
        $form = $this->createForm(new DestinoFinanciamientoType(), $entity, array(
            'action' => $this->generateUrl('destinofinanciamiento_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new DestinoFinanciamiento entity.
     *
     */
    public function newAction()
    {
        $entity = new DestinoFinanciamiento();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:DestinoFinanciamiento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DestinoFinanciamiento entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:DestinoFinanciamiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DestinoFinanciamiento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:DestinoFinanciamiento:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DestinoFinanciamiento entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:DestinoFinanciamiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DestinoFinanciamiento entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:DestinoFinanciamiento:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a DestinoFinanciamiento entity.
    *
    * @param DestinoFinanciamiento $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(DestinoFinanciamiento $entity)
    {
        $form = $this->createForm(new DestinoFinanciamientoType(), $entity, array(
            'action' => $this->generateUrl('destinofinanciamiento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing DestinoFinanciamiento entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:DestinoFinanciamiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DestinoFinanciamiento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el destino de financiamiento'
            );
            return $this->redirect($this->generateUrl('destinofinanciamiento', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:DestinoFinanciamiento:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a DestinoFinanciamiento entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:DestinoFinanciamiento')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el destino de financiamiento'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('destinofinanciamiento'));
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
        return $this->redirect($this->generateUrl('destinofinanciamiento'));
    }

    
    /**
     * Creates a form to delete a DestinoFinanciamiento entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('destinofinanciamiento_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
