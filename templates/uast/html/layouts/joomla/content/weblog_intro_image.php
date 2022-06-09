<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$params  = $displayData->params;
$images  = json_decode($displayData->images);

$imgclass   = empty($images->float_intro) ? $params->get('float_intro') : $images->float_intro;
if (empty($images->image_intro))
{
    $layoutAttr = [
        'src' => 'images/placeholder.svg',
        'alt' => empty($images->image_intro_alt) && empty($images->image_intro_alt_empty) ? false : $images->image_intro_alt,
        'data-uk-cover' => ''
    ];
} else {
    $layoutAttr = [
        'src' => $images->image_intro,
        'alt' => empty($images->image_intro_alt) && empty($images->image_intro_alt_empty) ? false : $images->image_intro_alt,
        'data-uk-cover' => ''
    ];
}
?>
<div class="uk-width-1-1 uk-width-1-3@s">
    <figure class="uk-margin-remove uk-border-rounded uk-box-shadow-small uk-overflow-hidden <?php echo $this->escape($imgclass); ?> item-image">
        <?php if ($params->get('link_intro_image') && ($params->get('access-view') || $params->get('show_noauth', '0') == '1')) : ?>
            <a href="<?php echo Route::_(RouteHelper::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)); ?>" itemprop="url" title="<?php echo $this->escape($displayData->title); ?>" class="uk-display-block uk-cover-container">
                <canvas width="400" height="225"></canvas>
                <?php echo LayoutHelper::render('joomla.html.image', array_merge($layoutAttr, ['itemprop' => 'thumbnailUrl'])); ?>
            </a>
        <?php else : ?>
            <?php echo LayoutHelper::render('joomla.html.image', array_merge($layoutAttr, ['itemprop' => 'thumbnail'])); ?>
        <?php endif; ?>
        <?php if (isset($images->image_intro_caption) && $images->image_intro_caption !== '') : ?>
            <figcaption class="caption"><?php echo $this->escape($images->image_intro_caption); ?></figcaption>
        <?php endif; ?>
    </figure>
</div>