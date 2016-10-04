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
 * @version         $Id: add.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/add.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
} else {
    $header = '<table class="loop-header">'."\n";
    $post_loop = '<tr class="post-top">
    <td class="post-title"><a href="[LINK]">[TITLE]</a></td>
    <td class="post-date">[PUBLISHED_DATE], [PUBLISHED_TIME]</td>
    </tr>
    <tr>
    <td class="post-short" colspan="2">
    [SHORT]
    <h3 style="visibility:[SHOW_READ_MORE];"><a href="[LINK]">[TEXT_READ_MORE]</a></h3>
    </td>
    </tr>';
    $footer = '</table>
    <table  class="page-header" style="display: [DISPLAY_PREVIOUS_NEXT_LINKS]">
    <tr>
    <td class="page-left">[PREVIOUS_PAGE_LINK]</td>
    <td class="page-center">[OF]</td>
    <td class="page-right">[NEXT_PAGE_LINK]</td>
    </tr>
    </table>';
    $post_header = ('<table  class="post-header">
    <tr>
    <td><h1>[TITLE]</h1></td>
    <td rowspan="3" style="display: [DISPLAY_IMAGE]">[GROUP_IMAGE]</td>
    </tr>
    <tr>
    <td class="public-info"><b>[TEXT_POSTED_BY] [DISPLAY_NAME] [TEXT_ON] [PUBLISHED_DATE]</b></td>
    </tr>
    <tr style="display: [DISPLAY_GROUP]">
    <td class="group-page"><a href="[BACK]">[PAGE_TITLE]</a> &gt;&gt; <a href="[BACK]?g=[GROUP_ID]">[GROUP_TITLE]</a></td>
    </tr>
    </table>');
    $post_footer = '<p>[TEXT_LAST_CHANGED]: [MODI_DATE] [TEXT_AT] [MODI_TIME]</p>
    <a href="[BACK]">[TEXT_BACK]</a>';
    $comments_header = ('<br /><br />
    <h2>[TEXT_COMMENTS]</h2>
    <table cellpadding="2" cellspacing="0" class="comment-header">');
    $comments_loop = ('<tr>
    <td class="comment_title">[TITLE]</td>
    <td class="comment_info">[TEXT_BY] [DISPLAY_NAME] [TEXT_ON] [DATE] [TEXT_AT] [TIME]</td>
    </tr>
    <tr>
    <td colspan="2" class="comment-text">[COMMENT]</td>
    </tr>');
    $comments_footer = '</table>
    <br /><a href="[ADD_COMMENT_URL]">[TEXT_ADD_COMMENT]</a>';
    $comments_page = '<h2>[TEXT_COMMENT]</h2>
    <h2>[POST_TITLE]</h2>
    <br />';
    $commenting = 'none';
    $use_captcha = true;
    $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_news_settings` SET '
         . '`section_id`='.$database->escapeString($section_id).', '
         . '`page_id`='.$database->escapeString($page_id).', '
         . '`header`=\''.$database->escapeString($header).'\', '
         . '`post_loop`=\''.$database->escapeString($post_loop).'\', '
         . '`footer`=\''.$database->escapeString($footer).'\', '
         . '`posts_per_page`=5, '
         . '`post_header`=\''.$database->escapeString($post_header).'\', '
         . '`post_footer`=\''.$database->escapeString($post_footer).'\', '
         . '`comments_header`=\''.$database->escapeString($comments_header).'\', '
         . '`comments_loop`=\''.$database->escapeString($comments_loop).'\', '
         . '`comments_footer`=\''.$database->escapeString($comments_footer).'\', '
         . '`comments_page`=\''.$database->escapeString($comments_page).'\', '
         . '`commenting`=\''.$database->escapeString($commenting).'\', '
         . '`resize`=0, '
         . '`use_captcha`='.$database->escapeString($use_captcha).' '
         . ''.'';
    $database->query( $sql );
    }
