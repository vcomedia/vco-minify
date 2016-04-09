<?php
/**
 * VcoZfMinify - Zend Framework 2 HeadLink and headLink view helper wrappers to minify CSS & JS.
 *
 * @category Module
 * @package  VcoZfMinify
 * @author   Vahag Dudukgian (valeeum)
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link     http://github.com/vcomedia/vco-zf-minify/
 */

namespace VcoZfMinify\View\Helper;

use Zend\View\Helper\HeadLink as HeadLinkOriginal;
use VcoZfMinify\Service\MinifyServiceInterface;

/**
 * Class HeadLink
 *
 * @package VcoZfMinify\View\Helper
 */
class HeadLink extends HeadLinkOriginal {

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
        $this->minifyEnabled = $this->minifyConfig['minifyCSS']['enabled'];
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

          $itemSrcPath = !empty($item->href) ? $this->minifyDocRootPath . trim($item->href,'/\ ') : null;
          if($item->type === 'text/css'
              && $itemSrcPath
              && file_exists($itemSrcPath)
              && empty($item->conditionalStylesheet)
              && (!isset($item->attributes['minify']) || $item->attributes['minify'] !== false)
          ) {
            $filesToMinify[$item->media][] = $itemSrcPath;
            $lastModifiedTime = max(filemtime($itemSrcPath), $lastModifiedTime);
          } else {
            $items[] = $this->itemToString($item);
          }
      }

      if(count($filesToMinify, COUNT_RECURSIVE) > 0) {
        foreach($filesToMinify as $media => $filePaths) {
          $minifiedFileName = md5(implode('|', $filePaths)) . '.min.css';
          $minifiedFileBasePath = $this->view->basePath($this->minifyCacheDir . '/' . $minifiedFileName);
          $minifiedFilePath = $this->minifyDocRootPath . trim($minifiedFileBasePath, '\/ ');
          $lockFilePath = sys_get_temp_dir() . '/' . $minifiedFileName . '.lock';

          $isMinifiedFileBuildRequired = !file_exists($minifiedFilePath) || filemtime($minifiedFilePath) < $lastModifiedTime;
          $isMinifiedFileBuildLocked = file_exists($lockFilePath);

          if ($isMinifiedFileBuildRequired && !$isMinifiedFileBuildLocked){
              file_put_contents($lockFilePath, 'locked', LOCK_EX);
              try {
                $pieces = array();
                foreach ($filePaths as $filePath) {
                    $pieces[] = $this->minifyService->minify(file_get_contents($filePath), array('docRoot' => $this->minifyDocRootPath, 'currentDir' => dirname($filePath)));
                }
                $content = implode($this->getSeparator(), $pieces);
                file_put_contents($minifiedFilePath, $content, LOCK_EX);
              } catch(\Exception $e) {
                unlink($lockFilePath);
                throw new \Exception($e->getMessage());
              }
              unlink($lockFilePath);

              //clean out old files
              $flattened = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->minifyCachePath));
              $files = new \RegexIterator($flattened, '/^[a-f0-9]{32}\.min\.css$/i');
              foreach($files as $file) {
                if(filemtime($file) < time() - 86400 * 7) {
                  unlink($file);
                }
              }
          }

          $item = $this->createData(
            array(
              'type'=>'text/css',
              'rel' => 'stylesheet',
              'media' => $media,
              'href' => $this->view->mediaPath($minifiedFileBasePath, false),
              'conditionalStylesheet' => false
            )
          );
          array_unshift($items, $this->itemToString($item));
        }
      }

      return $indent . implode($this->escape($this->getSeparator()) . $indent, $items);
    }
    
    /**
     * Overload method access
     *
     * Items that may be added in the future:
     * - Navigation?  need to find docs on this
     *   - public function appendStart()
     *   - public function appendContents()
     *   - public function appendPrev()
     *   - public function appendNext()
     *   - public function appendIndex()
     *   - public function appendEnd()
     *   - public function appendGlossary()
     *   - public function appendAppendix()
     *   - public function appendHelp()
     *   - public function appendBookmark()
     * - Other?
     *   - public function appendCopyright()
     *   - public function appendChapter()
     *   - public function appendSection()
     *   - public function appendSubsection()
     *
     * @param  mixed $method
     * @param  mixed $args
     * @throws Exception\BadMethodCallException
     * @return void
     */
    public function __call($method, $args)
    {
        if (preg_match('/^(?P<action>set|(ap|pre)pend|offsetSet)(?P<type>Stylesheet|Alternate|Prev|Next)$/', $method, $matches)) {
            $argc   = count($args);
            $action = $matches['action'];
            $type   = $matches['type'];
            $index  = null;

            if ('offsetSet' == $action) {
                if (0 < $argc) {
                    $index = array_shift($args);
                    --$argc;
                }
            }

            if (1 > $argc) {
                throw new Exception\BadMethodCallException(
                    sprintf('%s requires at least one argument', $method)
                );
            }

            if (is_array($args[0])) {
                $item = $this->createData($args[0]);
            } else {
                $dataMethod = 'createData' . $type;
                $item       = $this->$dataMethod($args);
            }
            
            if(isset($item->href)) {
                $content = ($this->startsWith($item->href, '//') || $this->startsWith($item->href, 'http') || $this->startsWith($item->href, 'ftp')) ? $item->href : $this->view->mediapath($item->href);
            }

            if ($item) {
                if ('offsetSet' == $action) {
                    $this->offsetSet($index, $item);
                } else {
                    $this->$action($item);
                }
            }

            return $this;
        }

        return parent::__call($method, $args);
    }
    
    private function startsWith($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}
