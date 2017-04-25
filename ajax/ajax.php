<?php

// initialize json_respond array  (will be sent back)
$aJsonRespond = array();
$aJsonRespond['message'] = 'ajax operation failed';
$aJsonRespond['success'] = FALSE;

        if(!isset($_POST['action']) )
        {
            $aJsonRespond['message'] = '"action" was not set';
            exit(json_encode($aJsonRespond));
        }
        $iRecordID = isset($_POST['iRecordID']) && ($_POST['iRecordID'] !=0)? intval($_POST['iRecordID']):0;

        // check if arguments are set
        if ($iRecordID !=0)
        {
            // require config for Core Constants
            require(dirname(dirname(dirname(__DIR__))).'/config.php');
            // retrieve Data from ajax data string
            $sDbRecordTable  = TABLE_PREFIX.'mod_news_posts';
            $sDbColumn  = 'post_id';
            $sModuleDIR  = $_POST['MODULE'];
            // Check if user has enough rights to do this:
            if (!class_exists('admin', false)){require(WB_PATH.'/framework/class.admin.php');}
            $admin = new admin('Modules', 'module_view', FALSE, FALSE);
            if (!($admin->is_authenticated() && $admin->get_permission($sModuleDIR, 'module')))
            {
                    $aJsonRespond['message'] = 'You\'re not allowed to make changes to this Module: '.$sModuleDIR;
                    $aJsonRespond['success'] = FALSE;
                    exit(json_encode($aJsonRespond));
            }
        } else {
            $aJsonRespond['message'] = 'Post arguments missing '.$iRecordID;
            $aJsonRespond['success'] = FALSE;
            exit(json_encode($aJsonRespond));
        }

        switch ($_POST['purpose'])
        {
            case 'active_status':
                // Check the Parameters
                if(isset($_POST['action']) && $_POST['action'] == 'active_status') {
                   // if(!is_numeric($iRecordID)) {
                   //         $iRecordID = $admin->checkIDKEY($iRecordID);
                   // }
                    $sql  = 'SELECT `active` FROM `'.$sDbRecordTable.'` ';
                    $sqlWhere  = 'WHERE `'.$sDbColumn.'` = '.(int)$iRecordID.' ';
                    $val = !(bool)$database->get_one($sql.$sqlWhere);
                    $sql = 'UPDATE `'.$sDbRecordTable.'` SET '
                         . '`active`='.($val ? true : 0).' ';
                    $database->query($sql.$sqlWhere);
                    if($database->is_error())
                    {
                        $aJsonRespond['message'] = 'db query failed: '.$database->get_error();
                        exit(json_encode($aJsonRespond));
                    } else {
                        $aJsonRespond['message'] = 'Activity Status successfully changed';
                        $aJsonRespond['action'] = intval($val);
                    }
                }
                else{
                  $aJsonRespond['message'] = "can't delete from list";
                  exit(json_encode($aJsonRespond));
                }
                break;
        }

// If the script is still running, set success to true
$aJsonRespond['success'] = true;
// and echo the json_respond to the ajax function
exit(json_encode($aJsonRespond));

