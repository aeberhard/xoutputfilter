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

unset($_SESSION['xoutputfilter']);

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');
$active = rex_request('active', 'string');
$runtimeinfo = rex_request('runtimeinfo', 'string');
$excludecats = rex_request('excludecats', 'string');
$excludeids = rex_request('excludeids', 'string');

$config_file = $REX['INCLUDE_PATH'] . '/addons/xoutputfilter/config.inc.php';

if ($func == 'update')
{
  $REX['ADDON']['xoutputfilter']['active'] = $active;
  $REX['ADDON']['xoutputfilter']['runtimeinfo'] = $runtimeinfo;
  $REX['ADDON']['xoutputfilter']['excludecats'] = $excludecats;
  $REX['ADDON']['xoutputfilter']['excludeids'] = $excludeids;
  $content = '
$REX[\'ADDON\'][\'xoutputfilter\'][\'active\'] = \''.$active.'\';
$REX[\'ADDON\'][\'xoutputfilter\'][\'runtimeinfo\'] = \''.$runtimeinfo.'\';
$REX[\'ADDON\'][\'xoutputfilter\'][\'excludecats\'] = \''.$excludecats.'\';
$REX[\'ADDON\'][\'xoutputfilter\'][\'excludeids\'] = \''.$excludeids.'\';
';
  if(rex_replace_dynamic_contents($config_file, $content) !== false)
    echo rex_info($I18N->msg('xoutputfilter_config_saved'));
  else
    echo rex_warning($I18N->msg('xoutputfilter_config_not_saved'));	
}
if(!xoutputfilter_is_writable($config_file))
  echo rex_warning($I18N->msg('xoutputfilter_error_notwriteable', $config_file));

$activecheck = '';
if ($REX['ADDON']['xoutputfilter']['active'] == '1')
{
  $activecheck = 'checked="checked"';
}
$runtimeinfocheck = '';
if ($REX['ADDON']['xoutputfilter']['runtimeinfo'] == '1')
{
  $runtimeinfocheck = 'checked="checked"';
}
?>

<div class="rex-addon-output">
<div class="rex-form">
  <h2 class="rex-hl2"><?php echo $I18N->msg('xoutputfilter_config_title'); ?></h2>
  
  <form action="index.php" method="post">
  <fieldset class="rex-form-col-1">
  
  <div class="rex-form-wrapper">
    <input type="hidden" name="page" value="<?php echo $page; ?>" />
    <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
    <input type="hidden" name="func" value="update" />
      
    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-checkbox"style="display:inline !important;">
        <label for="active" style="width:145px !important;"><?php echo $I18N->msg('xoutputfilter_config_active'); ?></label>
        <input type="checkbox" <?php echo $activecheck; ?> value="1" id="active" name="active" />
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-checkbox"style="display:inline !important;">
        <label for="runtimeinfo" style="width:145px !important;"><?php echo $I18N->msg('xoutputfilter_config_runtimeinfo'); ?></label>
        <input type="checkbox" <?php echo $runtimeinfocheck; ?> value="1" id="runtimeinfo" name="runtimeinfo" />
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-text">
        <label for="excludecats"><?php echo $I18N->msg('xoutputfilter_config_excludecats'); ?></label>
        <input class="rex-form-text" type="text" id="excludecats" name="excludecats" value="<?php echo $REX['ADDON']['xoutputfilter']['excludecats']; ?>" />
      </p>
    </div>
    
    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-text">
        <label for="excludeids"><?php echo $I18N->msg('xoutputfilter_config_excludeids'); ?></label>
        <input class="rex-form-text" type="text" id="excludeids" name="excludeids" value="<?php echo $REX['ADDON']['xoutputfilter']['excludeids']; ?>" />
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-submit">
        <input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N->msg('update'); ?>" />
      </p>
    </div>
    
  </div>
  
  </fieldset>
  </form>

</div>
</div>
