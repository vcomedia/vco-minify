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
use Minify_Controller_Files;
use JSMin;

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
        $isMinifyEnabled = $config['ZfMinify']['minifyJS']['enabled'];
        $docRoot = trim($config['ZfMinify']['documentRoot'],'/\ ');
        $cachePath = trim($config['ZfMinify']['cachePath'],'/\ ');
        $absoluteDocRootPath = getcwd() . '/' . $docRoot . '/';

        if ($isMinifyEnabled === false) {
            return parent::toString($indent);
        }

        if (!is_writable($absoluteDocRootPath . $cachePath)) {
            throw new \Exception("Cache path not writable: $absoluteDocRootPath . $cachePath");
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

            if($item->type === 'text/javascript'
                && !empty($item->attributes)
                && !empty($item->attributes['src'])
                && file_exists($absoluteDocRootPath . trim($item->attributes['src'],'/\ '))
                && empty($item->attributes['conditional'])
                && (!isset($item->attributes['minify']) || $item->attributes['minify'] !== false)
            ) {
                $itemSrcsToMinify[] = $item->attributes['src'];
            } else {
                $items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
            }
        }

        //$itemSrcsToMinify = array_unique($itemSrcsToMinify);

        if(count($itemSrcsToMinify) > 0) {

          $controller = new Minify_Controller_Files();
          $options = $controller->setupSources(array('files' => $itemSrcsToMinify));
          $options = $controller->analyzeSources($options);
          $options = $controller->mixInDefaultOptions($options);

          $lastmodified = $options['lastModifiedTime'];
          $filePath = $this->generateFileName($itemSrcsToMinify, $cachePath);
          $absoluteFilePath = $absoluteDocRootPath . trim($filePath, '\/ ');
          $absoluteLockFilePath = $absoluteFilePath . '.lock';

          if ((!file_exists($absoluteFilePath) || filemtime($absoluteFilePath) < $lastmodified)
              && (!file_exists($absoluteLockFilePath) || time() > filemtime($absoluteLockFilePath) + 600)
          ){
                file_put_contents($absoluteLockFilePath, 'locked', LOCK_EX);
                $pieces = array();
                foreach ($controller->sources as $source) {
                    $pieces[] = $source->getContent();
                }
                $content = implode($this->getSeparator(), $pieces);
                $content = JsMin::minify($content);
                file_put_contents($absoluteFilePath, $content, LOCK_EX);
                unlink($absoluteLockFilePath);
          }

          $item = $this->createData('text/javascript', array('src' => $filePath . '?v=' . $lastmodified));
          array_unshift($items, $this->itemToString($item, $indent, $escapeStart, $escapeEnd));
        }

        return implode($this->getSeparator(), $items);
    }

    protected function generateFileName($files, $cachePath) {
        return $this->view->basePath($cachePath . md5(implode('|', $files)) . '.js');
    }
}
