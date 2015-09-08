<?php
namespace VcoZfMinify\Factory;

use VcoZfMinify\View\Helper\HeadScript;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HeadScriptFactory implements FactoryInterface {

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator) {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $minifyJsService = $realServiceLocator->get('VcoZfMinify\Service\MinifyJsService');
        $config = $realServiceLocator->get('Config');

        return new HeadScript($minifyJsService, $config['VcoZfMinify']);
    }
}
