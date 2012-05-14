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

// Sprachmenü
xoutputfilter_langtoolbar($subpage);

$table = $REX['TABLE_PREFIX'] . '420_xoutputfilter';
$func = rex_request('func', 'string', '');
$entry_id = rex_request('entry_id', 'int', -1);
$fieldSet = $I18N->Msg('xoutputfilter_frontend_fieldset');
$filter = rex_request('filter', 'int', 0);

// Filter setzen
if ($filter === 1)
{
  $_SESSION['xoutputfilter']['frontend']['filter1'] = trim(rex_request('filter1', 'string', ''));
  $_SESSION['xoutputfilter']['frontend']['filter2'] = trim(rex_request('filter2', 'string', ''));  
}
if (!isset($_SESSION['xoutputfilter']['frontend']['filter1']))
{
  $_SESSION['xoutputfilter']['frontend']['filter1'] = '';
  $_SESSION['xoutputfilter']['frontend']['filter2'] = '';
}

// Hinzufügen / Bearbeiten
if ($func == 'add' || $func == 'edit')
{

  echo '<div class="rex-toolbar"><div class="rex-toolbar-content">';
  echo '<p><a class="rex-back" href="index.php?page='.$page.'&amp;clang='.$REX['CUR_CLANG'].'&amp;subpage='.$subpage.'">'.$I18N->Msg('xoutputfilter_frontend_back').'</a></p>';
  echo '</div></div>';

  echo '<div class="rex-addon-output-v2">';

  require_once $REX['INCLUDE_PATH'] . '/addons/xoutputfilter/plugins/frontend/classes/class.form.inc.php';

  $form = new rex_form_xo_frontend($table, $fieldSet, 'id='. $entry_id, 'post', false, 'rex_form_xo_frontend');
  //$form = rex_form::factory($table, $fieldSet, 'id='. $entry_id, 'post', false, 'rex_form_xo_frontend');

  $form->addParam('clang', $REX['CUR_CLANG']);

  if($func == 'edit')
  {
    $form->addParam('entry_id', $entry_id);
  }
  $form->addHiddenField('typ', 4);
  $form->addHiddenField('lang', $REX['CUR_CLANG']);

  $field = &$form->addTextField('name');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_name'));

  $field = &$form->addTextField('description');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_description'));

  $field = &$form->addSelectField('active');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_active'));
  $select = &$field->getSelect();
  $select->addOption($I18N->msg('yes'), 1);
  $select->addOption($I18N->msg('no'), 0);
  $select->setSize(1);

  $field = &$form->addSelectField('insertbefore');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_insertbefore'));
  $select = &$field->getSelect();
  $select->addOption($I18N->msg('xoutputfilter_frontend_insertbefore0'), 0);
  $select->addOption($I18N->msg('xoutputfilter_frontend_insertbefore1'), 1);
  $select->addOption($I18N->msg('xoutputfilter_frontend_insertbefore2'), 2);
  $select->addOption($I18N->msg('xoutputfilter_frontend_insertbefore3'), 3);
  $select->addOption($I18N->msg('xoutputfilter_frontend_insertbefore4'), 4);
  $select->setSize(1);

  $field = &$form->addTextAreaField('marker');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_marker'));

  $field = &$form->addTextAreaField('html');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_html'));

  $field = &$form->addSelectField('categories');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_categories'));
  $select = &$field->getSelect();
  $select->setSize(10);
  $select->setMultiple(true);
  $cat_ids = array();
  if ($cats = OOCategory::getRootCategories())
  {
    foreach( $cats as $cat)
    {
      xoutputfilter_add_cat_options($select, $cat, $cat_ids);
    }
  }
  $select = xoutputfilter_add_rootart_options($select, $clang);

  $field = &$form->addSelectField('subcats');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_subcats'));
  $select = &$field->getSelect();
  $select->addOption($I18N->msg('yes'), 1);
  $select->addOption($I18N->msg('no'), 0);
  $select->setSize(1);

  $field = &$form->addSelectField('allcats');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_allcats'));
  $select = &$field->getSelect();
  $select->addOption($I18N->msg('yes'), 1);
  $select->addOption($I18N->msg('no'), 0);
  $select->setSize(1);

  $field = &$form->addSelectField('once');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_once'));
  $select = &$field->getSelect();
  $select->addOption($I18N->msg('yes'), 1);
  $select->addOption($I18N->msg('no'), 0);
  $select->setSize(1);

  $field = &$form->addTextField('excludeids');
  $field->setLabel($I18N->Msg('xoutputfilter_frontend_excludeids'));

  $form->show();

  echo '</div>';
}


