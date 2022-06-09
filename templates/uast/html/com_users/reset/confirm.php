<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

?>
<div class="com-users-reset-confirm reset-confirm uk-width-1-1 uk-width-1-2@s">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1 class="font uk-h4 f900">
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>
	<form action="<?php echo Route::_('index.php?option=com_users&task=reset.confirm'); ?>" method="post" class="com-users-reset-confirm__form form-validate form-horizontal well">
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
			<fieldset>
				<?php if (isset($fieldset->label)) : ?>
					<p class="uk-text-tiny uk-text-muted font f500"><?php echo Text::_($fieldset->label); ?></p>
				<?php endif; ?>
                <div class="uk-child-width-1-1 uk-grid-medium" data-uk-grid>
                    <?php echo $this->form->renderFieldset($fieldset->name); ?>
                </div>
			</fieldset>
		<?php endforeach; ?>
		<div class="com-users-reset-confirm__submit control-group uk-width-1-1 uk-width-auto@s uk-grid-margin-medium">
			<div class="controls">
				<button type="submit" class="uk-box-shadow-small uk-box-shadow-hover-medium uk-border-rounded uk-width-1-1 uk-button uk-button-primary validate">
					<?php echo Text::_('JSUBMIT'); ?>
				</button>
			</div>
		</div>
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
