<?php /* $Id */
//Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of the GNU General Public License
//as published by the Free Software Foundation; either version 2
//of the License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.


isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
//the item we are currently displaying
isset($_REQUEST['itemid'])?$itemid=tneCleanVar('int',$_REQUEST['itemid']):$itemid='';

$dispnum = "fagi"; //used for switch on config.php
$tabindex = 0;

//if submitting form, update database
switch ($action) {
	case "add":
		modfagi_add($_POST);
		needreload();
		redirect_standard();
	break;
	case "duplicate":
		$_REQUEST['itemid']=modfagi_duplicate($itemid);
		needreload();
		redirect_standard('itemid');
	break;
	case "delete":
		modfagi_del($itemid);
		needreload();
		redirect_standard();
	break;
	case "edit":  //just delete and re-add
		modfagi_edit($itemid,$_POST);
		needreload();
		redirect_standard('itemid');
	break;
}


//get list of time conditions
$fagis = modfagi_list();
?>

<!-- right side menu -->
<div class="rnav"><ul>
    <li><a id="<?php echo ($itemid=='' ? 'current':'') ?>" href="config.php?display=<?php echo urlencode($dispnum)?>"><?php echo _("Add Fast AGI")?></a></li>
<?php
if (isset($fagis)) {
	foreach ($fagis as $fagi) {
		echo "<li><a id=\"".($itemid==$fagi['id'] ? 'current':'')."\" href=\"config.php?display=".urlencode($dispnum)."&itemid=".urlencode($fagi['id'])."\">{$fagi['displayname']}</a></li>";
	}
}
?>
</ul></div>


<?php
if ($itemid){ 
  $thisItem = modfagi_get($itemid);
}
if (! $_REQUEST['fw_popover']) {
 echo  '<h2>'.($itemid ? _("Fast Agi:")." ". $thisItem['displayname'] : _("Fast Agi"))." </h2>\n";
}

if ($itemid){ 
  $delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=delete';
  $tlabel = sprintf(_("Delete Fast Agi: %s"),trim($thisItem['displayname']) == '' ? $itemid : $thisItem['displayname']." ($itemid) ");
  $label = '<span><img width="16" height="16" border="0" title="'.$tlabel.'" alt="" src="images/core_delete.png"/>&nbsp;'.$tlabel.'</span>';
?>
  <a href="<?php echo $delURL ?>"><?php echo $label; ?></a><br />
<?php
  $dupURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=duplicate';
  $tlabel = sprintf(_("Duplicate Fast Agi: %s"),trim($thisItem['displayname']) == '' ? $itemid : $thisItem['displayname']." ($itemid) ");
  $label = '<span><img width="16" height="16" border="0" title="'.$tlabel.'" alt="" src="images/core_add.png"/>&nbsp;'.$tlabel.'</span>';
?>
        <a href="<?php echo $dupURL ?>"><?php echo $label; ?></a><br />
<?php
  $usage_list = framework_display_destination_usage(modfagi_getdest($itemid));
  if (!empty($usage_list)) {
?>
  <a href="#" class="info"><?php echo $usage_list['text']?>:<span><?php echo $usage_list['tooltip']?></span></a>
<?php
  }
} 
?>
<form name="fagi" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkName(fagi);">
<input type="hidden" name="display" value="<?php echo $dispnum?>">
<input type="hidden" name="action" value="<?php echo ($itemid ? 'edit' : 'add') ?>">
<?php		if ($itemid){ ?>
  <input type="hidden" name="account" value="<?php echo $itemid; ?>">
<?php		}?>
<table>
<tr><td colspan="2"><h5><?php echo ($itemid ? _("Edit Fast AGI") : _("Add Fast AGI")) ?><hr></h5></td></tr>
<tr>
 <td><a href="#" class="info"><?php echo _("Name:")?><span><?php echo _("Give this Fast AGI a brief name to help you identify it.")?></span></a></td>
 <td><input type="text" name="displayname" value="<?php echo (isset($thisItem['displayname']) ? $thisItem['displayname'] : ''); ?>" tabindex="<?php echo ++$tabindex;?>"></td>
</tr>
<tr>
 <td><a href="#" class="info"><?php echo _("Description:")?><span><?php echo _("Give the description to help you identify it.")?></span></a></td>
 <td><input type="text" size="40" name="agidesc" value="<?php echo (isset($thisItem['description']) ? $thisItem['description'] : ''); ?>" tabindex="<?php echo ++$tabindex;?>"></td>
</tr>
<tr>
 <td><a href="#" class="info"><?php echo _("Host:")?><span><?php echo _("Host name or IP address")?></span></a></td>
 <td><input type="text" name="agihost" value="<?php echo (isset($thisItem['host']) ? $thisItem['host'] : ''); ?>" tabindex="<?php echo ++$tabindex;?>"></td>
</tr>
<tr>
 <td><a href="#" class="info"><?php echo _("Port:")?><span><?php echo _("Port FAGI server is listening at (default 1585, is possible to change default in amportal.conf with FAGIPORT)")?></span></a></td>
 <td><input type="text" name="agiport" size="8" value="<?php echo (isset($thisItem['port']) ? $thisItem['port'] : ''); ?>" tabindex="<?php echo ++$tabindex;?>"></td>
</tr>
								
<tr>
 <td><a href="#" class="info"><?php echo _("Path:")?><span><?php echo _("Path for FAGI<br/>e.g.: /cidlookup")?></span></a></td>
 <td><input type="text" name="agipath"  size="15" value="<?php echo (isset($thisItem['path']) ? $thisItem['path'] : ''); ?>" tabindex="<?php echo ++$tabindex;?>"></td>
</tr>
<tr>
 <td><a href="#" class="info"><?php echo _("Query:")?><span><?php echo _("Query string")?></span></a></td>
 <td><input type="text" name="agiquery" size="128" value="<?php echo (isset($thisItem['query']) ? $thisItem['query'] : ''); ?>" tabindex="<?php echo ++$tabindex;?>"></td>
</tr>

<tr><td colspan="2"><br><h5><?php echo _("Destination if Fast AGI return OK")?><hr></h5></td></tr>
<?php 
//draw goto selects
if (isset($thisItem)) {
  echo drawselects($thisItem['truegoto'],0);
} else { 
  echo drawselects(null, 0);
}
?>

<tr><td colspan="2"><br><h5><?php echo _("Destination if Fast AGI return NO (Default)")?><hr></h5></td></tr>

<?php 
//draw goto selects
if (isset($thisItem)) {
  echo drawselects($thisItem['falsegoto'],1);
} else { 
  echo drawselects(null, 1);
}
?>

<tr>
 <td colspan="2"><br><h6><input name="Submit" type="submit" value="<?php echo _("Submit")?>" tabindex="<?php echo ++$tabindex;?>"></h6></td>		
</tr>
</table>
</form>
  <script language="javascript">
  <!--  
function checkName(theForm) {
  var RegExPattern = /^[a-zA-Z][a-zA-z0-9]*$/;
  var msgInvalidName = "<?php echo _("Please enter a valid name (just asci, with out &_' ..) "); ?>";
  var name=theForm.displayname.value;

  if (name.match(RegExPattern))
   { 
    return true;
   }
return warnInvalid(theForm.displayname, msgInvalidName);
  }
//-->
</script>
