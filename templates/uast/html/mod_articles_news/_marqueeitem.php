<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
?>
<a href="<?php echo $item->link; ?>" class="uk-display-inline-block uk-text-white font f600 ltr uk-margin-left uk-margin-right"><?php echo $item->title; ?></a>