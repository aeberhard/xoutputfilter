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

$mypage = 'xoutputfilter';

if (isset($I18N) && is_object($I18N))
{
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang/');
  $REX['ADDON']['name'][$mypage] = $I18N->msg('xoutputfilter_menu_link');
}

$REX['ADDON']['perm'][$mypage] = $mypage.'[]';

$REX['ADDON']['version'][$mypage] = '2.0';
$REX['ADDON']['author'][$mypage] = 'Andreas Eberhard';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['PERM'][] = $mypage.'[]';

// --- DYN
$REX['ADDON']['xoutputfilter']['active'] = '1';
$REX['ADDON']['xoutputfilter']['runtimeinfo'] = '1';
$REX['ADDON']['xoutputfilter']['excludecats'] = '';
$REX['ADDON']['xoutputfilter']['excludeids'] = 'a356_ajax';
// --- /DYN

// ---------- Backend, Perms, Subpages etc.
if ($REX['REDAXO'] && $REX['USER'])
{
  $REX['EXTPERM'][] = $mypage.'[settings]';
  $REX['EXTPERM'][] = $mypage.'[help]';

  $REX['ADDON'][$mypage]['SUBPAGES'] = array();

  if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($mypage.'[settings]'))
    $REX['ADDON'][$mypage]['SUBPAGES'][] = array ('', $I18N->msg('xoutputfilter_menu_settings'));
}

include($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/classes/class.'.$mypage.'.inc.php');

include($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/functions/functions.inc.php');

// Plugins in gewünschter Reihenfolge einbinden
$xopf_plugins = array('languages', 'abbrev', 'acronym', 'frontend', 'backend', 'import_export');
foreach ($xopf_plugins as $myplugin)
{
  if ((isset($REX['ADDON']['plugins'][$mypage])) and ($REX['ADDON']['plugins'][$mypage]['status'][$myplugin]=='1'))
  {
    include($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/plugins/'.$myplugin.'/functions/functions.inc.php');  
    if (function_exists($mypage.'_'.$myplugin))
      rex_register_extension('OUTPUT_FILTER', $mypage.'_'.$myplugin);
    if ($REX['REDAXO'])
    {
      if (isset($I18N) && is_object($I18N))    
         $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/plugins/' . $myplugin . '/lang');
      if ($REX['REDAXO'] && $REX['USER'])
      {
        if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($mypage.'['.$myplugin.']'))
        {
          $REX['ADDON'][$mypage]['SUBPAGES'][] = array('plugin.' . $myplugin, $I18N->msg('xoutputfilter_'.$myplugin.'_menu_entry'));
        }
      }
    }
  }
}

if ($REX['REDAXO'] && $REX['USER'])
{
  if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($mypage.'[help]'))
    $REX['ADDON'][$mypage]['SUBPAGES'][] = array ('help', $I18N->msg('xoutputfilter_menu_help'));
}
