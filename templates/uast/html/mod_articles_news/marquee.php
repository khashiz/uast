<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

if (!$list)
{
	return;
}



?>
<div data-uk-sticky="offset: 62; media: @s" class="marqueeWrapper">
    <div class="uk-background-primary uk-padding-small uk-padding-remove-horizontal marquee uk-overflow-hidden uk-text-white uk-box-shadow-small ltr">
        <?php foreach ($list as $item) : ?>
            <?php require ModuleHelper::getLayoutPath('mod_articles_news', '_marqueeitem'); ?>
        <?php endforeach; ?>
    </div>
</div>