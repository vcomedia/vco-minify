<?php
namespace VcoZfMinify\Service;

use Minify_HTML;

class MinifyHtmlService implements MinifyServiceInterface {
  public function minify($content, $options = array()) {
    return Minify_HTML::minify($content, $options);
  }
}
