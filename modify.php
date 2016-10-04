<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://www.websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: modify.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/modify.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
//overwrite php.ini on Apache servers for valid SESSION ID Separator
if(function_exists('ini_set')) {
    ini_set('arg_separator.output', '&amp;');
}

$sql = 'DELETE FROM `'.TABLE_PREFIX.'mod_news_posts`  WHERE `section_id` = 0 OR title=\'\'';
$database->query($sql);

$sql = 'DELETE FROM `'.TABLE_PREFIX.'mod_news_groups`  WHERE `section_id` = 0 OR title=\'\'';
$database->query($sql);

$sModulName = basename(__DIR__);
$ModuleRel = '/modules/'.basename(__DIR__).'/';
$ModuleUrl = WB_URL.'/modules/'.basename(__DIR__).'/';
$ModulePath = WB_PATH.'/modules/'.basename(__DIR__).'/';

$FTAN = $admin->getFTAN('');
$sFtan = $FTAN['name'].'='.$FTAN['value'];
// load module language file
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}

if( !function_exists( 'make_dir' ) )  {  require(WB_PATH.'/framework/functions.php');  }

?><table style="width: 100%;">
<?php  ?>
<tbody>
<tr style="width: 100%;">
    <td>
        <form action="<?php echo WB_URL; ?>/modules/news/add_post.php" method="get" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="<?php echo $TEXT['ADD'].' '.$TEXT['POST']; ?>" style="width: 100%;" />
        </form>
    </td>
    <td>
        <form action="<?php echo WB_URL; ?>/modules/news/add_group.php" method="get" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="<?php echo $TEXT['ADD'].' '.$TEXT['GROUP']; ?>" style="width: 100%;" />
        </form>
    </td>
    <td >
        <form action="<?php echo WB_URL; ?>/modules/news/modify_settings.php" method="get" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="<?php echo $TEXT['SETTINGS']; ?>" style="width: 100%;" />
        </form>
    </td>
<?php if( @DEBUG && $admin->ami_group_member('1') ) {  ?>
    <td >
        <form action="<?php echo WB_URL; ?>/modules/news/reorgPosition.php" method="get" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="Reorg Position" style="width: 100%;" />
        </form>
    </td>
<?php } ?>
</tr>
</tbody>
</table>

<br />

<h2><?php echo $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$TEXT['POST']; ?></h2>

<?php

