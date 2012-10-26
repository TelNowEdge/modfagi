<?php /* $Id */

function modfagi_getdest($id) {
    $name=sql("SELECT displayname FROM fagi WHERE id='$id'","getOne");
    return array('fagi,'.$name.',1');
}

function modfagi_getdestinfo($dest) {
    global $active_modules;

    if (substr(trim($dest),0,9) == 'ext-fagi,') {
        $exten = explode(',',$dest);
        $exten = $exten[1];
        $thisexten = modfagi_list($exten);
        if (empty($thisexten)) {
            return array();
        } else {
            //$type = isset($active_modules['announcement']['type'])?$active_modules['announcement']['type']:'setup';
            return array('description' => sprintf(_("FastAgi %s : %s"),$exten,$thisexten['description']),
                         'edit_url' => 'config.php?isplay=fagi&itemid='.urlencode($thisexten['id']),
                         );
        }
    } else {
        return false;
    }
  
}


function modfagi_check_destinations($dest=true) {
    global $active_modules;

    $destlist = array();
    if (is_array($dest) && empty($dest)) {
        return $destlist;
    }
    $sql = "SELECT id,displayname,truegoto,falsegoto FROM fagi ";
    if ($dest !== true) {
        $sql .= "WHERE (truegoto in ('".implode("','",$dest)."') ) OR (falsegoto in ('".implode("','",$dest)."') )";
    }
    $results = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

    $type = isset($active_modules['modfagi']['type'])?$active_modules['modfagi']['type']:'setup';

    foreach ($results as $result) {
        $thisdest    = $result['truegoto'];
        $thisid      = $result['id'];
        $description = sprintf(_("Fast AGI: %s"),$result['displayname']);
        $thisurl     = 'config.php?display=fagi&itemid='.urlencode($thisid);
        if ($dest === true || $dest = $thisdest) {
            $destlist[] = array(
                                'dest' => $thisdest,
                                'description' => $description,
                                'edit_url' => $thisurl,
                                );
        }
        $thisdest = $result['falsegoto'];
        if ($dest === true || $dest = $thisdest) {
            $destlist[] = array(
                                'dest' => $thisdest,
                                'description' => $description,
                                'edit_url' => $thisurl,
                                );
        }
    }
    return $destlist;
}

// returns a associative arrays with keys 'destination' and 'description'
function modfagi_destinations() {
    //get the list of fagi
    $results = modfagi_list();

    // return an associative array with destination and description
    if (isset($results)) {
        foreach($results as $result){
            $extens[] = array('destination' => 'fagi,'.$result['displayname'].',1', 'description' => $result['displayname']);
        }

        return $extens;
    } else {
        return null;
    }
}

/* 	Generates dialplan for fagi
	We call this with retrieve_conf
*/
function modfagi_get_config($engine) {
    global $ext;  // is this the best way to pass this?
    global $conferences_conf;

    switch($engine) {
    case "asterisk":
        $agilist = modfagi_list();
        if(is_array($agilist)) {
            $context = "fagi";
            foreach ($agilist as $item) {
                // write out the dialplan details
                $id=$item['displayname'];
                $fagidesc=$item['description'];
                $ext->add($context, $id, '', new ext_noop('Fast Agi: '.$fagidesc));
                $ext->add($context, $id, '', new ext_setvar('FAGIRUN','NO'));
                $agi="agi://".$item['host'].":".$item['port']."/".$item['path'];
                if ($item['query'] != '') {$agi .="?".$item['query'];}
                $ext->add($context, $id, '', new ext_agi($agi));
                $ext->add($context, $id, '', new ext_execif('$["${FAGICIDNAME}" !=""]', 'Set', 'CALLERID(name)=${FAGICIDNAME}'));
                $ext->add($context, $id, '', new ext_gotoif('$["${FAGIRUN}"="NO"]','notrun'));
                list($cont,$exten,$prio)=explode(',',$item['truegoto']);
                $ext->add($context, $id, '', new ext_goto($prio,$exten,$cont));
                list($cont,$exten,$prio)=explode(',',$item['falsegoto']);
                $ext->add($context, $id, 'notrun', new ext_goto($prio,$exten,$cont));
            }
        }
        break;
    }
}

function modfagi_list($displayname="") {
    if ($displayname == "") {
        $results = sql("SELECT * FROM fagi","getAll",DB_FETCHMODE_ASSOC);
    } else {
        $results = sql("SELECT * FROM fagi WHERE displayname='$displayname'","getAll",DB_FETCHMODE_ASSOC);
    }
    if(is_array($results)){
        return $results;
    } else { 
        return null;
    }
}

function modfagi_get($id){
    //get all the variables for the timecondition
    $results = sql("SELECT * FROM fagi WHERE id = '$id'","getRow",DB_FETCHMODE_ASSOC);
    return $results;
}

function modfagi_del($id){
    $results = sql("DELETE FROM fagi WHERE id = \"$id\"","query");
}


function modfagi_add($post){
    global $amp_conf;  
    extract($post);

    if ($agiport =='') {
        $agiport=(isset($amp_conf['FAGIPORT']))?$amp_conf['FAGIPORT']:'1985';
    }
    $results = sql("INSERT INTO fagi (displayname,description,host,port,path,query,truegoto,falsegoto) values 
   ('$displayname','$agidesc','$agihost','$agiport','$agipath','$agiquery','${$goto0.'0'}','${$goto1.'1'}')");

}

function modfagi_edit($id,$post){
    extract($post);

    if ($agiport =='') {
        $agiport=(isset($amp_conf['FAGIPORT']))?$amp_conf['FAGIPORT']:'1985';
    }
    $results = sql("UPDATE fagi set displayname='$displayname',description='$agidesc',host='$agihost',port='$agiport',path='$agipath',
query='$agiquery',truegoto='${$goto0.'0'}',falsegoto='${$goto1.'1'}' where id='$id'"); 
}

?>
