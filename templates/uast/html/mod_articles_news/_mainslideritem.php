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
<a href="<?php echo $item->link; ?>" class="uk-display-block">
    <?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?>
        <figure class="newsflash-image" data-uk-cover>
            <?php echo LayoutHelper::render(
                'joomla.html.image',
                [
                    'src' => $item->imageSrc,
                    'alt' => $item->imageAlt,
                ]
            ); ?>
            <?php if (!empty($item->imageCaption)) : ?>
                <figcaption>
                    <?php echo $item->imageCaption; ?>
                </figcaption>
            <?php endif; ?>
        </figure>
    <?php endif; ?>
    <div class="uk-overlay uk-overlay-primary uk-position-bottom uk-text-center uk-transition-slide-bottom uk-padding-small">
        <h3 class="uk-margin-remove uk-text-right uk-h5 uk-margin-remove font f700"><?php echo $item->title; ?></h3>
    </div>
</a>
<div class="uk-hidden">
<?php if ($params->get('item_title')) : ?>

	<?php $item_heading = $params->get('item_heading', 'h4'); ?>
	<<?php echo $item_heading; ?> class="newsflash-title">
	<?php if ($item->link !== '' && $params->get('link_titles')) : ?>

	<?php else : ?>
		<?php echo $item->title; ?>
	<?php endif; ?>
	</<?php echo $item_heading; ?>>
<?php endif; ?>

<?php if (!$params->get('intro_only')) : ?>
	<?php echo $item->afterDisplayTitle; ?>
<?php endif; ?>

<?php echo $item->beforeDisplayContent; ?>

<?php if ($params->get('show_introtext', 1)) : ?>
	<?php echo $item->introtext; ?>
<?php endif; ?>

<?php echo $item->afterDisplayContent; ?>

<?php if (isset($item->link) && $item->readmore != 0 && $params->get('readmore')) : ?>
	<?php echo LayoutHelper::render('joomla.content.readmore', array('item' => $item, 'params' => $item->params, 'link' => $item->link)); ?>
<?php endif; ?>
</div>