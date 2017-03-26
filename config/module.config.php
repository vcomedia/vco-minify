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

return array(
    'VcoZfMinify' => array(
        'enableMediaPathModule' => false,
        'docRootDir' => 'public/',  //path to docRoot relative to app root - (preceeding and trailing slashes ignored)
        'cacheDir' => 'cache/',      //cache folder in documentRoot - (preceeding and trailing slashes ignored)
        'version' => '0.0.1', //version of files to force update cached files
        'minifyCSS' => array(
            'enabled' => false
        ),
        'minifyJS' => array(
            'enabled' => false,
            'async' => false
        ),
        'minifyHTML' => array(
            'enabled' => false,
        )
    )
);
