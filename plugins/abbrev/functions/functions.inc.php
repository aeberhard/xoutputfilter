<?php

/**
 * abbrev Plugin für XOutputFilter
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

/**
 * Output-Filter abbrev
 */
function xoutputfilter_abbrev($params)
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
  if (!isset($REX['xoutputfilter']['replaces']['2'][$REX['CUR_CLANG']]))	
    return $params['subject'];

  $search = $replace = array();

  foreach ($REX['xoutputfilter']['replaces']['2'][$REX['CUR_CLANG']] as $key => $val)
  {
    if ($REX['xoutputfilter']['excludeids']['2'][$REX['CUR_CLANG']][$key] <> '')
    {
      $exc = explode(',', $REX['xoutputfilter']['excludeids']['2'][$REX['CUR_CLANG']][$key]);
      foreach ($exc as $xkey => $xval) {
        $exc[$xkey] = trim($xval);
      }
      if (in_array($REX['ARTICLE_ID'], $exc))
        $key = '';
    }

    if (trim($key)<>'' and trim($val)<>'')
    {
      $markers = explode('|', $key);
      foreach ($markers as $key2 => $val2)
      {
        $markers[$key2] = trim($val2);
      }
      foreach ($markers as $key2)
      {
        $pattern1 = array('#', '[', ']', '?', '.', '^', '$', '*', '+', '|', '{', '}', '(', ')', '<', '>');
        $pattern2 = array('\#', '\[', '\]', '\?', '\.', '\^', '\$', '\*', '\+', '\|', '\{', '\}', '\(', '\)', '\<', '\>');
        $pattern = str_replace($pattern1, $pattern2, $key2);
        $val = htmlspecialchars($val);
        $pattern1 = array('"', "\r", "\n", "\\");
        $pattern2 = array('&quot;', '', ' ', '');
        $val = str_replace($pattern1, $pattern2, $val);
        $search[] = "|(?!<[^<>]*?)(?<![?.&])" . $pattern . "(?![^<>]*?>)|msU";
        $replace[] = '<span class="abbr" title="'.$val.'"><abbr title="'.$val.'">'.$key2.'</abbr></span>';
      }
    }
  }

  //$content = preg_replace($search, $replace, $params['subject']);
  $content = $params['subject'];
  preg_match_all("=<body[^>]*>(.*)</body>=iUms", $params['subject'], $oo);
  if (isset($oo[1][0]))
  {
    $body = $oo[1][0];
    $bodynew = preg_replace($search, $replace, $body);
    $content = str_replace($body, $bodynew, $params['subject']);
  }

  // evtl. Laufzeit ausgeben
  $laufzeitinfo = '';
  if ($REX['ADDON']['xoutputfilter']['runtimeinfo'] == '1')
  {
    $time = explode(' ', microtime());
    $stoptimer = ($time[0] + $time[1]);
    $timer = intval(($stoptimer - $starttimer) * 10000) / 10000;
    $laufzeitinfo = '<!-- XOutputFilter '.$REX['ADDON']['version']['xoutputfilter'].' [abbrev] - '.$timer.' seconds -->';
  }

  return $content . $laufzeitinfo;
}
