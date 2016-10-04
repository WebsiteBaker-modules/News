<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: comment.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/comment.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */

// Include config file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('wb')) { require(WB_PATH.'/framework/class.wb.php');  }
// Create new frontend object
if (!isset($wb) || !($wb instanceof wb)) { $wb = new wb(); }

// Check if there is a post id
// $post_id = $wb->checkIDKEY('post_id', false, 'GET');
$requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
$aRequestVars  = (isset(${$requestMethod}) ? ${$requestMethod} : null);
$section_id = intval(isset($aRequestVars['section_id'])) ? $aRequestVars['section_id'] : (isset($section_id) ? intval($section_id) : 0);
$post_id = (intval(isset($aRequestVars['post_id'])) ? $aRequestVars['post_id'] : (isset($post_id) ? intval($post_id) : 0));
$position = (isset($aRequestVars['p']) ? $aRequestVars['p'] : '' );
/*
$post_id = (int)$_GET['post_id'];
$section_id = (int)$_GET['section_id'];
$position = 0;
*/
if (!$post_id OR !isset($_GET['section_id']) OR !is_numeric($_GET['section_id'])) {
    $_SESSION['message'][] = ('ABORT::'.$MESSAGE['GENERIC_SECURITY_ACCESS'] );
    exit();
}

// Query post for page id
$query_post = $database->query("SELECT post_id,title,section_id,page_id FROM ".TABLE_PREFIX."mod_news_posts WHERE post_id = '$post_id'");
if($query_post->numRows() == 0)
{
    header("Location: ".WB_URL."/index.php");
    exit( 0 );
}
else
{
    $fetch_post = $query_post->fetchRow( MYSQLI_ASSOC );
    $page_id = $fetch_post['page_id'];
    $section_id = $fetch_post['section_id'];
    $post_id = $fetch_post['post_id'];
    $post_title = $fetch_post['title'];
    define('SECTION_ID', $section_id);
    define('POST_ID', $post_id);
    define('POST_TITLE', $post_title);

    // don't allow commenting if its disabled, or if post or group is inactive
    $t = time();
    $table_posts = TABLE_PREFIX."mod_news_posts";
    $table_groups = TABLE_PREFIX."mod_news_groups";
    $query = $database->query("
        SELECT p.post_id
        FROM $table_posts AS p LEFT OUTER JOIN $table_groups AS g ON p.group_id = g.group_id
        WHERE p.post_id='$post_id' AND p.commenting != 'none' AND p.active = '1' AND ( g.active IS NULL OR g.active = '1' )
        AND (p.published_when = '0' OR p.published_when <= $t) AND (p.published_until = 0 OR p.published_until >= $t)
    ");
    if($query->numRows() == 0)
    {
        header("Location: ".WB_URL."/index.php");
        exit( 0 );
    }

    // don't allow commenting if ASP enabled and user doesn't comes from the right view.php
    if(ENABLED_ASP && (!isset($_SESSION['comes_from_view']) OR $_SESSION['comes_from_view']!=POST_ID))
    {
        header("Location: ".WB_URL."/index.php");
        exit( 0 );
    }

    // Get page details
    $query_page = $database->query("SELECT parent,page_title,menu_title,keywords,description,visibility FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'");
    if($query_page->numRows() == 0)
    {
        header("Location: ".WB_URL."/index.php");
        exit( 0 );
    }
    else
    {
        $page = $query_page->fetchRow( MYSQLI_ASSOC );
        // Required page details
        define('PAGE_CONTENT', WB_PATH.'/modules/news/comment_page.php');
        // Include index (wrapper) file
        require(WB_PATH.'/index.php');
    }
}
