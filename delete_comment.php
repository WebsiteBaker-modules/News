<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.2.2 and higher
 * @version         $Id: delete_comment.php 67 2017-03-03 22:14:28Z manu $
 * @filesource        $HeadURL: svn://isteam.dynxs.de/wb2.10/tags/WB-2.10.0/wb/modules/news/delete_comment.php $
 * @lastmodified    $Date: 2017-03-03 23:14:28 +0100 (Fr, 03. Mrz 2017) $
 *
 */

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }

$admin_header = true;
// Tells script to update when this page was last updated
$update_when_modified = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

$comment_id = intval($admin->checkIDKEY('comment_id', false, 'GET'));
$post_id = intval($admin->checkIDKEY('post_id', false, 'GET'));
if (!$post_id || !$comment_id) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], WB_URL.'/modules/news/modify_post.php?page_id='.$page_id.'&section_id='.$section_id/*.'&post_id='.$post_id */);
} else {
    $post_id = $admin->getIDKEY($post_id);
}

// Update row
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_news_comments` '
          . 'WHERE `comment_id` = '.$database->escapeString($comment_id);
    $database->query($sql);

// Check if there is a db error, otherwise say successful
if($database->is_error())
{
    $admin->print_error($database->get_error(), WB_URL.'/modules/news/modify_post.php?page_id='.$page_id.'&section_id='.$section_id.'&post_id='.$post_id );
}
else
{
    $admin->print_success($TEXT['SUCCESS'], WB_URL.'/modules/news/modify_post.php?page_id='.$page_id.'&section_id='.$section_id.'&post_id='.$post_id );
}

// Print admin footer
$admin->print_footer();
