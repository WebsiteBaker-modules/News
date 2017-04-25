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
 * @version         $Id: modify.php 67 2017-03-03 22:14:28Z manu $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2.10/tags/WB-2.10.0/wb/modules/news/modify.php $
 * @lastmodified    $Date: 2017-03-03 23:14:28 +0100 (Fr, 03. Mrz 2017) $
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

$sAppUrl = WB_URL;
$sModulName = basename(__DIR__);
$ModuleRel = '/modules/'.basename(__DIR__).'/';
$sAddonUrl = $ModuleUrl = WB_URL.'/modules/'.basename(__DIR__).'/';
$ModulePath = WB_PATH.'/modules/'.basename(__DIR__).'/';
$sAddonThemeUrl = $sAddonUrl.'templates/default/';

$FTAN = $admin->getFTAN('');
$sFtan = $FTAN['name'].'='.$FTAN['value'];
// load module language file
$sModulName = $sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}

if( !function_exists( 'make_dir' ) )  {  require(WB_PATH.'/framework/functions.php');  }

?>
<script type="text/javascript">
<!--
var News = {
    WB_URL : '<?php echo $sAppUrl;?>',
    AddonUrl : '<?php echo $sAddonUrl;?>',
    THEME_URL : '<?php echo THEME_URL;?>',
    ThemeUrl:  '<?php echo $sAddonThemeUrl;?>'
};
-->
</script>

<table style="width: 100%;">
<?php  ?>
<tbody>
<tr style="width: 100%; line-height: 2.825em;">
    <td style="width: 25%;">
        <form action="<?php echo WB_URL; ?>/modules/news/add_post.php" method="get" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="<?php echo $TEXT['ADD'].' '.$TEXT['POST']; ?>" class="btn btn-default w3-blue-wb w3-round-small w3-hover-green w3-medium w3-padding-4" style="width: 100%;" />
        </form>
    </td>
    <td style="width: 25%;">
        <form action="<?php echo WB_URL; ?>/modules/news/add_group.php" method="get" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="<?php echo $TEXT['ADD'].' '.$TEXT['GROUP']; ?>" class="btn btn-default w3-blue-wb w3-round-small w3-hover-green w3-medium w3-padding-4" style="width: 100%;" />
        </form>
    </td>
    <td style="width: 25%;">
        <form action="<?php echo WB_URL; ?>/modules/news/modify_settings.php" method="get" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="<?php echo $TEXT['SETTINGS']; ?>" class="btn btn-default w3-blue-wb w3-round-small w3-hover-green w3-medium w3-padding-4" style="width: 100%;" />
        </form>
    </td>
<?php if(defined('DEBUG')&& DEBUG && $admin->ami_group_member('1') ) {  ?>
    <td style="width: 25%;">
        <form action="<?php echo WB_URL; ?>/modules/news/reorgPosition.php" method="get" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="Reorg Position" class="btn btn-default w3-blue-wb w3-round-small w3-hover-green w3-medium w3-padding-4" style="width: 100%;" />
        </form>
    </td>
<?php } ?>
</tr>
</tbody>
</table>

<br />

<h2><?php echo $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$TEXT['POST']; ?></h2>

<?php

$sSortOrder = 'DESC'; // ASC/DESC

