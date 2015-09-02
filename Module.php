<?php
/**
 * ZfMinify - Zend Framework 2 headScript and headLink view helper wrappers to minify CSS & JS.
 *
 * @category Module
 * @package  ZfMinify
 * @author   Vahag Dudukgian (valeeum)
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link     http://github.com/vcomedia/zf-minify/
 */

namespace ZfMinify;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

/**
 * Class Module
 *
 * @see ConfigProviderInterface
 * @see ViewHelperProviderInterface
 * @package ZfMinify
 */
class Module implements ConfigProviderInterface, ViewHelperProviderInterface {

    /**
     * @return array
     */
    public function getConfig () {
        return require __DIR__ . '/config/module.config.php';
    }

    //TODO: remove following method and autoload_classmap.php file
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

    // /** @return array */
    // public function getViewHelperConfig()
    // {
    //     return array(
    //         'invokables' => array(
    //             'headscript' => 'ZfMinify\View\Helper\HeadScript'
    //             // 'headstyle' => 'ZfMinify\View\Helper\HeadStyle'
    //         )
    //     );
    // }
}
