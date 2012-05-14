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

/*abstract*/ class rex_form_xo_frontend extends rex_form
{

  /*protected*/ function validate()
  {
    global $I18N;
    $msg = '';

    $el =& $this->getElement($this->fieldset, 'name');
    if (trim($el->getValue()) == '') {
      $msg .= $I18N->msg('xoutputfilter_frontend_noname')."<br />";
    }

    $el =& $this->getElement($this->fieldset, 'description');
    if (trim($el->getValue()) == '') {
      $msg .= $I18N->msg('xoutputfilter_frontend_nodesc')."<br />";
    }

    $el =& $this->getElement($this->fieldset, 'marker');
    if (trim($el->getValue()) == '') {
      $msg .= $I18N->msg('xoutputfilter_frontend_nomarker')."<br />";
    }

    $el =& $this->getElement($this->fieldset, 'insertbefore');
    $typ = trim($el->getValue());
  
    $el =& $this->getElement($this->fieldset, 'html');
    /*if (trim($el->getValue()) == '') {
      $msg .= $I18N->msg('xoutputfilter_frontend_nohtml')."<br />";
    }*/
    $phprc = xoutputfilter_eval_code($el->getValue());
    //echo "<pre>";var_dump($phprc);echo "</pre>";
    if ($phprc['error'])
    {
      $msg .= $phprc['phperror']."<br />";
    }

    if ($msg<>'')
      return $msg;
    else
      return true;
  }

} // End class rex_form_xo_frontend