// Loop through existing posts
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_news_posts` '
          . 'WHERE `section_id` = '.$section_id.' '
          . 'ORDER BY `position`'.$sSortOrder;
    $oPosts = $database->query($sql);
    if ($oPosts->numRows() > 0) {
        $num_posts = $oPosts->numRows();
?><div class="jsadmin hide"></div>
    <div class="outer">
        <table class="news-post w3-table table-scroll sortierbar" id="tableData"  >
            <thead>
                <tr class="w3-header-blue-wb">
                    <th class="action"><span>&nbsp;</span></th>
                    <th class="title sortierbar" style="padding-left: 5px; text-align: left;"><span><?php print $TEXT['POST']; ?></span></th>
                    <th class="group sortierbar" style=" text-align: left; "><span><?php print $TEXT['GROUP']; ?></span></th>
                    <th class="status" style="padding-right: 5px; text-align: left; "><span><?php print $TEXT['COMMENTS']; ?></span></th>
                    <th class="active" style=" text-align: left; " ><span><?php print $TEXT['ACTIVE']; ?></span></th>
                    <th class="action"><span>&nbsp;</span></th>
                    <th class="action"><span>&nbsp;</span></th>
                    <th class="action"><span>&nbsp;</span></th>
                    <th class="action"><span>&nbsp;</span></th>
                    <th class="sortierbar vorsortiert-" style="padding-right: 8px; "><span>Pos</span></th>
                </tr>
            </thead>
            <tbody>
        <?php
        while($post = $oPosts->fetchRow( MYSQLI_ASSOC )) {
            $pid = $admin->getIDKEY($post['post_id']);
            $sid = $admin->getIDKEY($section_id);
            $iPostId = intval($post['post_id']);
            if ($post['active'] == 1) {
                $activ_string = $TEXT['ENABLED'];
            } else {
                $activ_string = $TEXT['DISABLED'];
            }

            ?>
            <tr class=" sectionrow">
                <td class="action" style="text-align: center;">
                    <a href="<?php echo WB_URL; ?>/modules/news/modify_post.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/modify_16.png"  alt="Modify - " />
                    </a>
                </td>
                <td class="title" style="padding-left: 5px; ">
                    <a href="<?php echo WB_URL; ?>/modules/news/modify_post.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>">
                        <?php echo ($post['title']); ?>
                    </a>
                </td>
                <td class="group">
                    <?php
                    // Get group title
                    $sqlGroups  = 'SELECT `title`   FROM `'.TABLE_PREFIX.'mod_news_groups`  WHERE `group_id` = \''.$post['group_id'].'\'';
                    $oGroups = $database->query($sqlGroups);
                    if($oGroups->numRows() > 0) {
                        $fetch_title = $oGroups->fetchRow( MYSQLI_ASSOC );
                        echo ($fetch_title['title']);
                    } else {
                        echo $TEXT['NONE'];
                    }
                    ?>
                </td>
                <td class="status">
                    <?php
                    // Get number of comments
                    $sqlComment = 'SELECT COUNT(*) `iComment` FROM `'.TABLE_PREFIX.'mod_news_comments` WHERE `post_id` = \''.$post['post_id'].'\'';
                    $iComment = $database->get_one($sqlComment);
                    echo $iComment;
                    ?>
                </td>
                <td class="active_status">
                    <img id="<?php echo $iPostId; ?>_active" src="<?php echo $sAddonThemeUrl; ?>img/24/status_<?php echo (int)$post['active'];?>.png" alt=""  />
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

                <?php if ((($sSortOrder=='DESC'))&&($post['position'] > 1)&&($post['position'] < $num_posts)) { ?>
                    <a href="<?php echo WB_URL; ?>/modules/news/move_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>&amp;move_id=<?php echo $iPostId; ?>&amp;position=<?php echo $post['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/up_16.png" alt="^" />
                    </a>
                <?php } else if (($post['position'] > 1)&&($post['position'] < $num_posts)) { ?>
                    <a href="<?php echo WB_URL; ?>/modules/news/move_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>&amp;move_id=<?php echo $iPostId; ?>&amp;position=<?php echo $post['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/up_16.png" alt="^" />
                    </a>
                <?php } else if ((empty($sSortOrder)||($sSortOrder=='ASC'))&&($post['position'] == $num_posts)){ ?>
                    <a href="<?php echo WB_URL; ?>/modules/news/move_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>&amp;move_id=<?php echo $iPostId; ?>&amp;position=<?php echo $post['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/up_16.png" alt="^" />
                    </a>
                <?php } else if ((($sSortOrder=='DESC'))&&($post['position'] == 1)){ ?>
                    <a href="<?php echo WB_URL; ?>/modules/news/move_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>&amp;move_id=<?php echo $iPostId; ?>&amp;position=<?php echo $post['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/up_16.png" alt="^" />
                    </a>
                <?php } ?>
                </td>

                <td style="text-align: center;">
                <?php if (($post['position'] > 1)&&($post['position'] < $num_posts)) { ?>
                    <a href="<?php echo WB_URL; ?>/modules/news/move_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>&amp;move_id=<?php echo $iPostId; ?>&amp;position=<?php echo $post['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/down_16.png" alt="v" />
                    </a>
                <?php } else if ((empty($sSortOrder)||($sSortOrder=='ASC'))&&($post['position'] == 1)){ ?>
                    <a href="<?php echo WB_URL; ?>/modules/news/move_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>&amp;move_id=<?php echo $iPostId; ?>&amp;position=<?php echo $post['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/down_16.png" alt="^" />
                    </a>
                <?php } else if ((($sSortOrder=='DESC'))&&($post['position'] == $num_posts)){ ?>
                    <a href="<?php echo WB_URL; ?>/modules/news/move_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>&amp;move_id=<?php echo $iPostId; ?>&amp;position=<?php echo $post['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/down_16.png" alt="^" />
                    </a>
                <?php } ?>

                </td>
                <td style="text-align: center;">
                    <a href="javascript:confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>','<?php echo WB_URL; ?>/modules/news/delete_post.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;post_id=<?php echo $pid; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/delete_16.png" alt="X" />
                    </a>
                </td>
                <td style="text-align: right;"><?php echo $post['position']; ?></td>
            </tr>
            <?php
        }
        ?>
            </tbody>
        </table>
    </div>
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
    <table class="news-group w3-table" id="NewsGroupDataTable" >
        <colgroup class="action">
          <col />
        </colgroup>
        <colgroup class="title">
          <col />
        </colgroup>
        <colgroup class="group">
          <col />
        </colgroup>
        <colgroup class="status">
          <col />
        </colgroup>
        <colgroup class="active">
          <col />
        </colgroup>
        <colgroup class="action">
          <col />
          <col />
          <col />
          <col />
          <col />
        </colgroup>
        <thead>
            <tr class=" w3-header-blue-wb">
                <th style="padding-left: 5px;">&nbsp;</th>
                <th style="padding-left: 5px; text-align: left;"><?php print $TEXT['GROUP']; ?></th>
                <th > </th>
                <th > </th>
                <th ><?php print $TEXT['ACTIVE']; ?></th>
                <th > </th>
                <th > </th>
                <th > </th>
                <th > </th>
                <th style="padding-right: 4px;">Pos</th>
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
            <td style="padding-left: 5px;">
                <a href="<?php echo WB_URL; ?>/modules/news/modify_group.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;group_id=<?php echo $gid; ?>">
                    <?php echo $group['title']; ?>
                </a>
            </td>
            <td  style="text-align: right;"> </td>
            <td  style="text-align: right;"> </td>
            <td  style="text-align: center;">
                <?php if($group['active'] == 1) { echo $TEXT['YES']; } else { echo $TEXT['NO']; } ?>
            </td>
            <td  style="text-align: right;"> </td>
            <td  style="text-align: center;">
            <?php if($group['position'] != 1 ) { ?>
                <a href="<?php echo WB_URL; ?>/modules/news/move_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;group_id=<?php echo $gid; ?>&amp;move_id=<?php echo $group['group_id']; ?>&amp;position=<?php echo $group['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/up_16.png" alt="^" />
                </a>
            <?php } ?>
            </td>
            <td  style="text-align: center;">
            <?php if($group['position'] != $num_groups ) { ?>
                <a href="<?php echo WB_URL; ?>/modules/news/move_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;group_id=<?php echo $gid; ?>&amp;move_id=<?php echo $group['group_id']; ?>&amp;position=<?php echo $group['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/down_16.png" alt="v" />
                </a>
            <?php } ?>
            </td>
            <td  style="text-align: center;">
                <a href="javascript:confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/news/delete_group.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;group_id=<?php echo $gid; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/delete_16.png" alt="X" />
                </a>
            </td>
            <td  style="text-align: right;"><?php echo $group['position']; ?></td>
        </tr>
        <?php
    }
    ?>
        </tbody>
    </table>
    <script src="<?php echo $ModuleUrl;?>templates/default/js/TableSort.js" type="text/javascript"></script>

<?php
} else {
    echo $TEXT['NONE_FOUND'];
}
    // include the required file for Javascript admin
    if(file_exists(WB_PATH.'/modules/jsadmin/jsadmin_backend_include.php'))
    {
//        $js_buttonCell = 6;
        include(WB_PATH.'/modules/jsadmin/jsadmin_backend_include.php');
    }
