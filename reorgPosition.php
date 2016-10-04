<?php
/**
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category        modules
 * @package         news
 * @subpackage      reorgPosition
 * @author          Dietmar Wöllbrink
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id:  $
 * @filesource      $HeadURL:  $
 * @lastmodified    $Date:  $
 *
 */
 
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
require(WB_PATH.'/modules/admin.php');
$backlink = ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id;
if(!$admin->checkFTAN('GET')) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}
if ( !class_exists('order', false) ) { require(WB_PATH.'/framework/class.order.php'); }
$news   = new order(TABLE_PREFIX.'mod_news_posts', 'position', 'post_id', 'section_id');
$news->clean( $section_id );

$groups = new order(TABLE_PREFIX.'mod_news_groups', 'position', 'group_id', 'section_id');
$groups->clean( $section_id );

$admin->print_success($TEXT['SUCCESS'], $backlink );

