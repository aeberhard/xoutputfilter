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

  @ini_set('memory_limit', '64M');
  $func = rex_request('func', 'string');
  $ex['abbrev'] = rex_post('abbrev', 'string');
  $ex['acronym'] = rex_post('acronym', 'string');
  $ex['backend'] = rex_post('backend', 'string');
  $ex['frontend'] = rex_post('frontend', 'string');
  $ex['languages'] = rex_post('languages', 'string');
  $ex['exportdl'] = rex_post('exportdl', 'string');
  $ex['exporttype'] = rex_post('exporttype', 'string');
  $ex['filename'] = rex_request('filename', 'string');
  $ex['delfilename'] = '';

  // Export
  if ($func == 'export')
  {
    if ($ex['exporttype'] == '.csv')
    {
      xoutputfilter_export_csv($ex);
    }
    if ($ex['exporttype'] == '.sql')
    {
      xoutputfilter_export_sql($ex);
    }
  }
  
  // Import
  if ($func == 'import')
  {
//var_dump($_FILES)  ;

    // Upload prüfen
    if (isset($_FILES['FORM']) && $_FILES['FORM']['size']['importfile'] < 1 && $ex['filename'] == '')
    {
      echo rex_warning($I18N->msg("xoutputfilter_import_nofile"));
      $ex['filename'] = '';
    }

    // Upload File
    if (isset($_FILES['FORM']) && $_FILES['FORM']['size']['importfile'] > 0 && $ex['filename'] == '')
    {
       $file_temp  = xoutputfilter_getImportDir() . '/uploaded_' . $_FILES['FORM']['name']['importfile'];
       if (@move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'], $file_temp))
       {
         $ex['filename'] = 'uploaded_' . $_FILES['FORM']['name']['importfile'];
         $ex['delfilename'] = $file_temp;
       }
       else
       {
         echo rex_warning($I18N->msg("xoutputfilter_import_errorupload"));       
       }
       if (!xoutputfilter_endsWith($ex['filename'], '.csv') and !xoutputfilter_endsWith($ex['filename'], '.sql'))
       {
         echo rex_warning($I18N->msg("xoutputfilter_import_wrongfile"));
         $ex['filename'] = '';
       }
    }

    // Import starten
    if ($ex['filename'] <> '')
    {
      if (xoutputfilter_endsWith($ex['filename'], '.csv'))
      {
        xoutputfilter_import_csv($ex);
      }
      if (xoutputfilter_endsWith($ex['filename'], '.sql'))
      {
        xoutputfilter_import_sql($ex);
      }
    }
    
    // Upload-Datei löschen
    if ($ex['delfilename'] <> '')
    {
      @unlink($file_temp);
    }
  }
  
  // Delete
  if ($func == 'delete')
  {
    if (@unlink(xoutputfilter_getImportDir() . '/' . $ex['filename']))
    {
      echo rex_info($I18N->msg('xoutputfilter_import_deleted', $ex['filename']));
    }
    else
    {
      echo rex_warning($I18N->msg('xoutputfilter_import_notdeleted', $ex['filename']));
    }
  }  
  
  // Export Abkürzungen
  $abbrev_check = '';
  if (rex_post('abbrev', 'string') == '1' or $func <> 'export')
  {
    $abbrev_check = 'checked="checked"';
  }

  // Export Akronyme
  $acronym_check = '';
  if (rex_request('acronym', 'string') == '1' or $func <> 'export')
  {
    $acronym_check = 'checked="checked"';
  }

  // Export Backend-Ersetzungen
  $backend_check = '';
  if (rex_request('backend', 'string') == '1' or $func <> 'export')
  {
    $backend_check = 'checked="checked"';
  }

  // Export Frontend-Ersetzungen
  $frontend_check = '';
  if (rex_request('frontend', 'string') == '1' or $func <> 'export')
  {
    $frontend_check = 'checked="checked"';
  }

  // Export Sprachersetzungen
  $languages_check = '';
  if (rex_request('languages', 'string') == '1' or $func <> 'export')
  {
    $languages_check = 'checked="checked"';
  }

  // Export speichern oder download
  $exportdl = rex_post('exportdl', 'string', '');
  if ($exportdl=='download')
  {
    $checked_dlfile = ' checked="checked"';
    $checked_server = '';
  }
  else
  {
    $checked_server = ' checked="checked"';
    $checked_dlfile = '';
  }

  // Export-Typ csv oder sql
  $exporttype = rex_post('exporttype', 'string', '');
  if ($exporttype=='.sql')
  {
    $checked_sql = ' checked="checked"';
    $checked_csv = '';
  }
  else
  {
    $checked_csv = ' checked="checked"';
    $checked_sql = '';
  }

  // Export Dateiname
  $exportfilename = rex_post('filename', 'string', '');
  if (($exportfilename == '') or ($func == 'export'))
  {
    $exportfilename = 'xoutputfilter'.'_'.date('Ymd_His');
  }
  
if(!xoutputfilter_is_writable(xoutputfilter_getImportDir() . '/'))
  echo rex_warning($I18N->msg('xoutputfilter_import_notwriteable', xoutputfilter_getImportDir() . '/'));  
?>

<div class="rex-addon-output">
<div class="rex-form">

  <h2 class="rex-hl2"><?php echo $I18N->msg('xoutputfilter_export_title'); ?></h2>

  <form action="index.php" method="post">
  <fieldset class="rex-form-col-1">

  <div class="rex-form-wrapper">
  
    <input type="hidden" name="page" value="<?php echo $page; ?>" />
    <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
    <input type="hidden" name="func" value="export" />

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-radio rex-form-label-left">
        <label><strong><?php echo $I18N->msg('xoutputfilter_export_title_typ'); ?></strong></label>
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-radio rex-form-label-left">
        <input class="rex-form-radio" type="radio" id="exportdl_csv" name="exporttype" value=".csv"<?php echo $checked_csv; ?> onclick="jQuery('.selcb').each(function(){this.disabled = false;});" />
        <label for="exportdl_csv"><?php echo $I18N->msg('xoutputfilter_export_dlcsv'); ?></label>
      </p>
      <p class="rex-form-checkbox">
        <label for="languages" style="margin-left:10px !important;"><?php echo $I18N->msg('xoutputfilter_export_languages'); ?></label>
        <input type="checkbox" class="selcb" style="margin-left:-10px !important;" <?php echo $languages_check; ?> value="1" id="languages" name="languages" />
      </p>
      <p class="rex-form-checkbox xrex-form-label-right">
        <label for="abbrev" style="margin-left:10px !important;"><?php echo $I18N->msg('xoutputfilter_export_abbrev'); ?></label>
        <input type="checkbox" class="selcb" style="margin-left:-10px !important;" <?php echo $abbrev_check; ?> value="1" id="abbrev" name="abbrev" />
      </p>
      <p class="rex-form-checkbox">
        <label for="acronym" style="margin-left:10px !important;"><?php echo $I18N->msg('xoutputfilter_export_acronym'); ?></label>
        <input type="checkbox" class="selcb" style="margin-left:-10px !important;" <?php echo $acronym_check; ?> value="1" id="acronym" name="acronym" />
      </p>
      <p class="rex-form-checkbox">
        <label for="frontend" style="margin-left:10px !important;"><?php echo $I18N->msg('xoutputfilter_export_frontend'); ?></label>
        <input type="checkbox" class="selcb" style="margin-left:-10px !important;" <?php echo $frontend_check; ?> value="1" id="frontend" name="frontend" />
      </p>
      <p class="rex-form-checkbox">
        <label for="backend" style="margin-left:10px !important;"><?php echo $I18N->msg('xoutputfilter_export_backend'); ?></label>
        <input type="checkbox" class="selcb" style="margin-left:-10px !important;" <?php echo $backend_check; ?> value="1" id="backend" name="backend" />
      </p>
    </div>
   
    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-radio rex-form-label-left">
        <input class="rex-form-radio" type="radio" id="exportdl_sql" name="exporttype" value=".sql"<?php echo $checked_sql; ?> onclick="jQuery('.selcb').each(function(){this.disabled = true;});" />
        <label for="exportdl_sql"><?php echo $I18N->msg('xoutputfilter_export_dlsql'); ?></label>
      </p>
    </div>    

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-radio rex-form-label-left">
        <label><strong><?php echo $I18N->msg('xoutputfilter_export_title_dl'); ?></strong></label>
      </p>
    </div>
    
    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-radio rex-form-label-left">
        <input class="rex-form-radio" type="radio" id="exportdl_server" name="exportdl" value="server"<?php echo $checked_server; ?> />
        <label for="exportdl_server"><?php echo $I18N->msg('xoutputfilter_export_dlserver'); ?></label>
      </p>
      <p class="rex-form-radio rex-form-label-left">
        <input class="rex-form-radio" type="radio" id="exportdl_download" name="exportdl" value="download"<?php echo $checked_dlfile; ?> />
        <label for="exportdl_download"><?php echo $I18N->msg('xoutputfilter_export_dlfile'); ?></label>
      </p>
    </div>
    
    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-text">
        <label for="filename"><?php echo $I18N->msg('xoutputfilter_export_filename'); ?></label>
        <input class="rex-form-text" type="text" id="filename" name="filename" value="<?php echo $exportfilename; ?>" />
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-submit">
        <input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N->msg('xoutputfilter_btnexport'); ?>" />
      </p>
    </div>

  </div>

  </fieldset>
  </form>

</div>
</div>

<div class="rex-addon-output">
<div class="rex-form">

  <h2 class="rex-hl2"><?php echo $I18N->msg('xoutputfilter_import_title'); ?></h2>

  <form action="index.php" method="post" enctype="multipart/form-data">
  <fieldset class="rex-form-col-1">

  <div class="rex-form-wrapper">
    <input type="hidden" name="page" value="<?php echo $page; ?>" />
    <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
    <input type="hidden" name="func" value="import" />

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-file">
        <label for="importdbfile"><?php echo $I18N->msg('xoutputfilter_import_select'); ?></label>
        <input class="rex-form-file" type="file" id="importdbfile" name="FORM[importfile]" size="18" />
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-submit">
        <input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N->msg('xoutputfilter_btnimport'); ?>" />
      </p>
    </div>

    <div class="rex-addon-content">
      <?php echo $I18N->msg('xoutputfilter_import_hinweis'); ?>
    </div>

      <table class="rex-table" summary="<?php echo $I18N->msg('xoutputfilter_import_db_summary'); ?>">
        <caption><?php echo $I18N->msg('xoutputfilter_import_db_caption'); ?></caption>
        <colgroup>
          <col width="*" />
          <col width="110" />
          <col width="130" />
          <col width="80" span="2"/>
        </colgroup>
        <thead>
          <tr>
            <th><?php echo $I18N->msg('xoutputfilter_import_filename'); ?></th>
            <th><?php echo $I18N->msg('xoutputfilter_import_filesize'); ?></th>
            <th><?php echo $I18N->msg('xoutputfilter_import_createdate'); ?></th>
            <th colspan="2"><?php echo $I18N->msg('xoutputfilter_import_function'); ?></th>
          </tr>
        </thead>
        <tbody>
<?php
  $dir = xoutputfilter_getImportDir();

  $folder = xoutputfilter_readImportFolder('.csv');
  foreach ($folder as $file)
  {
    $filepath = $dir.'/'.$file;
    $filec = date('d.m.Y H:i', filemtime($filepath));
    $filesize = OOMedia::_getFormattedSize(filesize($filepath));

    echo '<tr>
            <td>'. $file .'</td>
            <td>'.$filesize.'</td>
            <td>'. $filec .'</td>
            <td><a href="index.php?page=xoutputfilter&amp;subpage=plugin.import_export&amp;func=import&amp;filename='. $file .'" title="'. $I18N->msg('xoutputfilter_import_importfile') .'" onclick="return confirm(\''. $I18N->msg('xoutputfilter_import_proceed_csv') .'\')">'. $I18N->msg('xoutputfilter_import_import') .'</a></td>
            <td><a href="index.php?page=xoutputfilter&amp;subpage=plugin.import_export&amp;func=delete&amp;filename='. $file .'" title="'. $I18N->msg('xoutputfilter_import_deletefile') .'" onclick="return confirm(\''. $I18N->msg('xoutputfilter_import_proceed_delete') .'\')">'. $I18N->msg('xoutputfilter_import_delete') .'</a></td>
          </tr>
  ';
  }

  $folder = xoutputfilter_readImportFolder('.sql');
  foreach ($folder as $file)
  {
    $filepath = $dir.'/'.$file;
    $filec = date('d.m.Y H:i', filemtime($filepath));
    $filesize = OOMedia::_getFormattedSize(filesize($filepath));

    echo '<tr>
            <td>'. $file .'</td>
            <td>'.$filesize.'</td>
            <td>'. $filec .'</td>
            <td><a href="index.php?page=xoutputfilter&amp;subpage=plugin.import_export&amp;func=import&amp;filename='. $file .'" title="'. $I18N->msg('xoutputfilter_import_importfile') .'" onclick="return confirm(\''. $I18N->msg('xoutputfilter_import_proceed_sql') .'\')">'. $I18N->msg('xoutputfilter_import_import') .'</a></td>
            <td><a href="index.php?page=xoutputfilter&amp;subpage=plugin.import_export&amp;func=delete&amp;filename='. $file .'" title="'. $I18N->msg('xoutputfilter_import_deletefile') .'" onclick="return confirm(\''. $I18N->msg('xoutputfilter_import_proceed_delete') .'\')">'. $I18N->msg('xoutputfilter_import_delete') .'</a></td>
          </tr>
  ';
  }
?>
        </tbody>
      </table>

  </div>

  </fieldset>
  </form>

</div>
</div>
