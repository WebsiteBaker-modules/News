<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       2004-2009, Ryan Djurovich
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.2.2 and higher
 * @version         $Id: move_down.php 67 2017-03-03 22:14:28Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/tags/WB-2.10.0/wb/modules/news/move_down.php $
 * @lastmodified    $Date: 2017-03-03 23:14:28 +0100 (Fr, 03. Mrz 2017) $
 *
 */

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');
$backlink = ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id;
// Get id
$pid = isset($aRequestVars['post_id']) ?$admin->checkIDKEY('post_id', false, 'GET'):0;
$gid = isset($aRequestVars['group_id']) ?$admin->checkIDKEY('group_id', false, 'GET'):0;
if (!$pid) {
    if (!$gid) {
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $backlink);
        exit();
    } else {
        $id = $gid;
        $id_field = 'group_id';
        $table = TABLE_PREFIX.'mod_news_groups';
    }
} else {
    $id = $pid;
    $id_field = 'post_id';
    $table = TABLE_PREFIX.'mod_news_posts';
}

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');

// Create new order object an reorder
$order = new order($table, 'position', $id_field, 'section_id');
if($order->move_down($id)) {
    $admin->print_success($TEXT['SUCCESS'], $backlink);
} else {
    $admin->print_error($TEXT['ERROR'], $backlink);
}

// Print admin footer
$admin->print_footer();
