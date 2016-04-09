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

/**
 * Class InlineScript
 *
 * @package VcoZfMinify\View\Helper
 */
class InlineScript extends HeadScript {
    
    /**
     * Registry key for placeholder
     *
     * @var string
     */
    protected $regKey = 'Zend_View_Helper_InlineScript';    
    
    /**
     * Return InlineScript object
     *
     * Returns InlineScript helper object; optionally, allows specifying a
     * script or script file to include.
     *
     * @param  string $mode      Script or file
     * @param  string $spec      Script/url
     * @param  string $placement Append, prepend, or set
     * @param  array  $attrs     Array of script attributes
     * @param  string $type      Script type and/or array of script attributes
     * @return InlineScript
     */
    public function __invoke(
        $mode = self::FILE,
        $spec = null,
        $placement = 'APPEND',
        array $attrs = [],
        $type = 'text/javascript'
    ) {
        return parent::__invoke($mode, $spec, $placement, $attrs, $type);
    }
}
