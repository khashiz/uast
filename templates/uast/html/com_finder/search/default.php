<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   (C) 2011 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$this->document->getWebAssetManager()
	->useStyle('com_finder.finder')
	->useScript('com_finder.finder');

?>
<div class="uk-padding-large uk-padding-remove-horizontal com-finder finder">
    <div class="uk-container uk-container-xsmall">
        <div id="search-form" class="uk-margin-large-bottom com-finder__form">
            <?php echo $this->loadTemplate('form'); ?>
        </div>
        <?php // Load the search results layout if we are performing a search. ?>
        <?php if ($this->query->search === true) : ?>
            <div id="search-results" class="com-finder__results">
                <?php echo $this->loadTemplate('results'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>