// aktivieren/deaktivieren
if ($func == 'active')
{
  $toggle = rex_request('toggle', 'int', -1);
  if ($toggle==0)
  {
    $query = "update $table set `active` = '0' where id='".$entry_id."' ";
    $msg = $I18N->Msg('xoutputfilter_frontend_deactivated');
  }
  if ($toggle==1)
  {
    $query = "update $table set `active` = '1' where id='".$entry_id."' ";
    $msg = $I18N->Msg('xoutputfilter_frontend_activated');
  }
  $updsql = new rex_sql;
  $updsql->debugsql=0;
  $updsql->setQuery($query);
  $func = '';
  echo rex_info($msg);
}


// Löschen
if ($func == 'delete')
{
  $query = "delete from $table where id='".$entry_id."' ";
  $delsql = new rex_sql;
  $delsql->debugsql=0;
  $delsql->setQuery($query);
  $func = '';
  echo rex_info($I18N->Msg('xoutputfilter_frontend_deleted'));
}


// Liste
if ($func == '')
{
?>

  <div class="rex-toolbar">
    <form action="index.php" method="post" class="rex-form">
     <input type="hidden" name="filter" value="1" />
     <input type="hidden" name="page" value="<?php echo $page; ?>" />
     <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
     <input type="hidden" name="clang" value="<?php echo $clang; ?>" />
      <div class="rex-toolbar-content">
        <strong style="margin-right:30px;"><?php echo $I18N->Msg('xoutputfilter_frontend_filter')?></strong>
        <label for="rex_420_xoutputfilter_filter1"><?php echo $I18N->Msg('xoutputfilter_frontend_filter1')?></label>
        <input id="rex_420_xoutputfilter_filter1" class="rex-form-text" type="text" name="filter1" value="<?php echo $_SESSION['xoutputfilter']['frontend']['filter1']; ?>" />
        <label for="rex_420_xoutputfilter_filter2"><?php echo $I18N->Msg('xoutputfilter_frontend_filter2')?></label>
        <input id="rex_420_xoutputfilter_filter2" class="rex-form-text" type="text" name="filter2" value="<?php echo $_SESSION['xoutputfilter']['frontend']['filter2']; ?>" />
        <input id="rex_420_filter" type="submit" name="rex_420_filter" class="rex-form-submit submit" value="<?php echo $I18N->Msg('xoutputfilter_frontend_button_filter')?>" />
      </div>
    </form>
  </div>

<?php
  echo '<div class="rex-addon-output-v2">';	

  // SQL inkl. Filter
  $sqlfilter = '';
  if (isset($_SESSION['xoutputfilter']['frontend']['filter1']) and $_SESSION['xoutputfilter']['frontend']['filter1'] <> '')
  {
    $sqlfilter .= ' AND `name` like \'%'.$_SESSION['xoutputfilter']['frontend']['filter1'].'%\' ';  
  }
  if (isset($_SESSION['xoutputfilter']['frontend']['filter2']) and $_SESSION['xoutputfilter']['frontend']['filter2'] <> '')
  {
    $sqlfilter .= ' AND `description` like \'%'.$_SESSION['xoutputfilter']['frontend']['filter2'].'%\' ';  
  } 
  $sql = 'SELECT id, name, description, marker, active FROM '.$table.' WHERE typ = 4 AND lang = ' . $REX['CUR_CLANG'] . $sqlfilter . ' ORDER BY name ASC ';
  $list = rex_list::factory($sql);

  $list->addParam('clang', $REX['CUR_CLANG']);
  
  // <Caption tag>
  $list->setCaption($I18N->Msg('xoutputfilter_frontend_listtitle'));
  // summary Attribut bei einer neuartigen Tabellen definiton
  $list->addTableAttribute('summary', $I18N->Msg('xoutputfilter_frontend_listsummary'));

  // ICON
  $img = '<img src="../files/addons/xoutputfilter/xoutputfilter.gif" alt="###id### ###name###" title="###id### ###name###" />';
  $imgAdd = '<img src="../files/addons/xoutputfilter/xoutputfilter_plus.gif" alt="'.$I18N->Msg('xoutputfilter_frontend_addentry').'" title="'.$I18N->Msg('xoutputfilter_frontend_addentry').'" />';

  // ICON um eine neue Sprachersetzung zu definieren
  $imgHeader = '<a href="'. $list->getUrl(array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'add')) .'">'. $imgAdd .'</a>';

  // Das ist das ICON welches in der ICON-Spalte angezeigt wird.
  $list->addColumn($imgHeader, $img, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($imgHeader, array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'edit', 'entry_id' => '###id###'));

  $list->removeColumn('id');
  $list->addTableColumnGroup(array(40, '*', '*', '*', 110, 80));

  $list->setColumnLabel('name', $I18N->msg('xoutputfilter_frontend_name'));
  $list->setColumnParams('name', array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'edit', 'entry_id' => '###id###'));

  $list->setColumnLabel('description', $I18N->msg('xoutputfilter_frontend_description'));
  $list->setColumnParams('description', array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'edit', 'entry_id' => '###id###'));

  $list->setColumnLabel('marker', $I18N->msg('xoutputfilter_frontend_listmarker'));
  $list->setColumnParams('marker', array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'edit', 'entry_id' => '###id###'));
  $list->setColumnFormat('marker', 'custom',
    create_function(
      '$params',
      'global $I18N;
      $list = $params["list"];
    $str = $list->getValue("marker");
      $str = wordwrap($str, 40, "\n", 1);
      $str = htmlspecialchars($str);
      return $str;'
    )
  );

  $list->setColumnLabel('active', $I18N->msg('xoutputfilter_frontend_active'));
  $list->setColumnParams('active', array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'active', 'entry_id' => '###id###'));
  $list->setColumnFormat('active', 'custom',
    create_function(
      '$params',
      'global $I18N;
      $list = $params["list"];
      if ($list->getValue("active") == "1") {
        $params = array("toggle" => "0");
        $str = $I18N->msg("yes") . " - ";
      } else {
        $params = array("toggle" => "1");
        $str = $I18N->msg("no") . " - ";
      }		
      return $str . $list->getColumnLink("active", $list->getValue("active") != "1" ?
      "<span class=\"rex-offline\">".$I18N->msg("xoutputfilter_frontend_actentry")."</span>" :
      "<span class=\"rex-online\">".$I18N->msg("xoutputfilter_frontend_deactentry")."</span>",
      $params);'
    )
  );

  $list->addColumn($I18N->msg('xoutputfilter_frontend_func'), $I18N->msg('xoutputfilter_frontend_delentry'));
  $list->setColumnParams($I18N->msg('xoutputfilter_frontend_func'), array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'delete', 'entry_id' => '###id###'));
  $list->addLinkAttribute($I18N->msg('xoutputfilter_frontend_func'), 'onclick', 'return confirm(\'[###name###] - '.$I18N->msg('xoutputfilter_frontend_delentry').' ?\')');

  $list->setNoRowsMessage($I18N->Msg('xoutputfilter_frontend_nodata'));

  $list->show();
  echo '</div>';
}
