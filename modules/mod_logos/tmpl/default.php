<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$modId = 'mod-custom' . $module->id;

if ($params->get('backgroundimage'))
{
	/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
	$wa->addInlineStyle('
#' . $modId . '{background-image: url("' . Uri::root(true) . '/' . HTMLHelper::_('cleanImageURL', $params->get('backgroundimage'))->url . '");}
', ['name' => $modId]);
}

?>
<div class="uk-container">
    <div id="<?php echo $modId; ?>" class="uk-position-relative" data-uk-slider>
        <ul class="uk-slider-items uk-child-width-1-2 uk-child-width-1-6@m uk-grid">
            <?php foreach ($params->get('logos') as $item) : ?>
                <?php if ($item->logo != '') { ?>
                    <li>
                        <a href="<?php echo $item->link; ?>" title="<?php echo $item->title; ?>" class="uk-flex uk-flex-center uk-flex-middle uk-flex-column uk-link-reset" target="_blank">
                            <img src="<?php echo (HTMLHelper::cleanImageURL($item->logo))->url; ?>" width="<?php echo (HTMLHelper::cleanImageURL($item->logo))->width; ?>" height="<?php echo (HTMLHelper::cleanImageURL($item->logo))->height; ?>" alt="<?php echo $item->title; ?>">
                            <span class="font uk-text-tiny uk-margin-top f700 uk-text-secondary f700 uk-display-block"><?php echo $item->title; ?></span>
                        </a>
                    </li>
                <?php } ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>