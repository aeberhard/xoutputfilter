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

/**
 * Klasse für Zugriff auf Sprachersetzungen aus Modulen/Addons
 */

/*abstract*/ class xoutputfilter
{

function xoutputfilter()
{
  global $REX;

  if (!isset($REX['xoutputfilter']['replaces']))
  {
    $REX['xoutputfilter']['replaces'] = array();

    $table = $REX['TABLE_PREFIX'] . '420_xoutputfilter';

    $query = "SELECT typ, lang, marker, html, excludeids FROM $table WHERE (typ = 1 OR typ = 2 OR typ = 3) AND active = '1' ORDER BY typ ASC, lang ASC, marker ASC ";
    $sql = new rex_sql;
    $sql->debugsql = 0;
    $sql->setQuery($query);

    for ($i = 0; $i < $sql->getRows(); $i ++)
    {
      $REX['xoutputfilter']['replaces'][$sql->getValue('typ')][$sql->getValue('lang')][$sql->getValue('marker')] = $sql->getValue('html');
      $REX['xoutputfilter']['excludeids'][$sql->getValue('typ')][$sql->getValue('lang')][$sql->getValue('marker')] = $sql->getValue('excludeids');
      $sql->next();
    }
  }
}

function get($_marker, $_clang = 0, $_typ = 1)
{
  global $REX;

  if (isset($REX['xoutputfilter']['replaces'][$_typ][$_clang][$_marker]))
  {
    return $REX['xoutputfilter']['replaces'][$_typ][$_clang][$_marker];
  }
  return false;
}

function replace($_content, $_clang = 0, $_typ = 1)
{
  global $REX;

  $search = $replace = array();

  if (isset($REX['xoutputfilter']['replaces'][$_typ][$_clang]))
  {
    foreach ($REX['xoutputfilter']['replaces'][$_typ][$_clang] as $key => $val)
    {
      if ($REX['xoutputfilter']['excludeids'][$_typ][$_clang][$key] <> '')
      {
        $exc = explode(',', $REX['xoutputfilter']['excludeids'][$_typ][$_clang][$key]);
        foreach ($exc as $xkey => $xval) {
          $exc[$xkey] = trim($xval);
        }
        if (in_array($REX['ARTICLE_ID'], $exc))
          $key = '';
      }
      if (trim($key)<>'' and $val<>'')
      {
        $markers = explode('|', $key);
        foreach ($markers as $key2 => $val2)
        {
          $markers[$key2] = trim($val2);
        }
        foreach ($markers as $key2)
        {
          $search[] = $key2;
          $replace[] = $val;
        }
      }
    }
  }
  return str_replace($search, $replace, $_content);
}

} // End class xoutputfilter
