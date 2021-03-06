<?php
/**
 *
 * @category        modules
 * @package         modules_news
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: view.php 67 2017-03-03 22:14:28Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/tags/WB-2.10.0/wb/modules/news/view.php $
 * @lastmodified    $Date: 2017-03-03 23:14:28 +0100 (Fr, 03. Mrz 2017) $
 *  if ( $setting_posts_per_page && $setting_posts_per_page + $position <= $i ) { break; }
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
global $post_id, $post_section, $TEXT, $MESSAGE, $MOD_NEWS;
// load module language file
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}
//overwrite php.ini on Apache servers for valid SESSION ID Separator
if (function_exists('ini_set')) {
    ini_set('arg_separator.output', '&amp;');
}

$addBracket = function ()
{
    $aList = func_get_args();
//    return preg_replace('/^(.*)$/', '/\[$1\]/s', $aList);
    return preg_replace('/^(.*)$/', '[$1]', $aList);
};
$modRel = str_replace(WB_PATH, '', __DIR__).'/';
$ModuleRel = '/modules/'.basename(__DIR__).'/';
$ModuleUrl = WB_URL.'/modules/'.basename(__DIR__).'/';
$ModulePath = WB_PATH.'/modules/'.basename(__DIR__).'/';
$sRecallAddress = WB_URL.PAGES_DIRECTORY.$GLOBALS['wb']->page['link'].PAGE_EXTENSION;

// Get user's username, display name, email, and id - needed for insertion into post info
$users = array();
$sql = 'SELECT `user_id`,`username`,`display_name`,`email` FROM `'.TABLE_PREFIX.'users`';
if (($resUsers = $database->query($sql))) {
    while ($recUser = $resUsers->fetchRow( MYSQLI_ASSOC )) {
        $users[$recUser['user_id']] = $recUser;
    }
}
// Get all groups (id, title, active, image)
$groups = array(
    0 => array(
        'group_id'  => 0,
        'title'     => '',
        'active'    => true,
        'image'     => ''
    )
);


$sql = 'SELECT `group_id`, `title`, `active` FROM `'.TABLE_PREFIX.'mod_news_groups` '
     . 'WHERE `section_id`='.(int)$section_id.' '
     . 'ORDER BY `position` ASC';
if (($query_users = $database->query($sql))) {
    while (($group = $query_users->fetchRow( MYSQLI_ASSOC ))) {
        // Insert user info into users array
        $groups[$group['group_id']] = $group;
        $sImageUrl = MEDIA_DIRECTORY.'/.news/image'.$group['group_id'].'.jpg';
        $groups[$group['group_id']]['image'] = (is_readable(WB_PATH.$sImageUrl) ? WB_URL.$sImageUrl : '');
    }
}
    // Check if we should only list posts from a certain group
    if (isset($_GET['g']) AND is_numeric($_GET['g'])) {
        $query_extra = 'AND `group_id`='.(int)$_GET['g'].' ';
    } else {
        $query_extra = '';
    }
    // Get settings
    $setting_header = $setting_post_loop = $setting_footer = $setting_posts_per_page = '';
    $sql = 'SELECT `header`, `post_loop`, `footer`, `posts_per_page` '
         . 'FROM `'.TABLE_PREFIX.'mod_news_settings` '
         . 'WHERE `section_id`='.(int)$section_id;
    if (($resSettings = $database->query($sql))) {
        if (($recSettings = $resSettings->fetchRow(MYSQL_ASSOC))) {
            foreach ($recSettings as $key=>$val) {
                ${'setting_'.$key} = $val;
            }
        }
    }
    // Get total number of posts relatet to now
// Check if we should show the main page or a post itself
    $now = $t = time();
    $sql = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'mod_news_posts` '
         . 'WHERE `section_id`='.(int)$section_id.' AND `active`=1 '
         .        'AND `title`!=\'\' '
         .        'AND (`published_when`=0 OR `published_when`<='.$now.') '
         .        'AND (`published_until`=0 OR `published_until`>='.$now.') '
         .        $query_extra;
    $total_num = intval($database->get_one($sql));
    if ( $total_num && $setting_posts_per_page ) {
        $iNumberOfPages = (int)($total_num / $setting_posts_per_page)+($total_num % $setting_posts_per_page ? 1:0 );
        $position  = intval( isset($_GET['p'] ) ? $_GET['p'] : 0 );
        $position  = abs( ( $position < $total_num) ? $position : ($iNumberOfPages*$setting_posts_per_page) );
        // Work-out if we need to add limit code to sql
        $limit_sql = ' LIMIT '.$position.', '.$setting_posts_per_page;
    } else {
        $display_previous_next_links = '';
        $position = 0;
        $next_link = '';
        $next_page_link = '';
        $previous_link = '';
        $previous_page_link = '';
        $out_of = '';
        $of = '';
        $limit_sql = '';
    }

// if(!defined('POST_ID') OR !is_numeric(POST_ID))
if (!isset($post_id) || !is_numeric($post_id)) {
/*
$setting_posts_per_page = 12/5 = 2 5 = 10
*/
    // Query posts (for this page)
    $sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_news_posts` '
         . 'WHERE `section_id`='.$section_id.' '
         .        'AND `active`=1 '
         .        'AND `title`!=\'\' '
         .        'AND (`published_when`=0 OR `published_when`<='.$now.') '
         .        'AND (`published_until`=0 OR `published_until`>='.$now.') '
         .        $query_extra
         . 'ORDER BY `position` DESC'.$limit_sql;
    $query_posts = $database->query($sql);
//    $total_num = $query_posts->numRows();
    // Create previous and next links

    if ($setting_posts_per_page != 0) {
        $spaces = str_repeat('&#160;', 25);
        if ( ($position > 0) && ($position < $total_num) ) {
            if (isset($_GET['g']) AND is_numeric($_GET['g'])) {
                $pl_prepend = '<a href="?p='.($position-$setting_posts_per_page).'&amp;g='.$_GET['g'].'">&lt;&lt; ';
            } else {
                $pl_prepend = '<a href="?p='.($position-$setting_posts_per_page).'">&lt;&lt; ';
            }
            $pl_append = '</a>';
            $previous_link = $pl_prepend.$TEXT['PREVIOUS'].$pl_append;
            $previous_page_link = $pl_prepend.$TEXT['PREVIOUS_PAGE'].$pl_append;
        } else {
            $previous_link = $spaces;
            $previous_page_link = $spaces;
        }

        if ($position + $setting_posts_per_page >= $total_num) {
            $next_link = $spaces;
            $next_page_link = $spaces;
        } else {
            if (isset($_GET['g']) AND is_numeric($_GET['g'])) {
                $nl_prepend = '<a href="?p='.($position+$setting_posts_per_page).'&amp;g='.$_GET['g'].'"> ';
            } else {
                $nl_prepend = '<a href="?p='.($position+$setting_posts_per_page).'"> ';
            }
            $nl_append = ' &gt;&gt;</a>';
            $next_link = $nl_prepend.$TEXT['NEXT'].$nl_append;
            $next_page_link = $nl_prepend.$TEXT['NEXT_PAGE'].$nl_append;
        }
        if ($position+$setting_posts_per_page > $total_num) {  //
            $num_of = $total_num;
        } else {
            $num_of = $position+$setting_posts_per_page;
        }
        if ( ($position >= 0) && ($position < $total_num) ) {
            $out_of = ($position+1).'-'.$num_of.' '.strtolower($TEXT['OUT_OF']).' '.$total_num;
            $of = ($position+1).'-'.$num_of.' '.strtolower($TEXT['OF']).' '.$total_num;
            $display_previous_next_links = $spaces;
        } else {
            $display_previous_next_links = 'none';
        }
    }
    if ($total_num=== 0) { // $num_posts
        $setting_header = '';
        $setting_post_loop = '';
        $setting_footer = '';
        $setting_posts_per_page = '';
    }

// Print header
    $aPlaceHolders = $addBracket(
        'DISPLAY_PREVIOUS_NEXT_LINKS',
        'NEXT_PAGE_LINK',
        'NEXT_LINK',
        'PREVIOUS_PAGE_LINK',
        'PREVIOUS_LINK',
        'OUT_OF',
        'OF'
    );
    if ($display_previous_next_links == 'none') {
        $aReplacements = array(
            $display_previous_next_links
        );
    } else {
        $aReplacements = array(
            $display_previous_next_links,
            $next_page_link,
            $next_link,
            $previous_page_link,
            $previous_link,
            $out_of,
            $of
        );
    }
    print (str_replace($aPlaceHolders, $aReplacements, $setting_header));
    if ($total_num > 0) // $num_posts
    {
        $sScriptUrl = $_SERVER['SCRIPT_NAME'];
        if ($query_extra != '') {
            echo ('<div class="selected-group-title">'
                 .'<a href="'.htmlspecialchars(strip_tags($sScriptUrl))
                 .'">'.PAGE_TITLE.'</a> &gt;&gt; '.$groups[$_GET['g']]['title']
                 .'</div>'.PHP_EOL
            );
        }

        $aPlaceHolders = $addBracket(
            'PAGE_TITLE',
            'GROUP_ID',
            'GROUP_TITLE',
            'GROUP_IMAGE',
            'DISPLAY_GROUP',
            'DISPLAY_IMAGE',
            'TITLE',
            'SHORT',
            'MODI_DATE',
            'MODI_TIME',
            'CREATED_DATE',
            'CREATED_TIME',
            'PUBLISHED_DATE',
            'PUBLISHED_TIME',
            'LINK',
            'SHOW_READ_MORE',
            'TEXT_READ_MORE',
            'USER_ID',
            'USERNAME',
            'DISPLAY_NAME',
            'EMAIL'
        );
        $i=0;
        while (($post = $query_posts->fetchRow( MYSQLI_ASSOC )))
        {
            ++$i;
            if (
                isset($groups[$post['group_id']]['active']) AND
                $groups[$post['group_id']]['active'] != false
            ) { // Make sure parent group is active
                $uid = $post['posted_by']; // User who last modified the post
                // Workout date and time of last modified post
                if ($post['published_when'] === '0') {
                    $post['published_when'] = time();
                }
                if ($post['published_when'] > $post['posted_when']) {
                    $post_date = date(DATE_FORMAT, $post['published_when']+TIMEZONE);
                    $post_time = date(TIME_FORMAT, $post['published_when']+TIMEZONE);
                } else {
                    $post_date = date(DATE_FORMAT, $post['posted_when']+TIMEZONE);
                    $post_time = date(TIME_FORMAT, $post['posted_when']+TIMEZONE);
                }
                $publ_date      = date(DATE_FORMAT,$post['published_when']+TIMEZONE);
                $publ_time      = date(TIME_FORMAT,$post['published_when']+TIMEZONE);
                // Work-out the post link
                $post_link      = page_link($post['link']);
                $post_link_path = str_replace(WB_URL, WB_PATH,$post_link);
                $create_date    = date(DATE_FORMAT, $post['created_when']+TIMEZONE);
                $create_time    = date(TIME_FORMAT, $post['created_when']+TIMEZONE);
                if (isset($_GET['p']) AND $position > 0) {
                    $post_link .= '?p='.$position;
                }
                if (isset($_GET['g']) AND is_numeric($_GET['g'])) {
                    if (isset($_GET['p']) AND $position > 0) {
                        $post_link .= '&amp;';
                    } else {
                        $post_link .= '?';
                    }
                    $post_link .= 'g='.$_GET['g'];
                }
                // Get group id, title, and image
                $group_id      = $post['group_id'];
                $group_title   = $groups[$group_id]['title'];
                $group_image   = $groups[$group_id]['image'];
                $display_image = ($group_image == '') ? "none" : "inherit";
                $display_group = ($group_id == 0) ? 'none' : 'inherit';

                if ($group_image != "") {
                    $group_image= "<img src='".$group_image."' alt='".$group_title."' />";
                }
                // Replace [wblink--PAGE_ID--] with real link
                $sMediaUrl = WB_URL.MEDIA_DIRECTORY;
                $short = ($post['content_short']);
                $short = (str_replace('{SYSVAR:MEDIA_REL}', $sMediaUrl, $short));
                // Replace vars with values
//                $post_long_len = mb_strlen($post['content_long']);
//                $bIsEmptyLongContent = (bool)( $post_long_len == 0);
                $bIsEmptyLongContent = !(bool)mb_strlen(
                    trim(preg_replace('/^\s*?<(p|div)>(.*)?<\/\s*?\1>$/si', '\2', $post['content_long']))
                );
                // set replacements for exchange
                $aReplacements = array(
                    PAGE_TITLE,
                    $group_id,
                    $group_title,
                    $group_image,
                    $display_group,
                    $display_image,
                    $post['title'],
                    $short,
                    $post_date,
                    $post_time,
                    $create_date,
                    $create_time,
                    $publ_date,
                    $publ_time
                );
                if (isset($users[$uid]['username']) && $users[$uid]['username'] != '')
                {
                    if ($bIsEmptyLongContent) {
                        $aReplacements[] = '#" onclick="javascript:void(0);return false;" style="cursor:no-drop;';
                        $aReplacements[] = 'hidden';
                        $aReplacements[] = '';
                        $aReplacements[] = $uid;
                        $aReplacements[] = $users[$uid]['username'];
                        $aReplacements[] = $users[$uid]['display_name'];
                        $aReplacements[] = $users[$uid]['email'];
                    } else {
                        $aReplacements[] = $post_link;
                        $aReplacements[] = 'visible';
                        $aReplacements[] = $MOD_NEWS['TEXT_READ_MORE'];
                        $aReplacements[] = $uid;
                        $aReplacements[] = $users[$uid]['username'];
                        $aReplacements[] = $users[$uid]['display_name'];
                        $aReplacements[] = $users[$uid]['email'];
                    }
                } else {
                    if ($bIsEmptyLongContent) {
                        $aReplacements[] = '#" onclick="javascript:void(0);return false;" style="cursor:no-drop;';
                        $aReplacements[] = 'hidden';
                    } else {
                        $aReplacements[] = $post_link;
                        $aReplacements[] = 'visible';
                        $aReplacements[] = $MOD_NEWS['TEXT_READ_MORE'];
                    }
                }
                print (str_replace($aPlaceHolders, $aReplacements, $setting_post_loop));
            }
//            if ( $setting_posts_per_page == $i ) { break; }
            if ( $setting_posts_per_page && $setting_posts_per_page + $position <= $i ) { break; }
        } // end while posts
    }
    // Print footer
    $aPlaceHolders = $addBracket(
        'DISPLAY_PREVIOUS_NEXT_LINKS',
        'NEXT_PAGE_LINK',
        'NEXT_LINK',
        'PREVIOUS_PAGE_LINK',
        'PREVIOUS_LINK',
        'OUT_OF',
        'OF'
    );
    if ($display_previous_next_links == 'none') {
        $aReplacements = array(
            $display_previous_next_links
        );
    } else {
        $aReplacements = array(
            $display_previous_next_links,
            $next_page_link,
            $next_link,
            $previous_page_link,
            $previous_link,
            $out_of,
            $of
        );
    }
    print (str_replace($aPlaceHolders, $aReplacements, $setting_footer));

} elseif(isset($post_id) && is_numeric($post_id)) {
    if (isset($post_section) && ($post_section == $section_id)) {
        // Get settings
        $setting_post_header = $setting_post_footer = $setting_comments_header
                             = $setting_comments_loop = $setting_comments_footer = '';
        $sql = 'SELECT `post_header`, `post_footer`, `comments_header`, `comments_loop`, `comments_footer` '
             . 'FROM `'.TABLE_PREFIX.'mod_news_settings` '
             . 'WHERE `section_id`='.(int)$section_id;
        if (($resSettings = $database->query($sql)) ) {
            if (($recSettings = $resSettings->fetchRow( MYSQLI_ASSOC ))) {
                foreach ($recSettings as $key=>$val) {
                    ${'setting_'.$key} = $val;
                }
            }
        }
        // Get page info
        $sql = 'SELECT `link` FROM `'.TABLE_PREFIX.'pages` '
             . 'WHERE `page_id`='.PAGE_ID;
        $query_page = $database->query($sql);
        if ($query_page->numRows() > 0) {
            $page = $query_page->fetchRow( MYSQLI_ASSOC );
            $page_link = page_link($page['link']);
            if (isset($_GET['p']) AND $position > 0) {
                $page_link .= '?p='.$_GET['p'];
            }
            if (isset($_GET['g']) AND is_numeric($_GET['g'])) {
                if (isset($_GET['p']) AND $position > 0) {
                    $page_link .= '&amp;';
                } else {
                    $page_link .= '?';
                }
                $page_link .= 'g='.$_GET['g'];
            }
        } else {
            exit($MESSAGE['PAGES_NOT_FOUND']);
        }
        // Get post info
        $t = time();
        $sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_news_posts` '
             . 'WHERE `post_id`='.$post_id.' AND active=1 '
             .        'AND (`published_when`=0 OR `published_when`<='.$t.') '
             .        'AND (`published_until`=0 OR `published_until`>='.$t.')';
        $query_post = $database->query($sql);
        if ($post = $query_post->fetchRow( MYSQLI_ASSOC )) {
            if (isset($groups[$post['group_id']]['active'])
                AND $groups[$post['group_id']]['active'] != false
            ) { // Make sure parent group is active
                $uid = $post['posted_by']; // User who last modified the post
                // Workout date and time of last modified post
                if ($post['published_when'] === '0') {
                    $post['published_when'] = time();
                }
                if ($post['published_when'] > $post['posted_when']) {
                    $post_date = date(DATE_FORMAT, $post['published_when']+TIMEZONE);
                    $post_time = date(TIME_FORMAT, $post['published_when']+TIMEZONE);
                } else {
                    $post_date = date(DATE_FORMAT, $post['posted_when']+TIMEZONE);
                    $post_time = date(TIME_FORMAT, $post['posted_when']+TIMEZONE);
                }
                $publ_date      = date(DATE_FORMAT,$post['published_when']+TIMEZONE);
                $publ_time      = date(TIME_FORMAT,$post['published_when']+TIMEZONE);
                // Work-out the post link
                $post_link      = page_link($post['link']);
                $post_link_path = str_replace(WB_URL, WB_PATH,$post_link);
                $create_date    = date(DATE_FORMAT, $post['created_when']+TIMEZONE);
                $create_time    = date(TIME_FORMAT, $post['created_when']+TIMEZONE);
                // Get group id, title, and image
                $group_id       = $post['group_id'];
                $group_title    = $groups[$group_id]['title'];
                $group_image    = $groups[$group_id]['image'];
                $display_image  = ($group_image == '') ? "none" : "inherit";
                $display_group  = ($group_id == 0) ? 'none' : 'inherit';
                $sMediaUrl = WB_URL.MEDIA_DIRECTORY;
                $post_short = ($post['content_short']);
                $post_short = (str_replace('{SYSVAR:MEDIA_REL}', $sMediaUrl, $post_short));
                if ($group_image != "") $group_image= "<img src='".$group_image."' alt='".$group_title."' />";

                $aPlaceHolders = $addBracket(
                    'PAGE_TITLE',
                    'GROUP_ID',
                    'GROUP_TITLE',
                    'GROUP_IMAGE',
                    'DISPLAY_GROUP',
                    'DISPLAY_IMAGE',
                    'TITLE',
                    'SHORT',
                    'BACK',
                    'TEXT_BACK',
                    'TEXT_LAST_CHANGED',
                    'MODI_DATE',
                    'TEXT_AT',
                    'MODI_TIME',
                    'CREATED_DATE',
                    'CREATED_TIME',
                    'PUBLISHED_DATE',
                    'PUBLISHED_TIME',
                    'TEXT_POSTED_BY',
                    'TEXT_ON',
                    'USER_ID',
                    'USERNAME',
                    'DISPLAY_NAME',
                    'EMAIL'
                );
                $aReplacements = array(
                    PAGE_TITLE,
                    $group_id,
                    $group_title,
                    $group_image,
                    $display_group,
                    $display_image,
                    $post['title'],
                    $post_short,
                    $page_link,
                    $MOD_NEWS['TEXT_BACK'],
                    $MOD_NEWS['TEXT_LAST_CHANGED'],
                    $post_date,
                    $MOD_NEWS['TEXT_AT'],
                    $post_time,
                    $create_date,
                    $create_time,
                    $publ_date,
                    $publ_time,
                    $MOD_NEWS['TEXT_POSTED_BY'],
                    $MOD_NEWS['TEXT_ON']
                );
                if (isset($users[$uid]['username']) AND $users[$uid]['username'] != '') {
                    $aReplacements[] = $uid;
                    $aReplacements[] = $users[$uid]['username'];
                    $aReplacements[] = $users[$uid]['display_name'];
                    $aReplacements[] = $users[$uid]['email'];
                }
                $sMediaUrl = WB_URL.MEDIA_DIRECTORY;
                $post_long = ($post['content_long'] != '') ? $post['content_long'] : $post['content_short'];
                $post_long = (str_replace('{SYSVAR:MEDIA_REL}', $sMediaUrl, $post_long));
                print (str_replace($aPlaceHolders, $aReplacements, $setting_post_header));
                print $post_long;
                print (str_replace($aPlaceHolders, $aReplacements, $setting_post_footer));
            }
        } else {
                $aPlaceHolders = $addBracket(
                    'BACK',
                    'TEXT_BACK',
                    'TEXT_LAST_CHANGED',
                    'TEXT_AT',
                    'MODI_DATE',
                    'MODI_TIME'
                );
                $aReplacements = array(
                    $page_link,
                    $MOD_NEWS['TEXT_BACK'],
                    $MESSAGE['FRONTEND_SORRY_NO_ACTIVE_SECTIONS'],
                    '',
                    ''
                );
                print (str_replace($aPlaceHolders, $aReplacements, $setting_post_footer));

//                $wb->print_error($MESSAGE['FRONTEND_SORRY_NO_ACTIVE_SECTIONS'], $sRecallAddress, false);
        }
        // Show comments section if we have to
        if (($post['commenting'] == 'private' AND isset($wb) AND $wb->is_authenticated() == true)
            OR $post['commenting'] == 'public'
        ) {
            // Print comments header
            $aPlaceHolders = $addBracket(
                'ADD_COMMENT_URL',
                'TEXT_COMMENTS'
            );
            $aReplacements = array(
                WB_URL.'/modules/news/comment.php?post_id='.$post_id.'&amp;section_id='.$section_id,
                $MOD_NEWS['TEXT_COMMENTS']
            );
            print (str_replace($aPlaceHolders, $aReplacements, $setting_comments_header));
            // Query for comments
            $iNumberOfComments = 0;
            $aPlaceHolders = $addBracket(
                'COMMENT',
                'TITLE',
                'TEXT_ON',
                'DATE',
                'TEXT_AT',
                'TIME',
                'TEXT_BY',
                'USER_ID',
                'USERNAME',
                'DISPLAY_NAME',
                'EMAIL'
            );
            $sql = 'SELECT `title`, `comment`, `commented_when`, `commented_by` '
                 . 'FROM `'.TABLE_PREFIX.'mod_news_comments` '
                 . 'WHERE `post_id`='.$post_id.' '
                 . 'ORDER BY `commented_when` ASC';

            if (($query_comments = $database->query($sql))) {
                while (($comment = $query_comments->fetchRow( MYSQLI_ASSOC ))) {
                    $iNumberOfComments++;
                    // Display Comments without slashes, but with new-line characters
                    $comment['comment'] = nl2br($wb->strip_slashes($comment['comment']));
                    $comment['title'] = $wb->strip_slashes($comment['title']);
                    // Print comments loop
                    $commented_date = date(DATE_FORMAT, $comment['commented_when']+TIMEZONE);
                    $commented_time = date(TIME_FORMAT, $comment['commented_when']+TIMEZONE);
                    $uid = $comment['commented_by'];
                    $aReplacements = array(
                        $comment['comment'],
                        $comment['title'],
                        $MOD_NEWS['TEXT_ON'],
                        $commented_date,
                        $MOD_NEWS['TEXT_AT'],
                        $commented_time,
                        $MOD_NEWS['TEXT_BY']
                    );
                    if (isset($users[$uid]['username']) AND $users[$uid]['username'] != '') {
                        $aReplacements[] = $uid;
                        $aReplacements[] = $users[$uid]['username'];
                        $aReplacements[] = $users[$uid]['display_name'];
                        $aReplacements[] = $users[$uid]['email'];
                    } else {
                        $aReplacements[] = '0';
                        $aReplacements[] = strtolower($TEXT['UNKNOWN']);
                        $aReplacements[] = $TEXT['UNKNOWN'];
                    }
                    print (str_replace($aPlaceHolders, $aReplacements, $setting_comments_loop));
                }
            }
            if (! $iNumberOfComments) {
                // Say no comments found
                $content = '';
                $aReplacements = array(
                    $MOD_NEWS['NO_COMMENT_FOUND']
                );
                print (str_replace($aPlaceHolders, $aReplacements, $setting_comments_loop));
            }
            // Print comments footer
            $aPlaceHolders = $addBracket(
                'ADD_COMMENT_URL',
                'TEXT_ADD_COMMENT',
                'TEXT_COMMENTS'
            );
            $aReplacements = array(
                WB_URL.'/modules/news/comment.php?post_id='.$post_id.'&amp;section_id='.$section_id.'&amp;p='.$position,
                $MOD_NEWS['TEXT_ADD_COMMENT'],
                $MOD_NEWS['TEXT_COMMENTS']
            );
            print (str_replace($aPlaceHolders, $aReplacements, $setting_comments_footer));
        }
        if (ENABLED_ASP) {
            $_SESSION['comes_from_view'] = $post_id;
            $_SESSION['comes_from_view_time'] = time();
        }
    }
}
unset($addBracket);