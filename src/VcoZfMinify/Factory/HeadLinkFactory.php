<?php
namespace VcoZfMinify\Factory;

use VcoZfMinify\View\Helper\HeadLink;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HeadLinkFactory implements FactoryInterface {

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator) {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $minifyJsService = $realServiceLocator->get('VcoZfMinify\Service\MinifyCssService');
        $config = $realServiceLocator->get('Config');

        return new HeadLink($minifyJsService, $config['VcoZfMinify']);
    }
}
