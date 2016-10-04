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
 * @version         $Id: submit_comment.php 1634 2012-03-09 02:20:16Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/news/submit_comment.php $
 * @lastmodified    $Date: 2012-03-09 03:20:16 +0100 (Fr, 09. Mrz 2012) $
 *
 */
// Include config file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('wb')) { require(WB_PATH.'/framework/class.wb.php');  }
// Create new frontend object
if (!isset($wb) || !($wb instanceof wb)) { $wb = new wb(); }

    $requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
    $aRequestVars  = (isset(${$requestMethod}) ? ${$requestMethod} : null);
// Get page id
    $page_id = intval(isset($aRequestVars['page_id'])) ? $aRequestVars['page_id'] : (isset($page_id) ? intval($page_id) : 0);
// Get post_id
    $post_id = (intval(isset($aRequestVars['post_id'])) ? $aRequestVars['post_id'] : (isset($post_id) ? intval($post_id) : 0));
// Get section id if there is one
    $section_id = intval(isset($aRequestVars['section_id'])) ? $aRequestVars['section_id'] : (isset($section_id) ? intval($section_id) : 0);
     $_SESSION['message']=null;
    if (!$wb->checkFTAN())
    {
        $_SESSION['message'][] = ($MESSAGE['GENERIC_SECURITY_ACCESS']);
        header("Location: ".WB_URL."/modules/news/comment.php?post_id=".(int)$aRequestVars['post_id']."&section_id=".(int)$aRequestVars['section_id']."" ) ;
        exit( 0 );
    }
    $position       = (isset($aRequestVars['p']) ? $aRequestVars['p'] : '' );
    $comment        = (isset($aRequestVars['comment']) ? $aRequestVars['comment'] : '' );
    $comment_date   = (isset($aRequestVars['comment_'.date('W')]) ? $aRequestVars['comment_'.date('W')] : '' );
    $sRecallAddress = (isset($aRequestVars['redirect']) ? $aRequestVars['redirect'] : WB_URL );
    $action = intval(isset($aRequestVars['cancel']) ? true : false );
// Check if we should show the form or add a comment
    if (
        $page_id && $section_id  && $post_id  && !$action
        && ( ( ENABLED_ASP && $comment_date != '')
        || ( !ENABLED_ASP && $comment != '' ) ) 
      ){
        if(ENABLED_ASP){
            $comment = $_POST['comment_'.date('W')];
        } else {
            $comment = $_POST['comment'];
        }
        $comment = strip_tags($comment);
        $title   = strip_tags($_POST['title']);
        // do not allow droplets in user input!
        $title   = $wb->StripCodeFromText($title);
        $comment = $wb->StripCodeFromText($comment);
        // Check captcha
        $sql  = 'SELECT `use_captcha` FROM `'.TABLE_PREFIX.'mod_news_settings` '
              . 'WHERE `section_id` ='.$section_id;
        if( $use_captcha = $database->get_one( $sql ) ) {
            $t=time();
            // Advanced Spam Protection
            if(ENABLED_ASP && ( ($_SESSION['session_started']+ASP_SESSION_MIN_AGE > $t)  // session too young
                OR (!isset($_SESSION['comes_from_view']))// user doesn't come from view.php
                OR (!isset($_SESSION['comes_from_view_time']) OR $_SESSION['comes_from_view_time'] > $t-ASP_VIEW_MIN_AGE) // user is too fast
                OR (!isset($_SESSION['submitted_when']) OR !isset($aRequestVars['submitted_when'])) // faked form
                OR ($_SESSION['submitted_when'] != $aRequestVars['submitted_when']) // faked form
                OR ($_SESSION['submitted_when'] > $t-ASP_INPUT_MIN_AGE && !isset($_SESSION['captcha_retry_news'])) // user too fast
                OR ($_SESSION['submitted_when'] < $t-43200) // form older than 12h
                OR ($aRequestVars['email'] OR $aRequestVars['url'] OR $aRequestVars['homepage'] OR $aRequestVars['comment']) /* honeypot-fields */ ) )
            {
                header("Location: ".$sRecallAddress."?p=".$position);
                exit;
            }
            if(ENABLED_ASP)
            {
                if(isset($_SESSION['captcha_retry_news']))
                {
                  unset($_SESSION['captcha_retry_news']);
                }
            }
            if( $use_captcha )
            {
                $search = array('{SERVER_EMAIL}');
                $replace = array( SERVER_EMAIL,);
                $MESSAGE['MOD_FORM_INCORRECT_CAPTCHA'] = str_replace($search,$replace,$MESSAGE['MOD_FORM_INCORRECT_CAPTCHA']);
                if(isset($_POST['captcha']) && $_POST['captcha'] != '')
                {
                    // Check for a mismatch
                    if(!isset($_POST['captcha']) OR !isset($_SESSION['captcha']) OR $_POST['captcha'] != $_SESSION['captcha'])
                    {
                        $_SESSION['captcha_error'] = $MESSAGE['MOD_FORM_INCORRECT_CAPTCHA'];
                        $_SESSION['comment_title'] = $title;
                        $_SESSION['comment_body'] = $comment;
                        header("Location: ".WB_URL.'/modules/news/comment.php?post_id='.$post_id.'&section_id='.$section_id.'&amp;p='.$position );
                        exit;
                    }
                }
                else
                {
                    $_SESSION['captcha_error'] = $MESSAGE['MOD_FORM_INCORRECT_CAPTCHA'];
                    $_SESSION['comment_title'] = $title;
                    $_SESSION['comment_body'] = $comment;
                    header("Location: ".WB_URL.'/modules/news/comment.php?post_id='.$post_id.'&section_id='.$section_id.'&amp;p='.$position );
                    exit;
                }
            }
        }
    
        if(isset($_SESSION['captcha'])) { unset($_SESSION['captcha']); }
    
        if(ENABLED_ASP)
        {
            unset($_SESSION['comes_from_view']);
            unset($_SESSION['comes_from_view_time']);
            unset($_SESSION['submitted_when']);
        }
        // Insert the comment into db
        $commented_when = time();
        if($wb->is_authenticated() == true)
        {
            $commented_by = $wb->get_user_id();
        }
        else
        {
            $commented_by = 0;
        }
        $sql  = 'INSERT INTO `'.TABLE_PREFIX.'mod_news_comments` SET '
              . '`section_id` = '.intval($section_id).', '
              . '`page_id` = '.intval($page_id).', '
              . '`post_id` = '.intval($post_id).', '
              . '`title` = \''.$database->escapeString($title).'\', '
              . '`comment` = \''.$database->escapeString($comment).'\', '
              . '`commented_when` = '.intval($commented_when).', '
              . '`commented_by` = '.intval($commented_by).' '
              .'';
        $query = $database->query( $sql );
    
    // Get page link
        $sql = 'SELECT `link` FROM `'.TABLE_PREFIX.'mod_news_posts` WHERE `post_id` = '.(int)$post_id;
        $query_page = $database->query( $sql );
        $page = $query_page->fetchRow( MYSQLI_ASSOC );
        header('Location: '.$wb->page_link($page['link']).'?post_id='.$post_id.'' );
        exit;
    }else{
    if( $post_id && $section_id && !$action )
    {
        header("Location: ".WB_URL.'/modules/news/comment.php?post_id='.$post_id.'&section_id='.$section_id );
        exit( 0 );
    }
    else
    {
        header("Location: ".$sRecallAddress);
        exit;
    }
}
