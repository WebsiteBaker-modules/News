<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       Website Baker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: save_comment.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/save_comment.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */

// Include config file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin')) { require(WB_PATH.'/framework/class.admin.php');  }
$requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
$aRequestVars  = (isset(${$requestMethod}) ? ${$requestMethod} : null);

// Get id
if(!isset($_POST['comment_id']) || !is_numeric($_POST['comment_id']) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id']))
{
    header("Location: ".ADMIN_URL."/pages/index.php");
    exit( 0 );
}
else
{
    $comment_id = (int)$_POST['comment_id'];
}

$admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = true;
// show the info banner
// $print_info_banner = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

if (!$admin->checkFTAN())
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id );
}

$id = intval($admin->getIDKEY($comment_id));

// Validate all fields
if($admin->get_post('title') == '' AND $admin->get_post('comment') == '')
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_FILL_IN_ALL'], WB_URL.'/modules/news/modify_comment.php?page_id='.$page_id.'&section_id='.$section_id.'comment_id='.$id);
}
else
{
    $title = strip_tags($admin->get_post('title'));
    $comment = strip_tags($admin->get_post('comment'));
    $post_id = $admin->getIDKEY($admin->get_post('post_id'));

    // do not allow droplets in user input!
    $title = $admin->StripCodeFromText( $title);
    $comment = $admin->StripCodeFromText( $comment);
}

// Update row
$sql  = 'UPDATE '.TABLE_PREFIX.'mod_news_comments SET '
      . '`title`=\''.$database->escapeString($title).'\', '
      . '`comment`=\''.$database->escapeString($comment).'\' '
      . ' WHERE `comment_id`=\''.$database->escapeString($comment_id).'\'';
$database->query($sql);

$admin->print_header();
// Check if there is a db error, otherwise say successful
if($database->is_error())
{
    $admin->print_error($database->get_error(), WB_URL.'/modules/news/modify_comment.php?page_id='.$page_id.'&section_id='.$section_id.'&comment_id='.$id);
}
else
{
    $admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();
