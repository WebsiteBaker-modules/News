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
 * @version         $Id: delete.php 67 2017-03-03 22:14:28Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/tags/WB-2.10.0/wb/modules/news/delete.php $
 * @lastmodified    $Date: 2017-03-03 23:14:28 +0100 (Fr, 03. Mrz 2017) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
function mod_news_delete($database, $page_id, $section_id)
{

    //get and remove all php files created for the news section
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_news_posts` '
          . 'WHERE `section_id` = '.$database->escapeString($section_id);
    $oPosts = $database->query($sql);
    if($oPosts->numRows() > 0) {
        while($aPost = $oPosts->fetchRow(MYSQLI_ASSOC)) {
            if(is_writable(WB_PATH.PAGES_DIRECTORY.$aPost['link'].PAGE_EXTENSION)) {
            unlink(WB_PATH.PAGES_DIRECTORY.$aPost['link'].PAGE_EXTENSION);
            }
        }
    }

    //check to see if any other sections are part of the news page, if only 1 news is there delete it
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'sections` '
          . 'WHERE `page_id` = '.$database->escapeString($page_id);
    $oSection = $database->query($sql);
    if($oSection->numRows() == 1) {
        $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'pages` '
              . 'WHERE `page_id` = '.$database->escapeString($page_id);
        $oPages = $database->query($sql);
        $link = $oPages->fetchRow(MYSQLI_ASSOC);
        if(is_writable(WB_PATH.PAGES_DIRECTORY.$link['link'].PAGE_EXTENSION)) {
            unlink(WB_PATH.PAGES_DIRECTORY.$link['link'].PAGE_EXTENSION);
        }
    }

    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_news_groups` '
          . 'WHERE `section_id` = '.$database->escapeString($section_id);
    $database->query($sql);
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_news_posts` '
          . 'WHERE `section_id` = '.$database->escapeString($section_id);
    $database->query($sql);
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_news_comments` '
          . 'WHERE `section_id` = '.$database->escapeString($section_id);
    $database->query($sql);
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_news_settings` '
          . 'WHERE `section_id` = '.$database->escapeString($section_id);
    $database->query($sql);
}

if( !function_exists('mod_news_delete') ){ mod_news_delete($database, $page_id, $section_id );}

