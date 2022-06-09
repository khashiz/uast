<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_logreg
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="mod_logreg">
	<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="login-form" class="form-vertical">
		<div class="login-greeting">
			<?php echo 'سلام '.$user->get('name') . ' عزیز'; ?>
		</div>
		<div class="logout-button">
			<input type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('JLOGOUT'); ?>" />
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.logout" />
			<input type="hidden" name="return" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>