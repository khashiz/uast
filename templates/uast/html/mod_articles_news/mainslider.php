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

JHtml::_('script', 'jquery.marquee.min.js', array('version' => 'auto', 'relative' => true));

if (!$list)
{
	return;
}

?>
<div class="mod-articlesnews newsflash uk-position-relative uk-visible-toggle uk-border-rounded uk-overflow-hidden uk-box-shadow-small mainSlider" data-uk-slideshow="animation: slide; autoplay: true; autoplay-interval:2500; ratio: 16:9;">
    <div class="uk-slideshow-items">
        <?php foreach ($list as $item) : ?>
            <div class="mod-articlesnews__item" itemscope itemtype="https://schema.org/Article">
                <?php require ModuleHelper::getLayoutPath('mod_articles_news', '_mainslideritem'); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <ul class="uk-slideshow-nav uk-dotnav uk-position-top-left uk-position-small"></ul>
    <a class="uk-position-center-left uk-position-small uk-hidden-hover uk-text-primary" href="#" data-uk-slidenav-next data-uk-slideshow-item="previous"></a>
    <a class="uk-position-center-right uk-position-small uk-hidden-hover uk-text-primary" href="#" data-uk-slidenav-previous data-uk-slideshow-item="next"></a>
</div>