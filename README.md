## ZfMinify - Zend Framework 2 headScript and headLink view helper wrappers to minify CSS & JS.
This module was inspired by [TpMinify](https://github.com/kkamkou/tp-minify) and uses the [Steve (mrclay) Clay's Minify](https://github.com/mrclay/minify) library for styles and scripts obfuscation.

## Installation
### Composer
 * Install the [Composer](http://getcomposer.org/doc/00-intro.md)
 * Add ```"vcomedia/zf-minify": "dev-master"``` to a ```composer.json``` file, to the ```require``` section
 * Execute ```composer update```
 * Add ```'modules' => array('ZfMinify', ...)``` to the ```application.config.php``` file of your project. *Important thing is to place the "ZfMinify" before any other modules.*
 * Open (just an example) the ```Frontend/config/module.config.php``` and add this config stub:

```php

'ZfMinify' => array(
	'minifyCSS' => array(
		'enabled' => true,
		'maxAge' => 86400
	),
	'minifyJS' => array(
		'enabled' => true,
		'maxAge' => 86400
	)
)
```
 * Put styles and scripts into the Head section:

```php

$this->headLink()->prependStylesheet($this->basePath('SOME/PATH.CSS'))
$this->headScript()->prependFile($this->basePath('SOME/PATH.JS'))
```
## Options
It's possible to disable minification on a per file basis
```php
$this->headScript()->setAllowArbitraryAttributes(true);
$this->headScript()->prependFile($this->basePath('SOME/PATH.JS'), 'text/javascript', array('minify' => false))
```

## License
The MIT License (MIT)

Copyright (c) 2013-2014 Kanstantsin Kamkou

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
