<?php

/**
 * import_export Plugin für XOutputFilter
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */


/**
 * Returns true if $string ends with $end
 *
 * @param $string String Searchstring
 * @param $start String Suffix to search for
 * @author Markus Staab <staab@public-4u.de>
 */
function xoutputfilter_endsWith($string, $end)
{
   return (substr($string, strlen($string) - strlen($end)) == $end);
}

/**
 * Returns the content of the given folder
 *
 * @param $dir Path to the folder
 * @return Array Content of the folder or false on error
 * @author Markus Staab <staab@public-4u.de>
 */
function xoutputfilter_readFolder($dir)
{
  if (!is_dir($dir))
  {
    trigger_error('Folder "'.$dir.'" is not available or not a directory');
    return false;
  }
  $hdl = opendir($dir);
  $folder = array ();
  while (false !== ($file = readdir($hdl)))
  {
    $folder[] = $file;
  }
  return $folder;
}

/**
 * Returns the content of the given folder.
 * The content will be filtered with the given $fileprefix
 *
 * @param $dir Path to the folder
 * @param $fileprefix Fileprefix to filter
 * @return Array Filtered-content of the folder or false on error
 * @author Markus Staab <staab@public-4u.de>
 */
function xoutputfilter_readFilteredFolder($dir, $fileprefix)
{
  $filtered = array ();
  $folder = xoutputfilter_readFolder($dir);
  if (!$folder)
  {
    return false;
  }

  foreach ($folder as $file)
  {
    if (xoutputfilter_endsWith($file, $fileprefix))
    {
      $filtered[] = $file;
    }
  }
  return $filtered;
}

/**
 * Compare Files
 */
function xoutputfilter_compareFiles($file_a, $file_b)
{
  $dir = xoutputfilter_getImportDir();

  $time_a = filemtime( $dir .'/'. $file_a);
  $time_b = filemtime( $dir .'/'. $file_b);

  if( $time_a == $time_b) {
    return 0;
  }

  return ( $time_a > $time_b) ? -1 : 1;
}

/**
 * Returns Import Directory
 */
function xoutputfilter_getImportDir()
{
  global $REX;

  return $REX['INCLUDE_PATH'].'/addons/xoutputfilter/plugins/import_export/backup';
}

/**
 * Reads Import Directory
 */
function xoutputfilter_readImportFolder($fileprefix)
{
  $folder = '';
  usort($folder = xoutputfilter_readFilteredFolder(xoutputfilter_getImportDir(), $fileprefix), 'xoutputfilter_compareFiles');
  return $folder;
}

/**
 * Export CSV
 */
function xoutputfilter_export_csv($ex)
{
global $REX;
global $I18N;

  $output = '';
  $fields = array();

  // Query zusammenbasteln und Daten auswählen
  $table = $REX['TABLE_PREFIX'] . '420_xoutputfilter';
  $where = ' WHERE typ = 0 ';
  if ($ex['abbrev']=='1')
    $where .= ' OR typ = 2 ';
  if ($ex['acronym']=='1')
    $where .= ' OR typ = 3 ';
  if ($ex['backend']=='1')
    $where .= ' OR typ = 5 ';
  if ($ex['frontend']=='1')
    $where .= ' OR typ = 4 ';
  if ($ex['languages']=='1')
    $where .= ' OR typ = 1 ';
  $query = 'SELECT * FROM ' . $table . $where . ' ORDER BY typ ASC, lang ASC, name ASC, marker ASC ';

  //$sql = rex_sql::factory();
  $sql = new rex_sql();
  $sql->setQuery($query);

  // Daten für Ausgabe aufbereiten
  foreach($sql->getArray() as $d)
  {
    if($output == '')
    {
      foreach($d as $a => $b)
      {
        $fields[] = '"'.$a.'"';
      }
      $output = implode(';', $fields);
	  }

    foreach($d as $a => $b)
    {
      $srch = array('"', "\n", "\r", "\t", ';');
      $repl = array("\~q~", "\~n~", "\~r~", "\~t~", "\~d~");
      $d[$a] = '"' . str_replace($srch, $repl, $b) . '"';
    }
    $output .= "\n" . implode(';', $d);
  }

  // download - save as
  if ($ex['exportdl'] == 'download')
  {
    ob_end_clean();

    $ex['filename']= preg_replace('@[^\.a-z0-9_\-]@', '', $ex['filename']);

    $filesize = strlen($output);
    $filetype = "application/octetstream";
    $expires = "Mon, 01 Jan 2000 01:01:01 GMT";
    $last_modified = "Mon, 01 Jan 2000 01:01:01 GMT";

    header("Expires: ".$expires); // Date in the past
    header("Last-Modified: " . $last_modified); // always modified
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Pragma: private');
    header('Cache-control: private, must-revalidate');
    header('Content-Type: '.$filetype.'; name="'.$ex['filename'].$ex['exporttype'].'"');
    header('Content-Disposition: attachment; filename="'.$ex['filename'].$ex['exporttype'].'"');
    header('Content-Description: "'.$ex['filename'].$ex['exporttype'].'"');
    header('Content-Length: '.$filesize);
    echo $output;
    exit;
  }

  // auf dem Server speichern
  if ($ex['exportdl'] == 'server')
  {
    $ex['filename']= preg_replace('@[^\.a-z0-9_\-]@', '', $ex['filename']);
    $filename = xoutputfilter_getImportDir() . '/' . $ex['filename'];

    if (file_exists(xoutputfilter_getImportDir() . '/' . $ex['filename'] . $ex['exporttype']))
    {
      $i = 1;
      while (file_exists(xoutputfilter_getImportDir() . '/' . $ex['filename'] . '_'.$i . $ex['exporttype'])) $i++;
      $ex['filename'] = $ex['filename'] . '_'.$i;
    }
    rex_put_file_contents(xoutputfilter_getImportDir() . '/' . $ex['filename'] . $ex['exporttype'], $output);

    $msg = $I18N->Msg('xoutputfilter_export_csv', $ex['filename'] . $ex['exporttype']);
    echo rex_info($msg);
  }
}

