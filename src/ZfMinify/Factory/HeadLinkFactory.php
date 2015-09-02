<?php
namespace ZfMinify\Factory;

use ZfMinify\View\Helper\HeadLink;
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
        $minifyJsService = $realServiceLocator->get('ZfMinify\Service\MinifyCssService');

        return new HeadLink($minifyJsService);
    }
}