// Loop through existing posts
$query_posts = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_news_posts` WHERE section_id = '$section_id' ORDER BY position DESC");
if($query_posts->numRows() > 0) {
    $num_posts = $query_posts->numRows();
?><div class="jsadmin hide"></div>
    <table class="news-post">
        <thead>
            <tr >
                <th style=" width: 3%;">&nbsp;</th>
                <th style="padding-left: 5px; text-align: left; width: 60%;"><?php print $TEXT['POST']; ?></th>
                <th style=" text-align: left; width: 15%;"><?php print $TEXT['GROUP']; ?></th>
                <th style="padding-right: 5px; text-align: left; width: 5%;"><?php print $TEXT['COMMENTS']; ?></th>
                <th style=" text-align: left; width: 3%;"><?php print $TEXT['ACTIVE']; ?></th>
                <th style=" text-align: left; width: 3%;"><?php echo '';  ?></th>
                <th style="width: 12%;" colspan="3"><?php echo $TEXT['ACTIONS']; ?></th>
                <th style="padding-right: 5px; width: 3%;">Pos</th>

            </tr>
        </thead>
        <tbody>
    <?php
    while($post = $query_posts->fetchRow( MYSQLI_ASSOC )) {
        $pid = $admin->getIDKEY($post['post_id']);
        $sid = $admin->getIDKEY($section_id);
        ?>
        <tr class=" sectionrow">
            <td style="text-align: center;">
                <a href="<?php echo WB_URL; ?>/modules/news/modify_post.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/modify_16.png"  alt="Modify - " />
                </a>
            </td>
            <td style="padding-left: 5px; ">
                <a href="<?php echo WB_URL; ?>/modules/news/modify_post.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>">
                    <?php echo ($post['title']); ?>
                </a>
            </td>
            <td >
                <?php
                // Get group title
                $query_title = $database->query("SELECT title FROM ".TABLE_PREFIX."mod_news_groups WHERE group_id = '".$post['group_id']."'");
                if($query_title->numRows() > 0) {
                    $fetch_title = $query_title->fetchRow( MYSQLI_ASSOC );
                    echo ($fetch_title['title']);
                } else {
                    echo $TEXT['NONE'];
                }
                ?>
            </td>
            <td >
                <?php
                // Get number of comments
                $query_title = $database->query("SELECT title FROM ".TABLE_PREFIX."mod_news_comments WHERE post_id = '".$post['post_id']."'");
                echo $query_title->numRows();
                ?>
            </td>
            <td >
                <?php  if($post['active'] == 1) { echo $TEXT['YES']; } else { echo $TEXT['NO']; } ?>
            </td>
            <td >
            <?php
            $start = $post['published_when'];
            $end = $post['published_until'];
            $t = time();
            $icon = '';
            if($start<=$t && $end==0)
                $icon=THEME_URL.'/images/noclock_16.png';
            elseif(($start<=$t || $start==0) && $end>=$t)
                $icon=THEME_URL.'/images/clock_16.png';
            else
                $icon=THEME_URL.'/images/clock_red_16.png';
            ?>
            <a href="<?php echo WB_URL; ?>/modules/news/modify_post.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
                <img src="<?php echo $icon; ?>" alt="" />
            </a>
            </td>
            <td style="text-align: center;">
            <?php if($post['position'] != 1 ) { ?>
                <a href="<?php echo WB_URL; ?>/modules/news/move_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/down_16.png" alt="^" />
                </a>
            <?php } ?>
            </td>
            <td style="text-align: center;">
            <?php if($post['position'] != $num_posts ) { ?>
                <a href="<?php echo WB_URL; ?>/modules/news/move_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/up_16.png" alt="v" />
                </a>
            <?php } ?>
            </td>
            <td style="text-align: center;">
                <a href="javascript: confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/news/delete_post.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/delete_16.png" alt="X" />
                </a>
            </td>
            <td style="text-align: right;"><?php echo (defined('DEBUG')&& DEBUG ? $post['position']:''); ?></td>
        </tr>
        <?php
    }
    ?>
        </tbody>
    </table>
    <?php
} else {
    echo $TEXT['NONE_FOUND'];
}

?>

<h2><?php echo $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$TEXT['GROUP']; ?></h2>

<?php

// Loop through existing groups
$query_groups = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_news_groups` WHERE section_id = '$section_id' ORDER BY position");
if($query_groups->numRows() > 0) {
    $num_groups = $query_groups->numRows();
    ?>
    <table class="news-group">
        <thead>
            <tr >
                <th style="padding-left: 5px; width: 3%;">&nbsp;</th>
                <th style="padding-left: 5px; text-align: left; width: 65%;"><?php print $TEXT['GROUP']; ?></th>
                <th style="width: 20%;"> </th>
                <th style="width: 10%;" > </th>
                <th style="width: 3%;"><?php print $TEXT['ACTIVE']; ?></th>
                <th style="width: 3%;" > </th>
                <th style="width: 10%;"  colspan="3"><?php echo $TEXT['ACTIONS']; ?></th>
                <th style="width: 3%;padding-right: 5px;">Pos</th>
            </tr>
        </thead>
        <tbody>
    <?php
    while($group = $query_groups->fetchRow( MYSQLI_ASSOC )) {
        $gid = $admin->getIDKEY($group['group_id']);
        ?>
        <tr>
            <td style="padding-left: 5px; text-align: center;">
                <a href="<?php echo WB_URL; ?>/modules/news/modify_group.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;group_id=<?php echo $gid; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/modify_16.png" alt="Modify - " />
                </a>
            </td>
            <td colspan="3" style="padding-left: 5px;">
                <a href="<?php echo WB_URL; ?>/modules/news/modify_group.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;group_id=<?php echo $gid; ?>">
                    <?php echo $group['title'].' ('.$group['group_id'].')'; ?>
                </a>
            </td>
            <td  style="text-align: center;">
                <?php if($group['active'] == 1) { echo $TEXT['YES']; } else { echo $TEXT['NO']; } ?>
            </td>
            <td  style="text-align: right;"> </td>
            <td  style="text-align: center;">
            <?php if($group['position'] != 1 ) { ?>
                <a href="<?php echo WB_URL; ?>/modules/news/move_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;group_id=<?php echo $gid; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/up_16.png" alt="^" />
                </a>
            <?php } ?>
            </td>
            <td  style="text-align: center;">
            <?php if($group['position'] != $num_groups ) { ?>
                <a href="<?php echo WB_URL; ?>/modules/news/move_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;group_id=<?php echo $gid; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/down_16.png" alt="v" />
                </a>
            <?php } ?>
            </td>
            <td  style="text-align: center;">
                <a href="javascript:confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/news/delete_group.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;group_id=<?php echo $gid; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/delete_16.png" alt="X" />
                </a>
            </td>
            <td  style="text-align: right;"><?php echo (defined('DEBUG')&& DEBUG ?$group['position']:''); ?></td>
        </tr>
        <?php
    }
    ?>
        </tbody>
    </table>
    <?php
} else {
    echo $TEXT['NONE_FOUND'];
}
