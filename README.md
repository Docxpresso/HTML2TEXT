HTML to Plain Text Converter
============================
HTML2TEXT is a single class PHP package that converts HTML into plain text.

It uses DOM methods rather than regular expressions and although it works out of
the box it can be easily further customized to suit any particular need.

## Installing HTML2TEXT

The recommended way to install HTML2TEXT is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of Guzzle:

```bash
php composer.phar require docxpresso/html2text
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update HTML2TEXT using composer:

 ```bash
composer.phar update
 ```

