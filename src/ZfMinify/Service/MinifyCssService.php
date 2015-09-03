<?php
namespace ZfMinify\Service;

use Minify_CSS;

class MinifyCssService implements MinifyServiceInterface {
  public function minify($content, $options = array()) {
    return Minify_CSS::minify($content, $optionss);
  }
}
