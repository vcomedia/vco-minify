<?php
namespace ZfMinify/Service;

use JSMin;

class MinifyJsService implements MinifyServiceInterface {
  public function minify($content) {
    return JSMin::minify($content);
  }
}
