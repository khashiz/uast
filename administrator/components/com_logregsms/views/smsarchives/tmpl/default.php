<?php

/**
 * @package    logregsms
 * @subpackage F:
 * @author     Mohammad Miri {@link joominamarket.com}
 * @author     Created on 21-Oct-2016
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.core');

$user = LRSHelper::User();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canOrder = $user->authorise('core.edit.state', 'com_logregsms');
$saveOrder = $listOrder == 'a.id';
?>
<form action="index.php?option=com_logregsms&view=smsarchives" method="post" name="adminForm" id="adminForm">

  <div class="row">
    <div class="col-md-4">
      <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" placeholder="جستجو ..." class="form-control" />
    </div>

    <div class="col-md-3">
      <?php echo JHTML::calendar($this->escape($this->state->get('filter.created_on')), 'filter_created_on', 'filter_created_on', '%Y-%m-%d'); ?>
    </div>

    <div class="col-md-3">
      <button type="submit" class="btn btn-success"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
      <button class="btn btn-primary" type="button" onclick="document.getElementById('filter_search').value = '';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
    </div>
  </div>

  <br>

  <table id="smsarchivesList" class="table itemList">
    <thead>
      <tr>
        <th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
        <th><?php echo JHtml::_('grid.sort', 'شناسه', 'a.id', $listDirn, $listOrder); ?></th>
        <th><?php echo JHtml::_('grid.sort', 'شماره موبایل', 'a.to', $listDirn, $listOrder); ?></th>
        <th><?php echo JHtml::_('grid.sort', 'ارسال از', 'a.from', $listDirn, $listOrder); ?></th>
        <th><?php echo JHtml::_('grid.sort', 'تاریخ', 'a.created_on', $listDirn, $listOrder); ?></th>
        <th>زمان</th>
        <th>پیغام</th>
        <th>نتیجه</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->items as $i => $row) : ?>
        <?php //$link = JRoute::_('index.php?option=com_logregsms&task=category.edit&id='.$row->id); 
        ?>
        <tr class="row<?php echo $i % 2; ?>">
          <td>
            <?php echo JHtml::_('grid.id', $i, $row->id); ?>
          </td>
          <td>
            <?php echo $row->id; ?>
          </td>
          <td>
            <?php echo $row->to; ?>
          </td>
          <td>
            <?php echo $row->from; ?>
          </td>
          <td>
            <?php echo JHTML::date($row->created_on, 'Y-m-d'); ?>
          </td>
          <td>
            <?php echo $row->time; ?>
          </td>
          <td>
            <?php echo $row->message; ?>
          </td>
          <td>
            <?php echo $row->result; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="28">
          <?php echo $this->pagination->getListFooter(); ?>
          <br>
          <?php echo $this->pagination->getLimitBox(); ?>
        </td>
      </tr>
    </tfoot>
  </table>

  <div>
    <input type="hidden" name="option" value="com_logregsms" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="smsarchive" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>