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

namespace VcoZfMinify\View\Helper;

use Zend\View\Helper\HeadScript as HeadScriptOriginal;
use VcoZfMinify\Service\MinifyServiceInterface;

/**
 * Class HeadScript
 *
 * @package VcoZfMinify\View\Helper
 */
class HeadScript extends HeadScriptOriginal {

    /**
     *
     * @var arrray
     */
    protected $minifyConfig;

    /**
     *
     * @var bool
     */
    protected $minifyEnabled;

    /**
     *
     * @var string
     */
    protected $minifyDocRootDir;

    /**
     *
     * @var string
     */
    protected $minifyDocRootPath;

    /**
     *
     * @var string
     */
    protected $minifyCacheDir;

    /**
     *
     * @var string
     */
    protected $minifyCachePath;

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
    public function __construct(MinifyServiceInterface $minifyService, $config)
    {
        $this->minifyService = $minifyService;
        $this->minifyConfig = $config;
        $this->minifyEnabled = $this->minifyConfig['minifyJS']['enabled'];
        $this->minifyDocRootDir = trim($this->minifyConfig['docRootDir'],'/\ ');
        $this->minifyDocRootPath = getcwd() . '/' . $this->minifyDocRootDir . '/';
        $this->minifyCacheDir = trim($this->minifyConfig['cacheDir'],'/\ ');
        $this->minifyCachePath = $this->minifyDocRootPath . $this->minifyCacheDir;

        if(!file_exists($this->minifyCachePath) && mkdir($this->minifyCachePath, 0755, true) === false) {
          throw new \Exception("Cache dir does not exist and could not be created - '$this->minifyCachePath'");
        }

        if (!is_writable($this->minifyCachePath)) {
          throw new \Exception("Cache path not writable - '$this->minifyCachePath'");
        }

        parent::__construct();
    }

    /**
     * Combine all files and retrieve minified file
     *
     * @param string|int $indent
     *            Amount of whitespaces or string to use for indention
     * @return string
     */
    public function toString ($indent = null) {
        if ($this->minifyEnabled === false) {
            return parent::toString($indent);
        }

        $indent = (null !== $indent) ? $this->getWhitespace($indent) : $this->getIndent();

        if ($this->view) {
            $useCdata = $this->view->plugin('doctype')->isXhtml();
        } else {
            $useCdata = $this->useCdata;
        }

        $escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
        $escapeEnd = ($useCdata) ? '//]]>' : '//-->';

        $filesToMinify = array();
        $lastModifiedTime = 0;

        $items = [];
        $this->getContainer()->ksort();
        foreach ($this as $item) {
            if (! $this->isValid($item)) {
                continue;
            }

            $itemSrcPath = (!empty($item->attributes) && !empty($item->attributes['src'])) ? $this->minifyDocRootPath . trim($item->attributes['src'],'/\ ') : null;

            if($item->type === 'text/javascript'
                && $itemSrcPath
                && file_exists($itemSrcPath)
                && empty($item->attributes['conditional'])
                && (!isset($item->attributes['minify']) || $item->attributes['minify'] !== false)
            ) {
                $filesToMinify[] = $itemSrcPath;
                $lastModifiedTime = max(filemtime($itemSrcPath), $lastModifiedTime);
            } else {
                if(isset($item->attributes['minify'])) {
                    unset($item->attributes['minify']);
                }
                $items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
            }
        }

        if(count($filesToMinify) > 0) {
          $minifiedFileName = md5(implode('|', $filesToMinify)) . '.min.js';
          $minifiedFileBasePath = $this->view->basePath($this->minifyCacheDir . '/' . $minifiedFileName);
          $minifiedFilePath = $this->minifyDocRootPath . trim($minifiedFileBasePath, '\/ ');
          $lockFilePath = sys_get_temp_dir() . '/' . $minifiedFileName . '.lock';

          $isMinifiedFileBuildRequired = !file_exists($minifiedFilePath) || filemtime($minifiedFilePath) < $lastModifiedTime;
          $isMinifiedFileBuildLocked = file_exists($lockFilePath);

          if ($isMinifiedFileBuildRequired && !$isMinifiedFileBuildLocked){
                file_put_contents($lockFilePath, 'locked', LOCK_EX);
                try {
                  $pieces = array();
                  foreach ($filesToMinify as $filePath) {
                      $pieces[] = file_get_contents($filePath);
                  }
                  $content = implode($this->getSeparator(), $pieces);
                  $content = $this->minifyService->minify($content);
                  file_put_contents($minifiedFilePath, $content, LOCK_EX);
                } catch(\Exception $e) {
                  unlink($lockFilePath);
                  throw new \Exception($e->getMessage());
                }
                unlink($lockFilePath);

                //clean out old files
                $flattened = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->minifyCachePath));
                $files = new \RegexIterator($flattened, '/^[a-f0-9]{32}\.min\.js$/i');
                foreach($files as $file) {
                  if(filemtime($file) < time() - 86400 * 7) {
                    unlink($file);
                  }
                }
          }

          $item = $this->createData('text/javascript', array('src' => $minifiedFileBasePath));
          array_unshift($items, $this->itemToString($item, $indent, $escapeStart, $escapeEnd));
        }

        return implode($this->getSeparator(), $items);
    }
    
    /**
     * Create script HTML
     *
     * @param  mixed  $item        Item to convert
     * @param  string $indent      String to add before the item
     * @param  string $escapeStart Starting sequence
     * @param  string $escapeEnd   Ending sequence
     * @return string
     */
    public function itemToString($item, $indent, $escapeStart, $escapeEnd) {
        if(isset($item->src)) {            
            $item->src = ($this->startsWith($item->src, '//') || $this->startsWith($item->src, 'http') || $this->startsWith($item->src, 'ftp')) ? $item->src : $this->view->mediapath($item->src);
        }
        parent::itemToString($item, $indent, $escapeStart, $escapeEnd);
    }
    
    private function startsWith($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}
