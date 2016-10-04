<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       Website Baker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: comment_page.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/comment_page.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
}
// check if module language file exists for the language set by the user (e.g. DE, EN)
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}

$sRecallAddress = WB_URL.PAGES_DIRECTORY.$GLOBALS['wb']->page['link'].PAGE_EXTENSION;

require_once(WB_PATH.'/include/captcha/captcha.php');
// Get comments page template details from db
$query_settings = $database->query(
"SELECT `comments_page`, `use_captcha`, `commenting` FROM `".TABLE_PREFIX."mod_news_settings` WHERE `section_id` = '".SECTION_ID."'"
);
if($query_settings->numRows() == 0)
{
    header("Location: ".$sRecallAddress."");
    exit( 0 );
}
else
{
    $settings = $query_settings->fetchRow( MYSQLI_ASSOC );

    // Print comments page
    $vars = array('[POST_TITLE]','[TEXT_COMMENT]');
    $values = array(POST_TITLE, $MOD_NEWS['TEXT_COMMENT']);
    echo str_replace($vars, $values, ($settings['comments_page']));
    if( isset($_SESSION['message']) ){
       echo '<p class="warning">'.implode('<br />',$_SESSION['message']).'</p>';
       unset($_SESSION['message']);
    }

?>
    <form id="news-wrapper" name="comment" action="<?php echo WB_URL.'/modules/'.basename(__DIR__).'/submit_comment.php' ?>" method="post">
      <input type="hidden" name="page_id" value="<?php echo PAGE_ID ;?>" />
      <input type="hidden" name="section_id" value="<?php echo SECTION_ID ;?>" />
      <input type="hidden" name="post_id" value="<?php echo POST_ID ;?>" />
      <input type="hidden" name="redirect" value="<?php echo $sRecallAddress ;?>" />
      <?php echo $wb->getFTAN(); ?>
    <?php if(ENABLED_ASP) { // add some honeypot-fields
    ?>
    <input type="hidden" name="submitted_when" value="<?php $t=time(); echo $t; $_SESSION['submitted_when']=$t; ?>" />
    <p class="nixhier">
    email address:
    <label for="email">Leave this field email blank:</label>
    <input id="email" name="email" size="60" value="" /><br />
    Homepage:
    <label for="homepage">Leave this field homepage blank:</label>
    <input id="homepage" name="homepage" size="60" value="" /><br />
    URL:
    <label for="url">Leave this field url blank:</label>
    <input id="url" name="url" size="60" value="" /><br />
    Comment:
    <label for="comment">Leave this field comment blank:</label>
    <input id="comment" name="comment" size="60" value="" /><br />
    </p>
    <?php }
    echo $TEXT['TITLE']; ?>:
    <br />
    <input type="text" name="title" maxlength="255" style="width: 90%;"<?php if(isset($_SESSION['comment_title'])) { echo ' value="'.$_SESSION['comment_title'].'"'; unset($_SESSION['comment_title']); } ?> />
    <br /><br />
    <?php echo $TEXT['COMMENT']; 
    ?>:
    <br />
    <?php if(ENABLED_ASP) { ?>
        <textarea name="comment_<?php echo date('W'); ?>" rows="10" cols="1" style="width: 90%; height: 150px;"><?php if(isset($_SESSION['comment_body'])) { echo $_SESSION['comment_body']; unset($_SESSION['comment_body']); } ?></textarea>
    <?php } else { ?>
        <textarea name="comment" rows="10" cols="1" style="width: 90%; height: 150px;"><?php if(isset($_SESSION['comment_body'])) { echo $_SESSION['comment_body']; unset($_SESSION['comment_body']); } ?></textarea>
    <?php } ?>
    <br /><br />
    <?php
    if(isset($_SESSION['captcha_error'])) {
        echo '<font color="#FF0000">'.$_SESSION['captcha_error'].'</font><br />';
        $_SESSION['captcha_retry_news'] = true;
    }
    // Captcha
    if($settings['use_captcha']) {
    ?>
    <table>
    <tr>
        <td><?php echo $TEXT['VERIFICATION']; ?>:</td>
        <td><?php call_captcha(); ?></td>
    </tr>
    </table>
    <?php
    if(isset($_SESSION['captcha_error'])) {
        unset($_SESSION['captcha_error']);
        ?><script>document.comment.captcha.focus();</script><?php
    }?>
    <?php
    }
    ?>
    <table class="news-table">
    <tr>
        <td>
            <input type="submit" name="submit" value="<?php echo $MOD_NEWS['TEXT_ADD_COMMENT']; ?>" />
        </td>
        <td>
            <input type="submit" value="<?php echo $TEXT['CANCEL']; ?>" name="cancel"  />
        </td>
    </tr>
    </table>
    </form>
    <?php
}
