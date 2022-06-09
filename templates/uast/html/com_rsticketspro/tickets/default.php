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

if ($this->isStaff)
{
	JHtml::_('script', 'com_rsticketspro/tickets.js', array('relative' => true, 'version' => 'auto'));
}

JText::script('RST_DELETE_TICKETS_CONFIRM');
JText::script('RST_DELETE_TICKET_CONFIRM');

$listOrder 	= $this->escape($this->state->get('list.ordering'));
$listDirn 	= $this->escape($this->state->get('list.direction'));

$script = array();
$script[] = 'Joomla.submitbutton = function(task) {';

if (!empty($this->permissions->delete_ticket))
{
	$script[] = "if (document.getElementById('bulk_delete').value == '1' && !confirm(Joomla.JText._('RST_DELETE_TICKETS_CONFIRM'))) {";
	$script[] = 'return false;';
	$script[] = '}';
}

if (!empty($this->permissions->export_tickets))
{
	$script[] = "RSTicketsPro.exportCSV.totalItems = {$this->totalItems};";
	$script[] = "if (task === 'tickets.exportcsv') {";
	$script[] = "RSTicketsPro.exportCSV.setCSV(0, '');";
	$script[] = "return false;";
	$script[] = "}";

	JHtml::_('script', 'com_rsticketspro/export.js', array('relative' => true, 'version' => 'auto'));
}

$script[] = "Joomla.submitform(task, document.getElementById('adminForm'));";
$script[] = "}";

JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
?>

<?php

echo $this->globalMessage;
?>


<form action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=tickets'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->isStaff && !empty($this->permissions->export_tickets)) { ?>
		<div class="com-rsticketspro-progress" id="com-rsticketspro-export-progress" style="display:none">
			<div class="com-rsticketspro-bar" style="width: 0%;">0%</div>
		</div>
		<p>
			<button type="button" class="btn btn-success" onclick="Joomla.submitbutton('tickets.exportcsv');"><?php echo JText::_('COM_RSTICKETSPRO_EXPORT');?></button>
		</p>
	<?php } ?>
