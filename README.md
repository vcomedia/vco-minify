## ZfMinify - Zend Framework 2 headScript and headLink view helper wrappers to minify CSS & JS.
This module extends the default headScript and headLink view helpers providing a simple means of adding CSS/JS minification capabilities to your Zend Framework 2 based applications.  Currently, [Steve (mrclay) Clay's Minify](https://github.com/mrclay/minify) library is used for all minification.  However, the minification service factory can be overriden to offer alternative minification services (i.e., Google's Closure Compiler) which we plan on including as an option shortly.

## Installation
### Composer
 * Install [Composer](http://getcomposer.org/doc/00-intro.md)
 * Install the module using Composer into your application's vendor directory. Add the following line to your `composer.json`.

 ```json
 {
    "require": {
        "vcomedia/zf-minify": "dev-master"
    }
 }
```
 * Execute ```composer update```
 * Enable the module in your ZF2 `application.config.php` file.

 ```php
 return array(
     'modules' => array(
         'ZfMinify'
     )
 );
 ```
 * Copy and paste the `zf-minify/config/module.zf-minify.local.php.dist` file to your `config/autoload` folder and customize it with your configuration settings. Make sure to remove `.dist` from your file. Your `module.zf-minify.local.php` might look something like the following:

  ```php
 <?php

 return array(
     'ZfMinify' => array(
         'docRootDir' => 'public/',  //path to docRoot relative to app root - (preceeding and trailing slashes ignored)
         'cacheDir' => 'cache/',      //cache folder in documentRoot - (preceeding and trailing slashes ignored)
         'minifyCSS' => array(
             'enabled' => true
         ),
         'minifyJS' => array(
             'enabled' => true
         )
     )
 );
  ```

 * Add styles and scripts to your layout file (can also be added through controller):

```php

echo $this->headLink()
    ->prependStylesheet($this->basePath('some/cssfile1.css'), 'all')
    ->prependStylesheet($this->basePath('some/cssfile2.css'), 'all')
    ->prependStylesheet($this->basePath('some/cssfile3.css'), 'all');

echo $this->headScript()
    ->prependFile($this->basePath('some/jsfile1.js'))
    ->prependFile($this->basePath('some/jsfile2.js'))
    ->prependFile($this->basePath('some/jsfile3.js'));
```

If minification is enabled, the output will look similar to:

```html

<link href="/cache/2f262b5f19b4ea014c71b946e40a59d5.css?v=1441224700" media="all" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/cache/73df60b254e54f657ac5d9e7bf7bed4d.js?v=1440633356"></script>  

```

## Options
It's possible to disable minification on a per file basis
```php
$this->headScript()->setAllowArbitraryAttributes(true)
    ->prependFile($this->basePath('some/path.js'), 'text/javascript', array('minify' => false));
```
## Notes
 * Any file that has a conditional will not be minified.

 ```php

 $this->headScript()->appendFile($this->basePath('js/ie6.js'), 'text/javascript', array('conditional' => 'IE6',));
 $this->headLink()->appendStylesheet('/css/ie6.css', 'screen', 'IE6');
 ```
  * Each css media type (i.e., screen, print, all, ...) will generate a separate minified css file.

## License
The MIT License (MIT)

Copyright (c) 2015 Vahag Dudukgian

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
