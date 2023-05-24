CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Compiling CSS


INTRODUCTION
------------

Farm Gin is a subtheme of the Gin Admin theme.


Compiling CSS
---------------
Site-wide CSS files are named with a leading underscore, for example _offcanvas.scss and are compiled and combined into a single styles.css file.

CSS loaded as libraries are named without a leading underscore, e.g. tabs.scss

- Setup Farm Gin locally that you can compile CSS & JS files.

* `npm install`

- Compile assets and watch (during development)

* `npx mix watch`

- Compile & minify assets (for production releases)

* `npx mix --production`