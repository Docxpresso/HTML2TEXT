HTML to Plain Text Converter
============================
HTML2TEXT is a single class PHP package that converts HTML into plain text.

It uses DOM methods rather than regular expressions and although it works out of
the box it can be easily further customized to suit any particular need.

You can visit the official page in [Docxpresso](http://www.docxpresso.com/documentation-api/html2text).

## Installing HTML2TEXT

The recommended way to install HTML2TEXT is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of HTML2TEXT:

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

## Using HTML2TEXT

The use of HTML2TEXT is extremely simple:

```php
require __DIR__ . '/../vendor/autoload.php';
use Docxpresso\HTML2TEXT as Parser;
$html = '<p>A simple paragraph.</p>';
$parser = new Parser\HTML2TEXT($html);
echo $parser->plainText();
```

You can override some of the default values by including an **options** array
whenever you invoke the HTML2TEXT class. The following options are available:
- **bold**: a string of chars that will wrap text in __b__ or __strong__ tags.
The default value is an empty string.
- **cellSeparator**: a string of chars used to separate content between
contiguous cells in a row. Default value is " || " (\t may be also
a sensible choice)
- **images**: if set to true the alt value associated to the image will
be printed like [img: alt value]. Default value is true.
- **italics**: a string of chars that will wrap text in __i__ or __em__ tags. The
default value is an empty string.
- **newLine**: if set it will replace the default value (\n\r) for titles
and paragraphs.
- **tab**: a string of chars that will be used like a "tab". The default
value is "   " (\t may be another standard option)
- **titles**: it can be "underline" (default), "uppercase" or "none".
