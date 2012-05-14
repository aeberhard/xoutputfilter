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

$mypage = 'xoutputfilter';
$myplugin = 'import_export';

$REX['ADDON']['version'][$myplugin] = '2.0';
$REX['ADDON']['author'][$myplugin] = 'Andreas Eberhard';
$REX['ADDON']['supportpage'][$myplugin] = 'forum.redaxo.de';

/*if (isset($I18N) && is_object($I18N))
{
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/plugins/' . $myplugin . '/lang');
}*/

$REX['EXTPERM'][] = $mypage.'['.$myplugin.']';

/*if ($REX["REDAXO"] && $REX['USER'])
{
  if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($mypage.'['.$myplugin.']'))
    $REX['ADDON'][$mypage]['SUBPAGES'][] = array('plugin.' . $myplugin, $I18N->msg('import_export_menu_entry'));
}*/

/*if ($REX["REDAXO"])
{
  include($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/plugins/'.$myplugin.'/functions/functions.inc.php');
}*/