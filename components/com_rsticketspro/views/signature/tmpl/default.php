<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');

if ($this->params->get('show_page_heading', 1))
{
	?>
	<h1><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
<?php
}

echo JText::_(RSTicketsProHelper::getConfig('global_message'));
?>

<form id="rsticketspro_form" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=signature'); ?>" method="post" name="signatureForm">
	<?php
	foreach ($this->form->getFieldsets() as $fieldset)
	{
		echo $this->form->renderFieldset($fieldset->name);
	}
	?>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php echo JText::_('RST_UPDATE'); ?></button>
	</div>

	<?php
	if ($this->show_footer)
	{
		echo $this->footer;
	}
	?>
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="task" value="signature.save" />
</form>