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
      $docRootDir = trim($config['ZfMinify']['docRootDir'],'/\ ');
      $docRootPath = getcwd() . '/' . $docRootDir . '/';
      $cacheDir = trim($config['ZfMinify']['cacheDir'],'/\ ');
      $cachePath = $docRootPath . $cacheDir;

      if ($isMinifyEnabled === false || !is_writable($cachePath)) {
          return parent::toString($indent);
      }

      //TODO: move below excecption outside of toString() method
      // if(!file_exists($cachePath)) {
      //   mkdir($cachePath, 0755);
      // }
      // if (!is_writable($cachePath)) {
      //     throw new \Exception("Cache path not writable '$cachePath'")
      // }


      $indent = (null !== $indent)
        ? $this->getWhitespace($indent)
        : $this->getIndent();

      $filesToMinify = array();
      $lastModifiedTime = 0;

      $items = [];
      $this->getContainer()->ksort();
      foreach ($this as $item) {
          if (!$this->isValid($item)) {
              continue;
          }

          $itemSrcPath = !empty($item->href) ? $docRootPath . trim($item->href,'/\ ') : null;
          if($item->type === 'text/css'
              && $itemSrcPath
              && file_exists($itemSrcPath)
              && empty($item->conditionalStylesheet)
              && (!isset($item->attributes['minify']) || $item->attributes['minify'] !== false)
          ) {
            $filesToMinify[$item->media][] = $itemSrcPath;
          } else {
            $items[] = $this->itemToString($item);
          }
      }

      if(count($filesToMinify, COUNT_RECURSIVE) > 0) {
        foreach($filesToMinify as $media => $filePaths) {
          $minifiedFileName = md5(implode('|', $filePaths) . $media) . '.css';
          $minifiedFileBasePath = $this->view->basePath($cacheDir . '/' . $minifiedFileName);
          $minifiedFilePath = $docRootPath . trim($minifiedFileBasePath, '\/ ');
          $lockFilePath = sys_get_temp_dir() . '/' . $minifiedFileName . '.lock';

          if ((!file_exists($minifiedFilePath) || filemtime($minifiedFilePath) < $lastModifiedTime)
              && (!file_exists($lockFilePath) || time() > filemtime($lockFilePath) + 600)   //ignore stray lock files
          ){
                file_put_contents($lockFilePath, 'locked', LOCK_EX);
                $pieces = array();
                foreach ($filePaths as $filePath) {
                    $pieces[] = $this->minifyService->minify(file_get_contents($filePath), array('docRoot' => $docRootPath, 'currentDir' => dirname($filePath)));
                }
                $content = implode($this->getSeparator(), $pieces);
                file_put_contents($minifiedFilePath, $content, LOCK_EX);
                unlink($lockFilePath);
          }

          $item = $this->createData(
            array(
              'type'=>'text/css',
              'rel' => 'stylesheet',
              'media' => $media,
              'href' => $minifiedFileBasePath . '?v=' . $lastModifiedTime,
              'conditionalStylesheet' => false
            )
          );
          array_unshift($items, $this->itemToString($item));
        }
      }

      return $indent . implode($this->escape($this->getSeparator()) . $indent, $items);
    }
}
