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

$rexversion = isset($REX['VERSION']) ? $REX['VERSION'] . $REX['SUBVERSION'] : $REX['version'] . $REX['subversion'];
if ($rexversion < 42)
{
  $REX['ADDON']['installmsg'][$addonname] = 'XOutputFilter benötigt REDAXO Version >= 4.2.x';
  return;
}

$addonname = 'xoutputfilter';

$error = '';

// Plugins automatisch mitinstallieren
$plugins = array('languages');

$ADDONS = rex_read_addons_folder();
$PLUGINS = array();
foreach($ADDONS as $_addon)
{
  $PLUGINS[$_addon] = rex_read_plugins_folder($_addon);
}

$pluginManager = new rex_pluginManager($PLUGINS, $addonname);

foreach($plugins as $pluginname)
{
  // plugin installieren
  if(($instErr = $pluginManager->install($pluginname)) !== true)
  {
    $error = $instErr;
  }

  // plugin aktivieren
  if ($error == '' && ($actErr = $pluginManager->activate($pluginname)) !== true)
  {
    $error = $actErr;
  }

  if($error != '')
  {
    break;
  }
}

if ($error != '')
  $REX['ADDON']['installmsg'][$addonname] = $error;
else
  $REX['ADDON']['install'][$addonname] = true;
