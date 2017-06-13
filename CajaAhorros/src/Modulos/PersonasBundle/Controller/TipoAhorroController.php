<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\TipoAhorro;
use Modulos\PersonasBundle\Form\TipoAhorroType;

/**
 * TipoAhorro controller.
 *
 */
class TipoAhorroController extends Controller
{

    /**
     * Lists all TipoAhorro entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:TipoAhorro')->findAll();

        return $this->render('ModulosPersonasBundle:TipoAhorro:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TipoAhorro entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TipoAhorro();
        $form = $this->createForm(new TipoAhorroType,$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el tipo de ahorro'
            ); 

            return $this->redirect($this->generateUrl('tipoahorro', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:TipoAhorro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TipoAhorro entity.
     *
     * @param TipoAhorro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoAhorro $entity)
    {
        $form = $this->createForm(new TipoAhorroType(), $entity, array(
            'action' => $this->generateUrl('tipoahorro_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoAhorro entity.
     *
     */
    public function newAction()
    {
        $entity = new TipoAhorro();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:TipoAhorro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TipoAhorro entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoAhorro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoAhorro:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TipoAhorro entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoAhorro entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoAhorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TipoAhorro entity.
    *
    * @param TipoAhorro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoAhorro $entity)
    {
        $form = $this->createForm(new TipoAhorroType(), $entity, array(
            'action' => $this->generateUrl('tipoahorro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoAhorro entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoAhorro entity.');
        }

        $editForm = $this->createForm(new TipoAhorroType(),$entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el tipo de ahorro'
            );

            return $this->redirect($this->generateUrl('tipoahorro', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:TipoAhorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),            
        ));
    }
    /**
     * Deletes a TipoAhorro entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:TipoAhorro')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el tipo de ahorro'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('tipoahorro'));
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

        return $this->redirect($this->generateUrl('tipoahorro'));
    }

    /**
     * Creates a form to delete a TipoAhorro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipoahorro_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
