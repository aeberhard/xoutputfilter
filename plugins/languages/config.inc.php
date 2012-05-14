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

$mypage = 'xoutputfilter';
$myplugin = 'languages';

$REX['ADDON']['version'][$myplugin] = '2.0';
$REX['ADDON']['author'][$myplugin] = 'Andreas Eberhard';
$REX['ADDON']['supportpage'][$myplugin] = 'forum.redaxo.de';

/*if (isset($I18N) && is_object($I18N))
{
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/plugins/' . $myplugin . '/lang');
}*/

$REX['EXTPERM'][] = $mypage.'['.$myplugin.']';

/*if ($REX['REDAXO'] && $REX['USER'])
{
  if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($mypage.'['.$myplugin.']'))
  {
    $REX['ADDON'][$mypage]['SUBPAGES'][] = array('plugin.' . $myplugin, $I18N->msg('xoutputfilter_lang_menu_entry'));
  }	
}*/

//include($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/plugins/'.$myplugin.'/functions/functions.inc.php');

/*if (!$REX['REDAXO'])
{
  rex_register_extension('OUTPUT_FILTER', $mypage.'_languages');
}*/