/**
 * Export SQL
 */
function xoutputfilter_export_sql($ex)
{
  global $REX;
  global $I18N;

  $nl = "\n";
  $output = '';
  $fields = array();

  $table = $REX['TABLE_PREFIX'] . '420_xoutputfilter';

  // Header für SQL-Export
  $output = '## Redaxo Database Dump Version '.$REX['VERSION'].$nl;
  $output .= '## Prefix '.$REX['TABLE_PREFIX'].$nl;
  $output .= '## charset '.$I18N->msg('htmlcharset').$nl.$nl;

  $create = rex_sql::showCreateTable($table);
  $output .= "DROP TABLE IF EXISTS `".$table."`;".$nl;
  $output .= $create.";".$nl;

  $output .= $nl."LOCK TABLES `$table` WRITE;";
  $output .= $nl."/*!40000 ALTER TABLE `$table` DISABLE KEYS */;";
  $output .= $nl;

  // Tabellenfelder ermitteln
  //$sql = rex_sql::factory();
  $sql = new rex_sql();
  $fields = $sql->getArray("SHOW FIELDS FROM `$table`");

  foreach ($fields as $field)
  {
    if (preg_match('#^(bigint|int|smallint|mediumint|tinyint|timestamp)#i', $field['Type']))
    {
      $field = 'int';
    }
    elseif (preg_match('#^(float|double|decimal)#', $field['Type']))
    {
      $field = 'double';
    }
    elseif (preg_match('#^(char|varchar|text|longtext|mediumtext|tinytext)#', $field['Type']))
    {
      $field = 'string';
    }
    // else ?
  }

  // Query zusammenbasteln und Daten auswählen
  $where = ' ';
  $query = 'SELECT * FROM ' . $table . $where . ' ORDER BY typ ASC, lang ASC, name ASC, marker ASC ';
  $sql->freeResult();
  $sql->setQuery($query);

  // SQL für insert aufbauen
  while($sql->hasNext())
  {
    $record = array();

    foreach ($fields as $idx => $type)
    {
      $column = $sql->getValue($idx);

      switch ($type)
      {
        case 'int':
          $record[] = intval($column);
          break;
        case 'double':
          $record[] = sprintf('%.10F', (double) $column);
          break;
        case 'string':
        default:
          $record[] = $sql->escape($column, "'");
          break;
      }
    }

    $values[] = $nl .'  ('.implode(',', $record).')';
    $sql->next();
  }

  if (!empty($values))
  {
    $values = implode(',', $values);
    $output .= $nl."INSERT INTO `$table` VALUES $values;";
    unset($values);
  }

  // Footer für SQL-Export
  $output .= $nl;
  $output .= $nl."/*!40000 ALTER TABLE `$table` ENABLE KEYS */;";
  $output .= $nl."UNLOCK TABLES;".$nl.$nl;

  // download - save as
  if ($ex['exportdl'] == 'download')
  {
    ob_end_clean();

    $ex['filename']= preg_replace('@[^\.a-z0-9_\-]@', '', $ex['filename']);

    $filesize = strlen($output);
    $filetype = "application/octetstream";
    $expires = "Mon, 01 Jan 2000 01:01:01 GMT";
    $last_modified = "Mon, 01 Jan 2000 01:01:01 GMT";

    header("Expires: ".$expires); // Date in the past
    header("Last-Modified: " . $last_modified); // always modified
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Pragma: private');
    header('Cache-control: private, must-revalidate');
    header('Content-Type: '.$filetype.'; name="'.$ex['filename'].$ex['exporttype'].'"');
    header('Content-Disposition: attachment; filename="'.$ex['filename'].$ex['exporttype'].'"');
    header('Content-Description: "'.$ex['filename'].$ex['exporttype'].'"');
    header('Content-Length: '.$filesize);
    echo $output;
    exit;
  }

  // auf dem Server speichern
  if ($ex['exportdl'] == 'server')
  {
    $ex['filename']= preg_replace('@[^\.a-z0-9_\-]@', '', $ex['filename']);
    $filename = xoutputfilter_getImportDir() . '/' . $ex['filename'];

    if (file_exists(xoutputfilter_getImportDir() . '/' . $ex['filename'] . $ex['exporttype']))
    {
      $i = 1;
      while (file_exists(xoutputfilter_getImportDir() . '/' . $ex['filename'] . '_'.$i . $ex['exporttype'])) $i++;
      $ex['filename'] = $ex['filename'] . '_'.$i;
    }
    rex_put_file_contents(xoutputfilter_getImportDir() . '/' . $ex['filename'] . $ex['exporttype'], $output);

    $msg = $I18N->Msg('xoutputfilter_export_sql', $ex['filename'] . $ex['exporttype']);
    echo rex_info($msg);
  }
}

