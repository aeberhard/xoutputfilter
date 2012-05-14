<?php

/**
 * languages Plugin für XOutputFilter
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

/**
 * Output-Filter Sprachersetzungen
 */
function xoutputfilter_languages($params)
{
global $REX;
  
  if ($REX['REDAXO'])
    return $params['subject'];
  if ($REX['ADDON']['xoutputfilter']['active'] <> '1')
    return $params['subject'];
  if (xoutputfilter_exclude_cat_art()) // Exlude Kategorie / Artikel-Id prüfen
    return $params['subject'];

  // Für Laufzeitermittlung
  $time = explode(' ', microtime());
  $starttimer = ($time[0] + $time[1]);

  // Ersetzen
  $x = new xoutputfilter();
  if (!isset($REX['xoutputfilter']['replaces']['1'][$REX['CUR_CLANG']]))	
    return $params['subject'];

  $content = $x->replace($params['subject'], $REX['CUR_CLANG'], 1);

  // evtl. Laufzeit ausgeben
  $laufzeitinfo = '';
  if ($REX['ADDON']['xoutputfilter']['runtimeinfo'] == '1')
  {
    $time = explode(' ', microtime());
    $stoptimer = ($time[0] + $time[1]);
    $timer = intval(($stoptimer - $starttimer) * 10000) / 10000;
    $laufzeitinfo = '<!-- XOutputFilter '.$REX['ADDON']['version']['xoutputfilter'].' [languages] - '.$timer.' seconds -->';
  }

  return $content . $laufzeitinfo;
}
