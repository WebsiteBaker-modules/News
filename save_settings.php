<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: save_settings.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/save_settings.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
$admin_header = false;
require(WB_PATH.'/modules/admin.php');
if (!$admin->checkFTAN())
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}
$admin->print_header();

// This code removes any <?php tags and adds slashes
$friendly = array('&lt;', '&gt;', '?php');
$raw = array('<', '>', '');
$header = $admin->StripCodeFromText( $_POST['header'] );
$post_loop = $admin->StripCodeFromText( $_POST['post_loop']);
$footer = $admin->StripCodeFromText( $_POST['footer']);
$post_header = $admin->StripCodeFromText( $_POST['post_header']);
$post_footer = $admin->StripCodeFromText( $_POST['post_footer']);
$comments_header = $admin->StripCodeFromText( $_POST['comments_header']);
$comments_loop = $admin->StripCodeFromText( $_POST['comments_loop']);
$comments_footer = $admin->StripCodeFromText( $_POST['comments_footer']);
$comments_page = $admin->StripCodeFromText( $_POST['comments_page']);
$commenting = $_POST['commenting'];
$posts_per_page = intval($_POST['posts_per_page']);
$use_captcha = intval($_POST['use_captcha']);
if(extension_loaded('gd') AND function_exists('imageCreateFromJpeg')) {
    $resize = $_POST['resize'];
} else {
    $resize = '';
}
$sql  = 'UPDATE '.TABLE_PREFIX.'mod_news_settings SET '
      . '`header`=\''.$database->escapeString($header).'\', '
      . '`post_loop`=\''.$database->escapeString($post_loop).'\', '
      . '`footer`=\''.$database->escapeString($footer).'\', '
      . '`posts_per_page`=\''.$database->escapeString($posts_per_page).'\', '
      . '`post_header`=\''.$database->escapeString($post_header).'\', '
      . '`post_footer`=\''.$database->escapeString($post_footer).'\', '
      . '`comments_header`=\''.$database->escapeString($comments_header).'\', '
      . '`comments_loop`=\''.$database->escapeString($comments_loop).'\', '
      . '`comments_footer`=\''.$database->escapeString($comments_footer).'\', '
      . '`comments_page`=\''.$database->escapeString($comments_page).'\', '
      . '`commenting`=\''.$database->escapeString($commenting).'\', '
      . '`resize`=\''.$database->escapeString($resize).'\', '
      . '`use_captcha`=\''.$database->escapeString($use_captcha).'\' '
      . 'WHERE `section_id`='.$database->escapeString($section_id).' '
      . '';
// Update settings
$database->query($sql);


// Check if there is a db error, otherwise say successful
if($database->is_error()) {
    $admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
    $admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();
