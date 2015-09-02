<?php
namespace ZfMinify/Service;

interface MinifyServiceInterface {

  /*
  * @param $content (String)
  * @return (string) Minified content
  */
  public function minify($content);
}