<?php if ($this->isStaff) { ?>
	<div id="bulk_actions" style="display: none;">
		<p><strong><?php echo JText::_('RST_BULK_ACTIONS'); ?></strong></p>
		<?php
		if ($this->permissions->move_ticket)
		{
			echo '<div>' . $this->bulkForm->getField('bulk_department_id')->input . '</div>';
		}
		if ($this->permissions->assign_tickets)
		{
			echo '<div>' . $this->bulkForm->getField('bulk_staff_id')->input . '</div>';
		}
		if ($this->permissions->update_ticket)
		{
			echo '<div>' . $this->bulkForm->getField('bulk_priority_id')->input . '</div>';
		}
		if ($this->permissions->change_ticket_status)
		{
			echo '<div>' . $this->bulkForm->getField('bulk_status_id')->input . '</div>';
		}
		if ($this->autocloseEnabled)
		{
			echo '<div>' . $this->bulkForm->getField('bulk_notify')->input . '</div>';
		}
		if ($this->permissions->delete_ticket)
		{
			echo '<div>' . $this->bulkForm->getField('bulk_delete')->input . '</div>';
		}
		?>

		<p>
			<button type="button" id="rst_update_button" class="btn btn-primary" onclick="Joomla.submitbutton('ticket.bulkupdate');"><?php echo JText::_('RST_UPDATE'); ?></button>
		</p>
	</div>
<?php } ?>

    <div>

        <div class="uk-margin-bottom" data-uk-grid>
            <div class="uk-width-expand">
                <?php if ($this->params->get('show_page_heading', 1)) { ?>
                    <h1 class="font uk-h4 f900 uk-margin-remove"><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
                <?php } ?>
            </div>
            <?php if ($this->isSearching || ($this->isStaff && $this->hasSearches)) { ?>
                <div class="uk-width-auto">
                    <p>
                        <?php
                        if ($this->isSearching)
                        {
                            if ($this->isStaff)
                            {
                                ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=predefinedsearch.add'); ?>" class="btn btn-success rst_search"><?php echo JText::_('RST_SAVE_SEARCH'); ?></a>
                                <?php
                            }
                            ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=resetsearch'); ?>" class="uk-text-danger font f700 uk-text-tiny"><?php echo JText::_('RST_RESET_SEARCH'); ?></a>
                            <?php
                        }
                        if ($this->isStaff && $this->hasSearches)
                        {
                            ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=predefinedsearches'); ?>" class="btn btn-secondary rst_manage_searches"><?php echo JText::_('RST_MANAGE_SEARCHES'); ?></a>
                            |
                            <?php
                            foreach ($this->searches as $search)
                            {
                                if (!$search->current)
                                {
                                    ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=predefinedsearch.perform&id='.$search->id); ?>" class="btn btn-secondary btn-mini btn-sm <?php echo RSTicketsProHelper::tooltipClass();?>" title="<?php echo RSTicketsProHelper::tooltipText(JText::sprintf('RST_SEARCH_CLICK_DESC', $this->escape($search->name))); ?>"><?php echo $this->escape($search->name); ?></a>
                                    <?php
                                }
                                else
                                {
                                    echo $this->escape($search->name);
                                }
                            }
                        }
                        ?>
                    </p>
                </div>
            <?php } ?>
        </div>

        <?php if (count($this->items)) { ?>
            <table class="uk-table uk-table-divider uk-table-striped uk-table-middle uk-margin-remove uk-table-responsive">
<?php
if ($this->params->get('show_headings', 1))
{
	?>
	<thead>
		<tr>
		<?php
		if ($this->params->get('show_offset', 1))
		{
			?>
			<th id="rst_head_item_no" class="uk-text-center uk-text-tiny font f500 uk-text-muted"><?php echo JText::_('#'); ?></th>
			<?php
		}
		
		if ($this->isStaff)
		{
			?>
			<th id="rst_head_check_all" class="uk-text-center uk-text-tiny font f500 uk-text-muted">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<?php
		}
			
		if ($this->params->get('show_date', 1))
		{
			?>
			<th id="rst_head_date" nowrap="nowrap" class="uk-text-center uk-text-tiny font f500 uk-text-muted">
                <?php echo JText::_('RST_TICKET_DATE'); ?>
				<?php /* echo JHtml::_('grid.sort', 'RST_TICKET_DATE', 'date', $listDirn, $listOrder, 'none'); */ ?>
			</th>
			<?php
		}
		
		if ($this->params->get('show_last_reply', 1))
		{
			?>
			<th id="rst_head_last_reply" nowrap="nowrap" class="uk-text-center uk-text-tiny font f500 uk-text-muted">
                <?php echo JText::_('RST_TICKET_LAST_REPLY'); ?>
				<?php /* echo JHtml::_('grid.sort', 'RST_TICKET_LAST_REPLY', 'last_reply', $listDirn, $listOrder, 'none'); */ ?>
			</th>
			<?php
		}
			
		if ($this->isStaff)
		{
			?>
			<th id="rst_head_flag" nowrap="nowrap" width="1%" class="uk-text-center uk-text-tiny font f500 uk-text-muted">
				<?php echo JText::_('RST_FLAGGED'); ?>
			</th>
			<?php
			if ($this->permissions->delete_ticket)
			{
				?>
				<th id="rst_head_delete" nowrap="nowrap" class="uk-text-center uk-text-tiny font f500 uk-text-muted">
					<?php echo JText::_('RST_DELETE'); ?>
				</th>
			<?php
			}
		}
		?>
			<th id="rst_head_ticket_code" nowrap="nowrap" class="uk-width-auto uk-text-center uk-text-tiny font f500 uk-text-muted">
			<?php
            echo JText::_('RST_TICKET_SUBJECT');
			/* echo JHtml::_('grid.sort', 'RST_TICKET_SUBJECT', 'subject', $listDirn, $listOrder, 'none'); */
			?>
			</th>

            <th id="rst_head_ticket_code" nowrap="nowrap" class="uk-width-auto uk-text-center uk-text-tiny font f500 uk-text-muted">
                <?php
                if ($this->params->get('show_code', 1))
                {
                    echo JText::_('RST_TICKET_CODE');
                    /* echo JHtml::_('grid.sort', 'RST_TICKET_CODE', 'code', $listDirn, $listOrder, 'none'); */
                }
                ?>
            </th>
			
			<?php
			if ($this->params->get('show_customer', 1))
			{
				?>
				<th id="rst_head_customer" nowrap="nowrap" class="uk-text-center uk-text-tiny font f500 uk-text-muted">
					<?php echo JHtml::_('grid.sort', 'RST_TICKET_CUSTOMER', 'customer', $listDirn, $listOrder, 'none'); ?>
				</th>
				<?php
			}

			if ($this->params->get('show_priority', 1))
			{
				?>
				<th id="rst_head_priority" nowrap="nowrap" class="uk-text-center uk-text-tiny font f500 uk-text-muted">
					<?php echo JHtml::_('grid.sort', 'RST_TICKET_PRIORITY', 'priority', $listDirn, $listOrder, 'none'); ?>
				</th>
				<?php
			}
			
			if ($this->params->get('show_status', 1))
			{
				?>
				<th id="rst_head_status" class="uk-width-small uk-text-center uk-text-tiny font f500 uk-text-muted">
                    <?php echo JText::_('RST_TICKET_STATUS'); ?>
					<?php /* echo JHtml::_('grid.sort', 'RST_TICKET_STATUS', 'status', $listDirn, $listOrder, 'none'); */ ?>
				</th>
				<?php
			}
			
			if ($this->params->get('show_staff', 1))
			{
				?>
				<th id="rst_head_sort" nowrap="nowrap" class="uk-text-center uk-text-tiny font f500 uk-text-muted">
					<?php echo JHtml::_('grid.sort', 'RST_TICKET_STAFF', 'staff', $listDirn, $listOrder, 'none'); ?>
				</th>
				<?php
			}

			if ($this->params->get('show_time_spent', 0) && RSTicketsProHelper::getConfig('enable_time_spent'))
			{
				?>
				<th id="rst_head_sort" nowrap="nowrap" class="uk-text-center uk-text-tiny font f500 uk-text-muted">
					<?php echo JHtml::_('grid.sort', 'RST_TIME_SPENT', 'time_spent', $listDirn, $listOrder, 'none'); ?>
				</th>
				<?php
			}
			?>
		</tr>
	</thead>
	<?php
}
?>
	<tbody>
		<?php
		foreach ($this->items as $i => $item)
		{
			?>
			<tr class="rst_priority_color_<?php echo $item->priority_id; ?>">
				<?php if ($this->params->get('show_offset', 1)) { ?>
				<td class="uk-text-small font f700 uk-text-secondary uk-text-center uk-visible@s"><?php echo $this->pagination->getRowOffset($i); ?></td>
				<?php } ?>

				<?php if ($this->isStaff) { ?>
				<td class="rst_cell_checkbox center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
				<?php } ?>

				<?php if ($this->params->get('show_date', 1)) { ?>
				<td class="uk-text-small font f700 uk-text-secondary uk-text-center fnum"><?php echo '<span class="uk-hidden@s uk-text-muted">'.JText::_('RST_TICKET_DATE').'&ensp;:&ensp;</span>'.fnum($this->escape($this->showDate($item->date))); ?></td>
				<?php } ?>

				<?php if ($this->params->get('show_last_reply', 1)) { ?>
				<td class="uk-text-small font f700 uk-text-secondary uk-text-center fnum"><?php echo '<span class="uk-hidden@s uk-text-muted">'.JText::_('RST_TICKET_LAST_REPLY').'&ensp;:&ensp;</span>'.fnum($this->escape($this->showDate($item->last_reply))); ?></td>
				<?php } ?>

				<?php if ($this->isStaff) { ?>
				<td class="rst_cell_flag center hidden-phone hidden-tablet"><button type="button" class="btn btn-small btn-sm <?php echo $item->flagged ? 'rst_flagged' : 'rst_not_flagged'; ?>" onclick="RSTicketsPro.flagTicket(this, '<?php echo $item->id; ?>');"><i class="rsticketsproicon-star"></i></button></td>

				<?php if ($this->permissions->delete_ticket) { ?>
				<td class="rst_cell_delete_ticket center">
					<a class="btn btn-small btn-sm btn-danger rst_button_delete_ticket <?php echo RSTicketsProHelper::tooltipClass();?>" title="<?php echo RSTicketsProHelper::tooltipText(JText::_('RST_TICKET_DELETE_DESC')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=ticket.delete&cid=' . $item->id); ?>" onclick="return confirm(Joomla.JText._('RST_DELETE_TICKET_CONFIRM'));">&#10006;</a>
				</td>
				<?php } ?>

				<?php } ?>

				<td class="uk-text-center rst_cell_subject">
					<a class="uk-text-small font f700 uk-text-secondary uk-text-center uk-display-block" href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&id='.$item->id.':'.JFilterOutput::stringURLSafe($item->subject)); ?>"><?php echo '<span class="uk-hidden@s uk-text-muted">'.JText::_('RST_TICKET_SUBJECT').'&ensp;:&ensp;</span>'.$this->escape($item->subject); ?></a>
				</td>
                <td class="uk-text-center">
                    <?php if ($item->has_files) { ?>
                        <i class="rsticketsproicon-attach"></i>
                    <?php } ?>
                    <?php if ($this->params->get('show_code', 1)) { ?>
                        <a class="uk-text-small font f700 uk-text-secondary uk-text-center fnum" href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&id='.$item->id.':'.JFilterOutput::stringURLSafe($item->subject)); ?>"><?php echo '<span class="uk-hidden@s uk-text-muted">'.JText::_('RST_TICKET_CODE').'&ensp;:&ensp;</span>'.$item->code; ?></a>
                        <?php /* if ($this->params->get('show_replies', 1)) { ?>
                            (<?php echo $item->replies; ?>)
                        <?php } */ ?>
                    <?php } ?>

                    <?php if (!$this->params->get('show_code', 1) && $this->params->get('show_replies', 1)) { ?>
                        (<?php echo $item->replies; ?>)
                    <?php } ?>
                    <?php echo $this->notify($item); ?>
                </td>

				<?php if ($this->params->get('show_customer', 1)) { ?>
				<td class="rst_cell_customer center"><?php echo $this->escape($item->customer); ?></td>
				<?php } ?>

				<?php if ($this->params->get('show_priority', 1)) { ?>
				<td class="center rst_priority_cell hidden-phone"><?php echo JText::_($item->priority); ?></td>
				<?php } ?>

				<?php if ($this->params->get('show_status', 1)) { ?>
                    <?php
                    $lblStatus = 'default';
                    if ($item->status == 'TICKET_STATUS_NEW') {$lblStatus = 'default';}
                    elseif ($item->status == 'TICKET_STATUS_ANSWERED') {$lblStatus = 'success';}
                    elseif ($item->status == 'TICKET_STATUS_CLOSED') {$lblStatus = 'danger';}
                    ?>
				<td class="uk-text-small font f500 uk-text-center" data-status="<?php echo $item->status; ?>">
                    <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&id='.$item->id.':'.JFilterOutput::stringURLSafe($item->subject)); ?>" class="uk-label uk-border-pill uk-box-shadow-small uk-label-<?php echo $lblStatus; ?>"><?php echo JText::_($item->status); ?></a>
                </td>
				<?php } ?>

				<?php if ($this->params->get('show_staff', 1)) { ?>
				<td class=""><?php echo $item->staff_id ? $this->escape($item->staff) : '<em>'.JText::_('RST_UNASSIGNED').'</em>'; ?></td>
				<?php } ?>

				<?php if ($this->params->get('show_time_spent', 0) && RSTicketsProHelper::getConfig('enable_time_spent')) { ?>
				<td class="center hidden-phone hidden-tablet"><?php echo $this->showTotal($item->time_spent); ?></td>
				<?php } ?>
			</tr>
		<?php
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11" class="center">
				<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
				<?php echo $this->pagination->getPagesCounter(); ?>
			</td>
		</tr>
	</tfoot>
</table>
        <?php } else { ?>
            <div>
                <div class="uk-placeholder uk-margin-remove uk-text-center uk-padding-large">
                    <div class="uk-margin-bottom"><i class="far fa-3x fa-message-lines"></i></div>
                    <p class="font uk-text-muted uk-text-small uk-margin-medium-bottom uk-margin-remove-top"><?php echo JText::_('RST_NO_RECENT_ACTIVITY'); ?></p>
                    <div class="uk-width-1-1 uk-width-1-3@s uk-margin-auto">
                        <a href="<?php echo JRoute::_("index.php?Itemid=151"); ?>" class="uk-button uk-button-success uk-border-rounded uk-box-shadow-small"><i class="fas fa-plus"></i><?php echo JText::_('RST_SUBMIT_TICKET'); ?></a>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>

<?php
if ($this->showFooter)
{
	echo $this->footer;
}
?>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->escape($listOrder); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($listDirn); ?>" />
<input type="hidden" name="limitstart" value="<?php echo $this->escape($this->limitstart); ?>" />
<input type="hidden" name="task" value="" />
</form>