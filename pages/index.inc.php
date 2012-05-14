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

include $REX['INCLUDE_PATH'] . '/layout/top.php';

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');

rex_title($I18N->msg('xoutputfilter_title'), $REX['ADDON'][$page]['SUBPAGES']);

if (!$REX['USER']->isValueOf('rights', 'admin[]') and !$REX['USER']->isValueOf('rights', $page.'[settings]'))
{
  if ($subpage=='' and $REX['USER']->isValueOf('rights', $page.'[abbrev]'))
  {
    $subpage = 'plugin.abbrev';
  }
  if ($subpage=='' and $REX['USER']->isValueOf('rights', $page.'[acronym]'))
  {
    $subpage = 'plugin.acronym';
  }
  if ($subpage=='' and $REX['USER']->isValueOf('rights', $page.'[backend]'))
  {
    $subpage = 'plugin.backend';
  }
  if ($subpage=='' and $REX['USER']->isValueOf('rights', $page.'[frontend]'))
  {
    $subpage = 'plugin.frontend';
  }
  if ($subpage=='' and $REX['USER']->isValueOf('rights', $page.'[languages]'))
  {
    $subpage = 'plugin.languages';
  }
}
else
{
  if ($subpage=='')
  {
    $subpage = 'settings';
  }
}

if (substr($subpage, 0, 7)=="plugin.")
{
  $incfile = $REX['INCLUDE_PATH'] . '/addons/' . $page . '/plugins/' . substr($subpage, 7, strlen($subpage)-7) . '/pages/index.inc.php';
}
else
{
  $incfile = $REX['INCLUDE_PATH'] . '/addons/' . $page . '/pages/' . $subpage . '.inc.php';
}

if ($subpage=='')
{
  echo $I18N->msg('xoutputfilter_error_norights');
}
else
{
  include($incfile);
}

include $REX['INCLUDE_PATH'].'/layout/bottom.php';
