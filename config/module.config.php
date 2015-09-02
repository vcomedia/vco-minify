<?php
namespace ZfMinify;

/**
 * ZfMinify - Zend Framework 2 headScript and headLink view helper wrappers to minify CSS & JS.
 *
 * @category Module
 * @package  ZfMinify
 * @author   Vahag Dudukgian (valeeum)
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link     http://github.com/vcomedia/zf-minify/
 */

return array(
    'ZfMinify' => array(
        'docRootDir' => 'public/',  //path to docRoot relative to app root - (preceeding and trailing slashes ignored)
        'cacheDir' => 'cache/',      //cache folder in documentRoot - (preceeding and trailing slashes ignored)
        'minifyCSS' => array(
            'enabled' => false
        ),
        'minifyJS' => array(
            'enabled' => false
        )
    )
);
