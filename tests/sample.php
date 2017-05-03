<?php
require __DIR__ . '/../vendor/autoload.php';

use Docxpresso\HTML2TEXT as Parser;

$html = '
<p>A simple paragraph followe by an ordered list:</p>
<ol>
    <li>One item
        <ol>
            <li>Subitem</li>
        </ol>
    </li>
    <li>Two
        <ol>
            <li>Another subitem</li>
            <li>Nested subitem
                <ol>
                    <li>Third level item</li>
                </ol>
            </li>
        </ol>
    </li>
    <li>Last item</li>
</ol>
<p>And now an unordered list:</p>
<ul>
    <li>First</li>
    <li>Second</li>
    <li>Third</li>
</ul>
<h2>A Title</h2>
<p>A link to <a href="http://google.es">Google</a>.</p>
<p>And a table to finish:</p>
<table>
    <tr>
        <td> Cell 1 1</td>
        <td> Cell 1 2</td>
    </tr>
    <tr>
        <td> Cell 2 1</td>
        <td> Cell 2 2</td>
    </tr>
</table>
';

$parser = new Parser\HTML2TEXT($html);
$parser->printText();

