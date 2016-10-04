<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       Website Baker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: rss.php 1485 2011-08-01 18:22:28Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/rss.php $
 * @lastmodified    $Date: 2011-08-01 20:22:28 +0200 (Mo, 01. Aug 2011) $
 *
 */

// Check that GET values have been supplied
if(isset($_GET['page_id']) && is_numeric($_GET['page_id'])) {
    $page_id = intval($_GET['page_id']);
} else {
    // something is gone wrong, send error header
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
    if (preg_match('/fcgi/i', php_sapi_name())) {
        header("Status: 204 No Content"); // RFC7231, Section 6.3.5
    } else {
        header("HTTP/1.0 204  No Content");
    }
    flush();
    exit;
}

if(isset($_GET['group_id']) && is_numeric($_GET['group_id'])) {
    $group_id = $_GET['group_id'];
    define('GROUP_ID', $group_id);
}

// Include WB files
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('frontend')) { require(WB_PATH.'/framework/class.frontend.php');  }
// Create new frontend object
if (!isset($wb) || !($wb instanceof frontend)) { $wb = new frontend(); }
$wb->page_id = $page_id;
$wb->get_page_details();
$wb->get_website_settings();

//checkout if a charset is defined otherwise use UTF-8
if(defined('DEFAULT_CHARSET')) {
    $charset=DEFAULT_CHARSET;
} else {
    $charset='utf-8';
}

// Sending XML header
header("Content-type: text/xml; charset=$charset" );

// Header info
// Required by CSS 2.0
echo '<?xml version="1.0" encoding="'.$charset.'"?>';
?>
<rss version="2.0">
    <channel>
        <title><![CDATA[<?php echo PAGE_TITLE; ?>]]></title>
        <link>http://<?php echo $_SERVER['SERVER_NAME']; ?></link>
        <description><![CDATA[<?php echo PAGE_DESCRIPTION; ?>]]></description>
<?php
// Optional header info
?>
        <language><?php echo strtolower(DEFAULT_LANGUAGE); ?></language>
        <copyright><?php $thedate = date('Y'); $websitetitle = WEBSITE_TITLE; echo "Copyright {$thedate}, {$websitetitle}"; ?></copyright>
        <managingEditor><?php echo SERVER_EMAIL; ?></managingEditor>
        <webMaster><?php echo SERVER_EMAIL; ?></webMaster>
        <category><?php echo WEBSITE_TITLE; ?></category>
        <generator>WebsiteBaker Content Management System</generator>
<?php
// Get news items from database
$time = time();
//Query
    $sql='SELECT * FROM `'.TABLE_PREFIX.'mod_news_posts` '
        .'WHERE `page_id`='.(int)$page_id.' '
        .       (isset($group_id) ? 'AND `group_id`='.(int)$group_id.' ' : '')
        .       'AND `active`=1 '
        .       'AND (`published_when`  = 0 OR `published_when` <= '.$time.') '
        .       'AND (`published_until` = 0 OR `published_until` >= '.$time.') '
        .'ORDER BY posted_when DESC';

$result = $database->query($sql);

//Generating the news items
while($item = $result->fetchRow( MYSQLI_ASSOC )){
    $description = stripslashes($item["content_short"]);
    $description = OutputFilterApi('WbLink|SysvarMedia', $description);
?>
    <item>
        <title><![CDATA[<?php echo stripslashes($item["title"]); ?>]]></title>
        <description><![CDATA[<?php echo $description; ?>]]></description>
        <link><?php echo WB_URL.PAGES_DIRECTORY.$item["link"].PAGE_EXTENSION; ?></link>
        <pubDate><?PHP echo date('r', $item["published_when"]); ?></pubDate>
        <guid><?php echo WB_URL.PAGES_DIRECTORY.$item["link"].PAGE_EXTENSION; ?></guid>
    </item>
<?php } ?>
    </channel>
</rss>