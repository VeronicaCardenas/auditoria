<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\Caja;
use Modulos\PersonasBundle\Form\CajaType;

/**
 * Caja controller.
 *
 */
class CajaController extends Controller
{

    /**
     * Lists all Caja entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Caja')->findAll();

        return $this->render('ModulosPersonasBundle:Caja:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Caja entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Caja();
        $form = $this->createForm(new CajaType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado la caja'
            ); 
            return $this->redirect($this->generateUrl('caja', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:Caja:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Caja entity.
     *
     * @param Caja $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
   /* private function createCreateForm(Caja $entity)
    {
        $form = $this->createForm(new CajaType(), $entity, array(
            'action' => $this->generateUrl('caja_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new Caja entity.
     *
     */
    /*public function newAction()
    {
        $entity = new Caja();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:Caja:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a Caja entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Caja')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Caja entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Caja:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Caja entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Caja')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Caja entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Caja:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Caja entity.
    *
    * @param Caja $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Caja $entity)
    {
        $form = $this->createForm(new CajaType(), $entity, array(
            'action' => $this->generateUrl('caja_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Caja entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Caja')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Caja entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado la caja'
            );

            return $this->redirect($this->generateUrl('caja', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:Caja:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Caja entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Caja')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado la caja'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('caja'));
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
        return $this->redirect($this->generateUrl('caja'));
    }

    
    /**
     * Creates a form to delete a Caja entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('caja_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
