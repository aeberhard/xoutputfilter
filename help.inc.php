<?php

/**
 * XOutputFilter Addon
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

  ob_start();
?>
<strong>XOutputFilter <span style="font-size:10px;">(Version 2.0)</span></strong>
<p>
Mit dem Addon XOutputFilter hat man die Möglichkeit über den Extension-Point OUTPUT_FILTER die Ausgabe der REDAXO-Seite zu beeinflussen (Frontend und Backend).

Die Hauptaufgabe dieses Addons ist die Ersetzung von Markern/Konstanten in der jeweiligen Sprache und die Kennzeichnung von Abkürzungen und Akronymen.

Über eine Programmschnittstelle kann in Modulen und Addons auf die Sprachersetzungen zugegriffen werden.

Zusätzlich können für das Frontend und das Backend verschiedene "Inserts" mit Code-Fragmenten, sonstigem HTML-Code oder auch PHP-Code angelegt werden. Diese Einträge können dann bestimmten Markern und Kategorien/Unterkategorien zugeordnet werden. Der Code wird - je nach Auswahl - entweder vor, hinter oder statt dem vorhandenen Marker im Quelltext ausgegeben / ausgeführt.

</p>
<div style="background-color:#cbcbcb;height:1px;" /></div>
<p>
<strong>Verwendung der Sprachersetzungen in Modulen oder Addons:</strong>

$x = new xoutputfilter();
$wert = $x->get(MARKER, [Sprache]);

Beispiele:
echo $x->get('%%copyright%%');
echo $x->get('%%copyright%%', 0);
echo $x->get('%%copyright%%', $REX['CUR_CLANG']);

Sprachersetzungen auf eigenen HTML-Code anwenden:
$x = new xoutputfilter();
echo $x->replace($my_content, $REX['CUR_CLANG']);

</p>

<?php
  $out = ob_get_contents();
  ob_end_clean();
  echo nl2br($out);
