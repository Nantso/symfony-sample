<?php

namespace App\Devintech\BackOffice\AdminBundle\Controller;

use App\Devintech\Service\MetierManagerBundle\Form\DitServiceType;
use App\Devintech\Service\MetierManagerBundle\Utils\ServiceName;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Devintech\Service\MetierManagerBundle\Entity\DitService;

/**
 * Class DitServiceController
 */
class DitServiceController extends Controller
{
    /**
     * Afficher tout les services
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_service_manager = $this->get(ServiceName::SRV_METIER_SERVICE);

        // Récupérer tout les service
        $_services = $_service_manager->getAllDitService();

        return $this->render('AdminBundle:DitService:index.html.twig', array(
            'services' => $_services
        ));
    }

    /**
     * Affichage page modification service
     * @param DitService $_service
     * @return Render page
     */
    public function editAction(DitService $_service)
    {
        if (!$_service) {
            throw $this->createNotFoundException('Unable to find DitService entity.');
        }

        $_edit_form = $this->createEditForm($_service);

        return $this->render('AdminBundle:DitService:edit.html.twig', array(
            'service'   => $_service,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création service
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_service_manager = $this->get(ServiceName::SRV_METIER_SERVICE);

        $_service = new DitService();
        $_form    = $this->createCreateForm($_service);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement service
            $_service_manager->saveDitService($_service, 'new');

            $_service_manager->setFlash('success', "Service ajouté");

            return $this->redirect($this->generateUrl('service_index'));
        }

        return $this->render('AdminBundle:DitService:add.html.twig', array(
            'service' => $_service,
            'form'    => $_form->createView(),
        ));
    }

    /**
     * Modification service
     * @param Request $_request requête
     * @param DitService $_service
     * @return Render page
     */
    public function updateAction(Request $_request, DitService $_service)
    {
        // Récupérer manager
        $_service_manager = $this->get(ServiceName::SRV_METIER_SERVICE);

        if (!$_service) {
            throw $this->createNotFoundException('Unable to find DitService entity.');
        }

        $_edit_form = $this->createEditForm($_service);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_service_manager->saveDitService($_service, 'update');

            $_service_manager->setFlash('success', "Service modifié");

            return $this->redirect($this->generateUrl('service_index'));
        }

        return $this->render('AdminBundle:DitService:edit.html.twig', array(
            'service'   => $_service,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition service
     * @param DitService $_service The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DitService $_service)
    {
        $_form = $this->createForm(DitServiceType::class, $_service, array(
            'action' => $this->generateUrl('service_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création service
     * @param DitService $_service The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DitService $_service)
    {
        $_form = $this->createForm(DitServiceType::class, $_service, array(
            'action' => $this->generateUrl('service_update', array('id' => $_service->getId())),
            'method' => 'PUT'
        ));

        return $_form;
    }

    /**
     * Suppression service
     * @param Request $_request requête
     * @param DitService $_service
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, DitService $_service)
    {
        // Récupérer manager
        $_service_manager = $this->get(ServiceName::SRV_METIER_SERVICE);

        $_form = $this->createDeleteForm($_service);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression service
            $_service_manager->deleteDitService($_service);

            $_service_manager->setFlash('success', 'Service supprimé');
        }

        return $this->redirectToRoute('service_index');
    }

    /**
     * Création formulaire de suppression service
     * @param DitService $_service The DitService entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DitService $_service)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('service_delete', array('id' => $_service->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste service
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_service_manager = $this->get(ServiceName::SRV_METIER_SERVICE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            if ($_ids == null) {
                $_service_manager->setFlash('error', 'Veuillez sélectionner un élément à supprimer');

                return $this->redirect($this->generateUrl('service_index'));
            }
            $_service_manager->deleteGroupDitService($_ids);
        }

        $_service_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('service_index'));
    }
}