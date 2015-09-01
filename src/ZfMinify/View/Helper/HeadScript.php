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

namespace ZfMinify\View\Helper;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Helper\HeadScript as HeadScriptOriginal;
use Minify;

/**
 * Class HeadScript
 *
 * @package ZfMinify\View\Helper
 * @see ServiceLocatorAwareInterface
 */
class HeadScript extends HeadScriptOriginal implements 
    ServiceLocatorAwareInterface {

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Set serviceManager instance
     *
     * @param ServiceLocatorInterface $serviceLocator            
     * @return void
     */
    public function setServiceLocator (ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Retrieve serviceManager instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator () {
        return $this->serviceLocator;
    }
    
    // /**
    // * Create script HTML
    // *
    // * @param mixed $item Item to convert
    // * @param string $indent String to add before the item
    // * @param string $escapeStart Starting sequence
    // * @param string $escapeEnd Ending sequence
    // * @return string
    // */
    // public function itemToString($item, $indent, $escapeStart, $escapeEnd)
    // {
    // if (!empty($item->source)) {
    // $config = $this->getServiceLocator()->getServiceLocator()->get('Config');
    // $config = $config['ZfMinify']['helpers']['headScript'];
    // if ($config['enabled']) {
    // $result = Minify::serve(
    // 'Files',
    // array_merge(
    // $config,
    // array(
    // 'quiet' => true,
    // 'encodeOutput' => false,
    // 'files' => new \Minify_Source(
    // array(
    // 'contentType' => Minify::TYPE_JS,
    // 'content' => $item->source,
    // 'id' => __CLASS__ . hash('crc32', $item->source)
    // )
    // )
    // )
    // )
    // );
    
    // if ($result['success']) {
    // $item->source = $result['content'];
    // }
    // }
    // }
    
    // return parent::itemToString($item, $indent, $escapeStart, $escapeEnd);
    // }
    
    /**
     * Combine all files and retrieve minified file
     *
     * @param string|int $indent
     *            Amount of whitespaces or string to use for indention
     * @return string
     */
    public function toString ($indent = null) {
        $config = $this->getServiceLocator()
            ->getServiceLocator()
            ->get('Config');
        $isEnabled = $config['ZfMinify']['minifyJS']['enabled'];
        
        if ($isEnabled === false) {
            return parent::toString($indent);
        }
        
        // minification enabled - start minifying!
        $indent = (null !== $indent) ? $this->getWhitespace($indent) : $this->getIndent();
        
        if ($this->view) {
            $useCdata = $this->view->plugin('doctype')->isXhtml();
        } else {
            $useCdata = $this->useCdata;
        }
        
        $escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
        $escapeEnd = ($useCdata) ? '//]]>' : '//-->';
        
        $itemsToNotMinify = array();
        $itemSrcsToMinify = array();
        
        $items = [];
        $this->getContainer()->ksort();
        foreach ($this as $item) {
            if (! $this->isValid($item)) {
                continue;
            }
            
            if($item->type == 'text/javascript' &&	!empty($item->attributes) && !empty($item->attributes['src'] && 
                (!isset($item->attributes['minify']) || $item->attributes['minify'] !== false))) {
                $itemSrcsToMinify[] = $item->attributes['src'];
            } else {
                $itemsToNotMinify[] = $item;
            }
        }
        
        
        
        //$items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
        
        return implode($this->getSeparator(), $items);
    }
}
