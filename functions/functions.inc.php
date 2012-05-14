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
 * Sprach-Toolbar ausgben
 */
function xoutputfilter_langtoolbar($subpage)
{
global $REX;
global $I18N;

  $clang = rex_request('clang', 'int');

  reset($REX['CLANG']);
  $num_clang = count($REX['CLANG']);

  if ($num_clang>1)
  {
    echo '
<!-- *** OUTPUT OF CLANG-TOOLBAR - START *** -->
   <div id="rex-clang" class="rex-toolbar">
   <div class="rex-toolbar-content">
     <ul>
       <li>'.$I18N->msg("languages").' : </li>';

    $stop = false;
    $i = 1;
    foreach($REX['CLANG'] as $key => $val)
    {
      if($i == 1)
        echo '<li class="rex-navi-first rex-navi-clang-'.$key.'">';
      else
        echo '<li class="rex-navi-clang-'.$key.'">';

      $val = rex_translate($val);

      if (!$REX['USER']->isAdmin() && !$REX['USER']->hasPerm('clang[all]') && !$REX['USER']->hasPerm('clang['. $key .']'))
      {
        echo '<span class="rex-strike">'. $val .'</span>';
        if ($clang == $key) $stop = true;
      }
      else
      {
        $class = '';
        if ($key==$clang) $class = ' class="rex-active"';
        echo '<a'.$class.' href="index.php?page='. $REX["PAGE"] . '&amp;subpage='. $subpage . '&amp;clang='. $key . '"'. rex_tabindex() .'>'. $val .'</a>';
      }

      echo '</li>';
      $i++;
    }

    echo '
     </ul>
   </div>
   </div>
<!-- *** OUTPUT OF CLANG-TOOLBAR - END *** -->
';
  }

}

/**
 * prüfen ob exclude Kategorie/Artikel-Id 
 */
function xoutputfilter_exclude_cat_art()
{
global $REX;

  if ($REX['ADDON']['xoutputfilter']['excludecats'] <> '')
  {
    $artId = OOArticle::getArticleById($REX['ARTICLE_ID']);
    $exc = explode(',', $REX['ADDON']['xoutputfilter']['excludecats']);
    foreach ($exc as $key => $val) {
      $exc[$key] = trim($val);
    }
    if (in_array($artId->getValue("category_id"), $exc))
      return true;
  }
  if ($REX['ADDON']['xoutputfilter']['excludeids'] <> '')
  {
    $exc = explode(',', $REX['ADDON']['xoutputfilter']['excludeids']);
    foreach ($exc as $key => $val) {
      $exc[$key] = trim($val);
    }
    if (in_array($REX['ARTICLE_ID'], $exc))
      return true;
  }
  return false;
}

/**
 * Kategorie prüfen ob Ausgabe
 */
function xoutputfilter_check_cat($catids = '', $subcats = 0)
{
global $REX;

  $exc = explode('|', $catids);
  foreach ($exc as $key => $val)
  {
    $exc[$key] = trim($val);
  }  

  $artId = OOArticle::getArticleById($REX['ARTICLE_ID']);

  if (in_array($artId->getValue('category_id'), $exc))
    return true;
  if ($artId->getValue('category_id')==0 and in_array('r'.$REX['ARTICLE_ID'], $exc)) // Root-Artikel
    return true;

  if ($subcats == 1)
  {
    $cat = OOCategory::getCategoryById($artId->getValue('category_id'));
    if ($cat)
    {
      while($cat = $cat->getParent())
      {
        if (in_array($cat->_id, $exc))
          return true;
      }
    }    
  }

  return false;
}

/**
 * Artikel-Id prüfen ob Ausgabe
 */
function xoutputfilter_exclude_art($artids = '')
{
global $REX;

  if ($artids <> '')
  {
    $exc = explode(',', $artids);
    foreach ($exc as $key => $val)
    {
      $exc[$key] = trim($val);
    }  
    if (in_array($REX['ARTICLE_ID'], $exc))
      return true;	
  }
  return false;
}

/**
 * prüfen exclude Page/Subpage
 */
function xoutputfilter_exclude_page_subpage()
{
global $REX;

  if ($REX['ADDON']['xoutputfilter']['excludecats'] <> '')
  {
    $exc = explode(',', $REX['ADDON']['xoutputfilter']['excludecats']);
    foreach ($exc as $key => $val)
    {
      $exc[$key] = trim($val);
    }
    if (in_array(rex_request('page', 'string', ''), $exc))
      return true;
  }
  if ($REX['ADDON']['xoutputfilter']['excludeids'] <> '')
  {
    $exc = explode(',', $REX['ADDON']['xoutputfilter']['excludeids']);
    foreach ($exc as $key => $val)
    {
      $exc[$key] = trim($val);
    }
    if (in_array(rex_request('subpage', 'string', ''), $exc))
      return true;

    // evtl. vorhandener String in Url auch übergehen
    foreach ($exc as $key => $val)
    {
      if (strstr($_SERVER['REQUEST_URI'], $val)){
        return true;
      }
    }
  }
  return false;
}

