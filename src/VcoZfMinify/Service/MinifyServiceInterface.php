<?php
namespace VcoZfMinify\Service;

interface MinifyServiceInterface {

  /*
  * @param $content (String)
  * @return (string) Minified content
  */
  public function minify($content, $options = array());
}
