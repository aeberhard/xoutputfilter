<?php

/**
 * frontend Plugin für XOutputFilter
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

/**
 * Output-Filter frontend
 */
function xoutputfilter_frontend($params)
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

  // Daten bereitstellen
  $table = $REX['TABLE_PREFIX'] . '420_xoutputfilter';
  $query = "SELECT * FROM $table WHERE (typ = 4) AND active = '1' ORDER BY name ASC ";
  $sql = new rex_sql;
  $sql->debugsql = 0;
  $sql->setQuery($query);
  if ($sql->getRows() == 0)
    return $params['subject'];

  // Ersetzungen abarbeiten
  $content = $params['subject'];
  for ($i = 0; $i < $sql->getRows(); $i ++)
  {
    if (($sql->getValue('allcats')==1) or xoutputfilter_check_cat($sql->getValue('categories'), $sql->getValue('subcats')))
    {
      $insertbefore = $sql->getValue('insertbefore');
      $once = ($sql->getValue('once') == '1') ? 1 : -1;
      $name = $sql->getValue('name');
      $replace = $sql->getValue('html');
      $marker = $sql->getValue('marker');
      $markers = explode('|', $marker);
      $exmodules = $sql->getValue('excludeids');
      if (!xoutputfilter_exclude_art($exmodules))
      {
        foreach ($markers as $key => $val)
        {
          $markers[$key] = trim($val);
        }

        // normale Ersetzung
        if (($insertbefore == '0') or ($insertbefore == '1') or ($insertbefore == '2'))
        {
          if (($REX['ADDON']['xoutputfilter']['runtimeinfo'] == '1') and ($replace<>''))
          {
            $replace = '<!-- [backend] start '.$name.' -->'.$sql->getValue('html').'<!-- [backend] end '.$name.' -->';
          }
          $phprc = xoutputfilter_eval_code($replace);
          //echo "<pre>";var_dump($phprc);echo "</pre>";
          if (!$phprc['error'])
          {
            $replace = $phprc['evaloutput'];
          }

          foreach ($markers as $search)
          {
            // Code nur einmal einfügen/ersetzen - dann mit preg_replace
            if ($once == 1)
            {
              $pattern1 = array('#', '[', ']', '?', '.', '^', '$', '*', '+', '|', '{', '}', '(', ')', '<', '>');
              $pattern2 = array('\#', '\[', '\]', '\?', '\.', '\^', '\$', '\*', '\+', '\|', '\{', '\}', '\(', '\)', '\<', '\>');
              $pattern = '#' . str_replace($pattern1, $pattern2, $search) . '#';
              if ($insertbefore == '0') // nach dem Marker einfügen
              {
                $content = preg_replace($pattern, $search . $replace, $content, 1);
              }
              if ($insertbefore == '1') // vor dem Marker einfügen
              {
                $content = preg_replace($pattern, $replace . $search, $content, 1);
              }
              if ($insertbefore == '2') // Marker ersetzen
              {
                $content = preg_replace($pattern, $replace, $content, 1);
              }
            }
            // Code mehrmals einfügen/ersetzen
            else
            {
              if ($insertbefore == '0') // nach dem Marker einfügen
              {
                $content = str_replace($search, $search . $replace, $content);
              }
              if ($insertbefore == '1') // vor dem Marker einfügen
              {
                $content = str_replace($search, $replace . $search, $content);
              }
              if ($insertbefore == '2') // Marker ersetzen
              {
                $content = str_replace($search, $replace, $content);
              }
            }
          }
        }

        // PREG_REPLACE
        if ($insertbefore == '3')
        {
          $search = trim(str_replace(array("\n", "\r"), '', $marker));
          $phprc = xoutputfilter_eval_code($replace);
          //echo "<pre>";var_dump($phprc);echo "</pre>";
          if (!$phprc['error'])
          {
            $replace = $phprc['evaloutput'];
          }
          $content = preg_replace($search, $replace, $content, $once);
        }

        // -------------------------
        // PHP-Code
        if ($insertbefore == '4')
        {
          foreach ($markers as $search)
          {
            if ((trim($search) <> '') and strstr($content, trim($search)))
            {
              $REX['xoutputfilter']['content'] = $content;
              $phprc = xoutputfilter_eval_code($replace);
              $content = $REX['xoutputfilter']['content'];
            }
          }
        }
      }
    }
    $sql->next();
  }

  // evtl. Laufzeit ausgeben
  $laufzeitinfo = '';
  if ($REX['ADDON']['xoutputfilter']['runtimeinfo'] == '1')
  {
    $time = explode(' ', microtime());
    $stoptimer = ($time[0] + $time[1]);
    $timer = intval(($stoptimer - $starttimer) * 10000) / 10000;
    $laufzeitinfo = '<!-- XOutputFilter '.$REX['ADDON']['version']['xoutputfilter'].' [frontend] - '.$timer.' seconds -->';
  }

  return $content . $laufzeitinfo;
}