/**
 * page/subpage prüfen ob Ausgabe
 */
function xoutputfilter_check_module($incmodules)
{
global $REX;

  $inc = explode(',', $incmodules);
  foreach ($inc as $key => $val)
  {
    $inc[$key] = trim($val);
  }
  if (in_array(rex_request('page', 'string', ''), $inc))
    return true;
  return false;
}

/**
 * page/subpage prüfen ob Ausgabe
 */
function xoutputfilter_exclude_module($exmodules)
{
global $REX;

  $exc = explode(',', $exmodules);
  foreach ($exc as $key => $val)
  {
    $exc[$key] = trim($val);
  }
  if (in_array(rex_request('page', 'string', ''), $exc))
    return true;
  return false;
}

/**
 * PHP-Code ausführen mit Syntaxcheck
 */
function xoutputfilter_eval_code($code)
{
  $evalresult = array();
  $evalresult['error'] = false;
  $evalresult['phperror'] = '';
  $evalresult['evaloutput'] = $code;
  
  if (strstr($code, '<?php') and !strstr($code, 'exit;') and !strstr($code, 'die;'))
  {
    ob_start();
    $is = ini_set('display_errors', true);
    $evalresult['evaloutput'] = eval('?>' . $code );
    ini_set('display_errors', $is);
    if ($evalresult['evaloutput'] === false)
    {
      $evalresult['phperror'] = ob_get_contents();
      ob_end_clean();
      $evalresult['error'] = true;
    }
    else
    {
      $evalresult['evaloutput'] = ob_get_contents();
      ob_end_clean();
      // Warnings/Notices abfangen
      if (strpos($evalresult['evaloutput'], "eval()'d code") and (strpos($evalresult['evaloutput'], "Warning") or strpos($evalresult['evaloutput'], "Notice")))
      {
        $evalresult['phperror'] = '<br />' . str_replace(array('<!--','-->'), '', $evalresult['evaloutput']);
        $evalresult['error'] = false;
      }
    }
  }
  return $evalresult;
}

/**
 * Kategorien in die Selectbox übernehmen
 */
function xoutputfilter_add_cat_options($select, $cat, $cat_ids, $padding = 0)
{
global $REX;
  
  if (empty($cat)) 
  {
    return $select;
  }

  $cat_ids[] = $cat->getId();
  if( $REX['USER']->isValueOf("rights","admin[]") || $REX['USER']->isValueOf("rights","csw[0]") || $REX['USER']->isValueOf("rights","csr[".$cat->getId()."]") || $REX['USER']->isValueOf("rights","csw[".$cat->getId()."]") )
  {
    $select->addOption($cat->getName(), $cat->getId(), 0, 0, array('style' => 'padding-left:'.$padding.'px'));

    $childs = $cat->getChildren();
    if (is_array($childs))
    {
      $padding = $padding + 10;
      foreach ( $childs as $child)
      {
        xoutputfilter_add_cat_options($select, $child, $cat_ids, $padding);
      }
    }
  }
  return $select;
}

/**
 * Root-Artikel in Selectbox übernehmen
 */
function xoutputfilter_add_rootart_options($_select, $_clang)
{
  $attrs =  array('style' => 'color:#999', "disabled" => 'disabled');
  $select = $_select;
  $artroot = OOArticle::getRootArticles(false, $_clang);
  if (count($artroot) > 0)
  {
    $select->addOption('---', '', 0, 0, $attrs);
    foreach (OOArticle::getRootArticles(false, $_clang) as $artroot)
    {
      $select->addOption($artroot->getName(), 'r'.$artroot->getId(), 0, 0, null);
    }
  }
  return $select;
}

/**
 * Schreibberechtigung prüfen
 */
if (!function_exists("xoutputfilter_is_writable")) {
function xoutputfilter_is_writable($path)
{
  if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
    return xoutputfilter_is_writable($path.uniqid(mt_rand()).'.tmp');
  else if (is_dir($path))
    return xoutputfilter_is_writable($path.'/'.uniqid(mt_rand()).'.tmp');
  // check tmp file for read/write capabilities
  $rm = file_exists($path);
  $f = @fopen($path, 'a');
  if ($f===false)
    return false;
  fclose($f);
  if (!$rm)
    unlink($path);
  return true;
}
} // End function_exists