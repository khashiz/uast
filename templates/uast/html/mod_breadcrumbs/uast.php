<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_breadcrumbs
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;

?>
<nav class="uk-text-nowrap mod-breadcrumbs__wrapper" aria-label="<?php echo htmlspecialchars($module->title, ENT_QUOTES, 'UTF-8'); ?>">
	<ol itemscope itemtype="https://schema.org/BreadcrumbList" class="uk-padding-remove uk-flex uk-margin-remove uk-text-zero breadcrumb">
		<?php
		// Get rid of duplicated entries on trail including home page when using multilanguage
		for ($i = 0; $i < $count; $i++)
		{
			if ($i === 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link === $list[$i - 1]->link)
			{
				unset($list[$i]);
			}
		}

		// Find last and penultimate items in breadcrumbs list
		end($list);
		$last_item_key = key($list);
		prev($list);
		$penult_item_key = key($list);

		// Make a link if not the last item in the breadcrumbs
		$show_last = $params->get('showLast', 1);

		$class   = null;

		// Generate the trail
		foreach ($list as $key => $item) :
			if ($key !== $last_item_key) :
				if (!empty($item->link)) :
					$breadcrumbItem = HTMLHelper::_('link', Route::_($item->link), '<span>' . $item->name . '</span>', ['class' => 'font f700 uk-text-tiny pathway']);
				else :
					$breadcrumbItem = '<span class="font f700 uk-text-tiny uk-text-secondary">' . $item->name . '</span>';
				endif;
				echo '<li class="mod-breadcrumbs__item breadcrumb-item uk-flex' . $class . '">' . $breadcrumbItem . '<i class="fas fa-chevron-left"></i></li>';

			elseif ($show_last) :
				// Render last item if required.
				$breadcrumbItem = '<span class="font f700 uk-text-tiny uk-text-secondary">' . $item->name . '</span>';
				$class          = ' active';
				echo '<li class="mod-breadcrumbs__item breadcrumb-item uk-flex' . $class . '">' . $breadcrumbItem . '</li>';
			endif;
		endforeach; ?>
	</ol>
	<?php

	// Structured data as JSON
	$data = [
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => []
	];

	foreach ($list as $key => $item)
	{
		$data['itemListElement'][] = [
				'@type'    => 'ListItem',
				'position' => $key + 1,
				'item'     => [
						'@id'  => $item->link ? Route::_($item->link, true, Route::TLS_IGNORE, true) : Route::_(Uri::getInstance()),
						'name' => $item->name
				]
		];
	}

	/** @var WebAssetManager $wa */
	$wa = $app->getDocument()->getWebAssetManager();
	$wa->addInline('script', json_encode($data, JSON_UNESCAPED_UNICODE), [], ['type' => 'application/ld+json']);
	?>
</nav>
