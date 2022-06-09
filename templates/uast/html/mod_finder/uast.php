<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_finder
 *
 * @copyright   (C) 2011 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Module\Finder\Site\Helper\FinderHelper;

// Load the smart search component language file.
$lang = $app->getLanguage();
$lang->load('com_finder', JPATH_SITE);

$input = '<input autofocus type="search" name="q" id="mod-finder-searchword' . $module->id . '" class="uk-search-input font f500 uk-width-1-1 js-finder-search-query form-control" value="' . htmlspecialchars($app->input->get('q', '', 'string'), ENT_COMPAT, 'UTF-8') . '"'
	. ' placeholder="' . Text::_('MOD_FINDER_SEARCH_VALUE') . '">';

$showLabel  = $params->get('show_label', 1);
$labelClass = (!$showLabel ? 'visually-hidden ' : '') . 'finder';
$label      = '<label for="mod-finder-searchword' . $module->id . '" class="' . $labelClass . '">' . $params->get('alt_label', Text::_('JSEARCH_FILTER_SUBMIT')) . '</label>';

$output = '';

if ($params->get('show_button', 0))
{
	/* $output .= $label; */
	$output .= '<div class="uk-position-relative mod-finder__search input-group">';
	$output .= $input;
	$output .= '<button class="uk-position-center-left uk-button uk-button-link" type="submit"><span class="fas fa-search"></span></button>';
	$output .= '</div>';
}
else
{
	/* $output .= $label; */
	$output .= $input;
}

Text::script('MOD_FINDER_SEARCH_VALUE', true);

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_finder');

/*
 * This segment of code sets up the autocompleter.
 */
if ($params->get('show_autosuggest', 1))
{
	$wa->usePreset('awesomplete');
	$app->getDocument()->addScriptOptions('finder-search', array('url' => Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component', false)));
}

$wa->useScript('com_finder.finder');

?>
<div class="searchToggle uk-position-absolute uk-width-1-1 uk-background-white uk-position-z-index-negative uk-box-shadow-small" hidden>
    <div class="uk-container">
        <form class="uk-search uk-search-large mod-finder js-finder-searchform form-search uk-width-1-1" action="<?php echo Route::_($route); ?>" method="get" role="search">
            <div class="uk-grid-small" data-uk-grid>
                <div class="uk-width-auto uk-flex uk-flex-middle">
                    <i class="far fa-2x fa-search uk-text-secondary"></i>
                </div>
                <div class="uk-width-expand">
                    <?php echo $output; ?>
                    <?php $show_advanced = $params->get('show_advanced', 0); ?>
                    <?php if ($show_advanced == 2) : ?>
                        <a href="<?php echo Route::_($route); ?>" class="mod-finder__advanced-link"><?php echo Text::_('COM_FINDER_ADVANCED_SEARCH'); ?></a>
                    <?php elseif ($show_advanced == 1) : ?>
                        <div class="mod-finder__advanced js-finder-advanced">
                            <?php echo HTMLHelper::_('filter.select', $query, $params); ?>
                        </div>
                    <?php endif; ?>
                    <?php echo FinderHelper::getGetFields($route, (int) $params->get('set_itemid', 0)); ?>
                </div>
            </div>
        </form>
    </div>
</div>
