<?php
var_dump(microtime(true));
require_once 'HTML2TEXT.php';

use Docxpresso\HTML2TEXT as Parser;
$html = '<p>hola una ñapa gorda y otra &ntilde;apa más</p><ol><li>Uno&nbsp;&nbsp;MAS<ol><li>kkkk</li></ol></li><li>Dos<ol><li>kkkk</li><li>kkkk<ol><li>Otra</li></ol></li></ol></li><li>Tres</li></ol><h2>A Description List</h2>
<dl>
  <dt>Coffee</dt>
  <dd>- black hot drink</dd>
  <dt>Milk</dt>
  <dd>- white cold drink</dd>
</dl>
<p>un enlace <a href="http://google.es">Google</a>.
<p>hola <img src="" alt="sample text" /></p>';
//$html = file_get_contents('Plan_de_negocio.html');
for ($k = 0; $k<1; $k++) {
$parser = new Parser\HTML2TEXT($html);
$parser->printText();
}
var_dump(microtime(true));