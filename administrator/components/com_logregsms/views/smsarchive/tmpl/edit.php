<?php
/**
 * @package    logregsms
 * @subpackage C:
 * @author     Mohammad Hosein Mir {@link https://joomina.ir}
 * @author     Created on 22-Feb-2019
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');


JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<form method="post"
      action="<?php echo JRoute::_('index.php?option=com_logregsms&layout=edit&id='.(int)$this->item->id); ?>"
      name="adminForm" id="smsarchive-form">

    <fieldset class="form-horizontal">
        <legend><?php echo JText::_('جزییات'); ?></legend>
		<?php foreach($this->form->getFieldset() as $field): ?>
        <div class="control-group">
            <div class="control-label"><?php echo $field->label; ?></div>
            <div class="controls"><?php echo $field->input; ?></div>
        </div>
		<?php endforeach; ?>
    </fieldset>

    <div>
        <input type="hidden" name="task" value="smsarchive.edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
