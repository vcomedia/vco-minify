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

return array(
    'ZfMinify' => array(
        'documentRoot' => 'public/',  //path to public dir from app root
        'cachePath' => 'cache/',      //cache folder in documentRoot
        'minifyCSS' => array(
            'enabled' => false,
            'maxAge' => 86400
        ),
        'minifyJS' => array(
            'enabled' => false,
            'maxAge' => 86400
        )
    )
);