/**
 * Import CSV
 */
function xoutputfilter_import_csv($ex)
{
  global $REX;
  global $I18N;

  $icounter = 0;
  $ucounter = 0;
  $ecounter = 0;

  $fieldnames = array();
  $sqlinsert = array();

  $filename = xoutputfilter_getImportDir() . '/' . $ex['filename'];

  // CSV-Datei einlesen und SQL aufbauen
  $fp = @fopen($filename, 'r');
  if (!$fp)
  {
    echo rex_warning($I18N->Msg('xoutputfilter_import_errorfile', $ex['filename']));
    return;
  }

  while (($line_array = fgetcsv($fp, 30384, ';')) !== FALSE )
  {
    if(count($fieldnames) == 0) // erste Zeile, Feldnamen merken
    {
      $fieldnames = $line_array;
      //var_dump($fieldnames);
    }
    else // SQL-Insert aufbauen
    {
      $sqlinsert[$icounter] = "INSERT INTO `" . $REX['TABLE_PREFIX'] . "420_xoutputfilter` ( `id`";

      // Feldnamen
      unset($fieldnames[0]);
      foreach($fieldnames as $key => $val)
      {
        $sqlinsert[$icounter] .= ', `'.$val. '` ';
      }
      $sqlinsert[$icounter] .= ' ) VALUES ( null';

      // Werte
      unset($line_array[0]);
      foreach($line_array as $key => $val)
      {
        $srch = array("\~q~", "\~n~", "\~r~", "\~t~", "\~d~");
        $repl = array('"', "\n", "\r", "\t", ';');
        $val = str_replace($srch, $repl, $val);
        $sqlinsert[$icounter] .= ', \''.mysql_real_escape_string($val). '\' ';
      }

      $sqlinsert[$icounter] .= ' )';
      $icounter++;
    }
  }

  // SQL's ausführen
  //$sql = rex_sql::factory();
  $sql = new rex_sql();
  $sql->debugsql = 0;

  $errormsg = '';

  foreach($sqlinsert as $key => $val)
  {
    $sql->setQuery($val);
    if ($sql->getError())
    {
      $errormsg .= 'Line '.($key+2). ': '.$sql->getError().'<br />';
      $ecounter++;
    }
    else
    {
      $ucounter++;
    }
  }

  // Meldung ausgeben
  $msg = $I18N->Msg('xoutputfilter_import_csv', $ex['filename']);
  $msg .= '<br />' . $I18N->Msg('xoutputfilter_import_csv_count', $ucounter, $ecounter);
  echo rex_info($msg);

  if ($errormsg <> '')
  {
    echo rex_warning($errormsg);
  }
}

/**
 * Import SQL
 */
