<?php

namespace App\Devintech\Service\MetierManagerBundle\Metier\DitServiceType;

use App\Devintech\Service\MetierManagerBundle\Entity\DitServiceType;
use Doctrine\ORM\EntityManager;
use App\Devintech\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierDitServiceType
{
    private $_entity_manager;
    private $_container;

    public function __construct(EntityManager $_entity_manager, Container $_container)
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }

    /**
     * Ajouter un message flash
     * @param string $_type
     * @param string $_message
     * @return mixed
     */
    public function setFlash($_type, $_message) {
        return $this->_container->get('session')->getFlashBag()->add($_type, $_message);
    }

    /**
     * Récuperer le repository type service
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::DIT_SERVICE_TYPE);
    }

    /**
     * Récuperer tout les type services
     * @return array
     */
    public function getAllDitServiceType()
    {
        return $this->getRepository()->findBy(array(), array('id' => 'DESC'));
    }

    /**
     * Récuperer un type service par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getDitServiceTypeById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un type service
     * @param DitServiceType $_service_type
     * @param string $_action
     * @return boolean
     */
    public function saveDitServiceType($_service_type, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_service_type);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un type service
     * @param DitServiceType $_service_type
     * @return boolean
     */
    public function deleteDitServiceType($_service_type)
    {
        $this->_entity_manager->remove($_service_type);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un type service
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupDitServiceType($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_service_type = $this->getDitServiceTypeById($_id);
                $this->deleteDitServiceType($_service_type);
            }
        }

        return true;
    }
}