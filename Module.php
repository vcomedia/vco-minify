<?php
/**
 * VcoZfMinify - Zend Framework 2 headScript and headLink view helper wrappers to minify CSS & JS.
 *
 * @category Module
 * @package  VcoZfMinify
 * @author   Vahag Dudukgian (valeeum)
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link     http://github.com/vcomedia/vco-zf-minify/
 */

namespace VcoZfMinify;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

/**
 * Class Module
 *
 * @see ConfigProviderInterface
 * @see ViewHelperProviderInterface
 * @package VcoZfMinify
 */

class Module implements ConfigProviderInterface, ViewHelperProviderInterface, ServiceProviderInterface {

    /**
     * @return array
     */
    public function getConfig () {
        return require __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig () {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    /** @return array */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'headscript' => 'VcoZfMinify\Factory\HeadScriptFactory',
                'inlinescript' => 'VcoZfMinify\Factory\InlineScriptFactory',
                'headlink' => 'VcoZfMinify\Factory\HeadLinkFactory'
            )
        );
    }

    /** @return array */
    public function getServiceConfig() {
        return array(
            'invokables' => array(
                'VcoZfMinify\Service\MinifyJsService' => 'VcoZfMinify\Service\MinifyJsService',
                'VcoZfMinify\Service\MinifyCssService' => 'VcoZfMinify\Service\MinifyCssService'
            )
        );
    }
}