function xoutputfilter_import_sql($ex)
{
  global $REX;
  global $I18N;

  $errormsg = '';

  // SQL-Datei einlesen
  $filename = xoutputfilter_getImportDir() . '/' . $ex['filename'];
  $conts = rex_get_file_contents($filename);

  // Versionsstempel prüfen
  // ## Redaxo Database Dump Version x.x
  $version = strpos($conts, '## Redaxo Database Dump Version '.$REX['VERSION']);
  if($version === false)
  {
    $errormsg = $I18N->msg('xoutputfilter_import_wrongfile');
    $errormsg .= '<br />[## Redaxo Database Dump Version '.$REX['VERSION'].'] is missing';
    echo rex_warning($errormsg);
    return;
  }

  // Versionsstempel entfernen
  $conts = trim(str_replace('## Redaxo Database Dump Version '.$REX['VERSION'], '', $conts));

  // Prefix prüfen
  // ## Prefix xxx_
  if(preg_match('/^## Prefix ([a-zA-Z0-9\_]*)/', $conts, $matches) && isset($matches[1]))
  {
    // prefix entfernen
    $prefix = $matches[1];
    $conts = trim(str_replace('## Prefix '. $prefix, '', $conts));
  }
  else
  {
    // Prefix wurde nicht gefunden
    $errormsg = $I18N->msg('xoutputfilter_import_wrongfile');
    $errormsg .= '<br />[## Prefix '. $REX['TABLE_PREFIX'] .'] is missing';
    echo rex_warning($errormsg);
    return;
  }

  // Charset prüfen
  // ## charset xxx_
  if(preg_match('/^## charset ([a-zA-Z0-9\_\-]*)/', $conts, $matches) && isset($matches[1]))
  {
    // charset entfernen
    $charset = $matches[1];
    $conts = trim(str_replace('## charset '. $charset, '', $conts));
    if($I18N->msg('htmlcharset') == 'utf-8' AND $charset != 'utf-8')
    {
      $conts = utf8_encode($conts);
    }
    elseif($I18N->msg('htmlcharset') != $charset)
    {
      $return['message'] = $I18N->msg('im_export_no_valid_charset').'. '.$I18N->msg('htmlcharset').' != '.$charset;
      return $return;
      $errormsg = $I18N->msg('xoutputfilter_import_wrongfile');
      $errormsg .= '<br />Invalid charset '. $charset;
      echo rex_warning($errormsg);
      return;      
    }
  }
  else
  {
      $errormsg = $I18N->msg('xoutputfilter_import_wrongfile');
      $errormsg .= '<br />[## charset '. $I18N->msg('htmlcharset') .'] is missing';
      echo rex_warning($errormsg);
      return;      
  }
  
  // Prefix im export mit dem der installation angleichen
  if($REX['TABLE_PREFIX'] != $prefix)
  {
    // Hier case-insensitiv ersetzen, damit alle möglich Schreibweisen (TABLE TablE, tAblE,..) ersetzt werden
    // Dies ist wichtig, da auch SQLs innerhalb von Ein/Ausgabe der Module vom rex-admin verwendet werden
    $conts = preg_replace('/(TABLE `?)' . preg_quote($prefix, '/') .'/i', '$1'. $REX['TABLE_PREFIX'], $conts);
    $conts = preg_replace('/(INTO `?)'  . preg_quote($prefix, '/') .'/i', '$1'. $REX['TABLE_PREFIX'], $conts);
    $conts = preg_replace('/(EXISTS `?)'. preg_quote($prefix, '/') .'/i', '$1'. $REX['TABLE_PREFIX'], $conts);
  }  
  
  // Import
  // Datei aufteilen
  $lines = array();
  if (!function_exists('PMA_splitSqlFile'))
  {
    include_once ($REX['INCLUDE_PATH'].'/functions/function_rex_addons.inc.php');
  }
  PMA_splitSqlFile($lines, $conts, 0);  
  
  //$sql = rex_sql::factory();
  $sql = new rex_sql();
  
  foreach ($lines as $line) 
  {
    $line['query'] = trim($line['query']);
    
    if(rex_lang_is_utf8() AND strpos($line['query'], 'CREATE TABLE') === 0 AND !strpos($line['query'], 'DEFAULT CHARSET'))
    {
      $line['query'] .= ' DEFAULT CHARSET=utf8';
    }
    elseif(!rex_lang_is_utf8() AND strpos($line['query'], 'CREATE TABLE') === 0 AND !strpos($line['query'], 'DEFAULT CHARSET'))
    {
      $line['query'] .= ' DEFAULT CHARSET=latin1';
    }

    $sql->setQuery($line['query']);

    if($sql->hasError())
    {
      $errormsg .= "<br />". $sql->getError();
    }
  }  
  
  // Meldung ausgeben
  $msg = $I18N->Msg('xoutputfilter_import_sql', $ex['filename']);
  //$msg .= '<br />' . $I18N->Msg('xoutputfilter_import_sql_count', count($lines));
  echo rex_info($msg);

  if ($errormsg <> '')
  {
    echo rex_warning($errormsg);
  }
}
