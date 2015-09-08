<?php
namespace VcoZfMinify\Service;

use JSMin;

class MinifyJsService implements MinifyServiceInterface {
  public function minify($content, $options = array()) {
    return JSMin::minify($content);
  }
}
