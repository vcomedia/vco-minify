<?php
namespace VcoZfMinify\Factory;

use VcoZfMinify\View\Helper\InlineScript;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class InlineScriptFactory implements FactoryInterface {

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

        return new InlineScript($minifyJsService, $config['VcoZfMinify']);
    }
}
