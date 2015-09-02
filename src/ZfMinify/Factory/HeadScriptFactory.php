<?php
namespace ZFMinify\Factory;

use ZFMinify\View\Helper\HeadScript;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CatalogControllerFactory implements FactoryInterface {

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator) {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $minifyJsService = $realServiceLocator->get('ZFMinify\Service\MinifyServiceInterface');

        return new HeadScript($minifyJsService);
    }
}
