<?php
/**
 * ZfMinify - Zend Framework 2 HeadLink and headLink view helper wrappers to minify CSS & JS.
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
use Zend\View\Helper\HeadLink as HeadLinkOriginal;
use ZfMinify\Service\MinifyServiceInterface;

/**
 * Class HeadLink
 *
 * @package ZfMinify\View\Helper
 * @see ServiceLocatorAwareInterface
 */
class HeadLink extends HeadLinkOriginal implements
    ServiceLocatorAwareInterface {

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     *
     * @var MinifyServiceInterface
     */
    protected $minifyService;

    /**
     * Constructor
     *
     * @param
     */
    public function __construct(MinifyServiceInterface $minifyService)
    {
        $this->minifyService = $minifyService;
        parent::__construct();
    }

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
      $isMinifyEnabled = $config['ZfMinify']['minifyCSS']['enabled'];
      $docRoot = trim($config['ZfMinify']['documentRoot'],'/\ ');
      $cachePath = trim($config['ZfMinify']['cachePath'],'/\ ');
      $absoluteDocRootPath = getcwd() . '/' . $docRoot . '/';

      if ($isMinifyEnabled === false || !is_writable($absoluteDocRootPath . $cachePath)) {
          return parent::toString($indent);
      }

      //TODO: move below excecption outside of toString() method
      // if (!is_writable($absoluteDocRootPath . $cachePath)) {
      //     throw new \Exception("Cache path not writable $absoluteDocRootPath . $cachePath")
      // }

      $indent = (null !== $indent)
        ? $this->getWhitespace($indent)
        : $this->getIndent();

      $items = [];
      $this->getContainer()->ksort();
      foreach ($this as $item) {
          $items[] = $this->itemToString($item);
      }

      return $indent . implode($this->escape($this->getSeparator()) . $indent, $items);



        // $indent = (null !== $indent) ? $this->getWhitespace($indent) : $this->getIndent();
        //
        // if ($this->view) {
        //     $useCdata = $this->view->plugin('doctype')->isXhtml();
        // } else {
        //     $useCdata = $this->useCdata;
        // }
        //
        // $escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
        // $escapeEnd = ($useCdata) ? '//]]>' : '//-->';
        //
        // $itemsToNotMinify = array();
        // $filesToMinify = array();
        // $lastModifiedTime = 0;
        //
        // $items = [];
        // $this->getContainer()->ksort();
        // foreach ($this as $item) {
        //     if (! $this->isValid($item)) {
        //         continue;
        //     }
        //
        //     $itemSrcPath = (!empty($item->attributes) && !empty($item->attributes['src'])) ? $absoluteDocRootPath . trim($item->attributes['src'],'/\ ') : null;
        //
        //     if($item->type === 'text/javascript'
        //         && $itemSrcPath
        //         && file_exists($itemSrcPath)
        //         && empty($item->attributes['conditional'])
        //         && (!isset($item->attributes['minify']) || $item->attributes['minify'] !== false)
        //     ) {
        //         $filesToMinify[] = $itemSrcPath;
        //         $lastModifiedTime = max(filemtime($itemSrcPath), $lastModifiedTime);
        //     } else {
        //         $items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
        //     }
        // }
        //
        // if(count($filesToMinify) > 0) {
        //   $minifiedFileName = md5(implode('|', $filesToMinify)) . '.js';
        //   $minifiedFilePath = $this->view->basePath($cachePath . '/' . $minifiedFileName);
        //   $absoluteFilePath = $absoluteDocRootPath . trim($minifiedFilePath, '\/ ');
        //   $absoluteLockFilePath = sys_get_temp_dir() . '/' . $minifiedFileName . '.lock';
        //
        //   if ((!file_exists($absoluteFilePath) || filemtime($absoluteFilePath) < $lastModifiedTime)
        //       && (!file_exists($absoluteLockFilePath) || time() > filemtime($absoluteLockFilePath) + 600)   //ignore stray lock files
        //   ){
        //         file_put_contents($absoluteLockFilePath, 'locked', LOCK_EX);
        //         $pieces = array();
        //         foreach ($filesToMinify as $filePath) {
        //             $pieces[] = file_get_contents($filePath);
        //         }
        //         $content = implode($this->getSeparator(), $pieces);
        //         $content = $this->minifyService->minify($content);
        //         file_put_contents($absoluteFilePath, $content, LOCK_EX);
        //         unlink($absoluteLockFilePath);
        //   }
        //
        //   $item = $this->createData('text/javascript', array('src' => $minifiedFilePath . '?v=' . $lastModifiedTime));
        //   array_unshift($items, $this->itemToString($item, $indent, $escapeStart, $escapeEnd));
        // }
        //
        // return implode($this->getSeparator(), $items);
    }
}
