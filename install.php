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
 * @version         $Id: install.php 1587 2012-01-24 23:19:06Z darkviper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/install.php $
 * @lastmodified    $Date: 2012-01-25 00:19:06 +0100 (Mi, 25. Jan 2012) $
 *
 */
if(defined('WB_PATH'))
{
    // create tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        $database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, __FILE__ );
// Make news post access files dir
            require_once(WB_PATH.'/framework/functions.php');
            if(make_dir(WB_PATH.PAGES_DIRECTORY.'/posts')) {
            }
    }
}
/* **** END INSTALL ********************************************************* */