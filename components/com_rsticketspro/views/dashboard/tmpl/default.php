<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('stylesheet', 'com_rsticketspro/awesomplete.css', array('relative' => true, 'version' => 'auto'));
JHtml::_('script', 'com_rsticketspro/awesomplete.min.js', array('relative' => true, 'version' => 'auto'));
JHtml::_('script', 'com_rsticketspro/dashboard.js', array('relative' => true, 'version' => 'auto'));

if ($this->params->get('show_page_heading', 1))
{
	?>
	<h1><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
	<?php
}

echo JText::_(RSTicketsProHelper::getConfig('global_message'));
?>
<form method="post" action="<?php echo $this->search_link; ?>">
	<div id="rsticketspro_dashboard_search">
		<div class="btn-group">
			<input type="text" placeholder="<?php echo $this->escape(JText::_('RST_SEARCH_HELPDESK')); ?>" class="form-control input-xlarge" name="search" autocomplete="off" id="rsticketspro_searchinp" />
			<button type="submit" class="btn btn-primary"><i id="rstickets_search_icon" class="icon-search"></i><?php echo JHtml::_('image', 'com_rsticketspro/loading.gif', '', array('id' => 'rsticketspro_loading', 'style' => 'display:none;'), true); ?></button>
		</div>
	</div>

	<div class="rst_dashboard_items">
		<div class="rst_dashboard_item">
			<h1><i class="rsticketsproicon-mail"></i> <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=submit'); ?>"><?php echo JText::_('RST_SUBMIT_TICKET'); ?></a></h1>
			<div class="caption">
				<p><?php echo JText::_($this->params->get('submit_ticket_desc')); ?></p>
			</div>
		</div>
		<div class="rst_dashboard_item">
			<h1><i class="rsticketsproicon-clipboard"></i> <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=tickets'); ?>"><?php echo JText::_('RST_VIEW_TICKETS'); ?></a></h1>
			<div class="caption">
				<p><?php echo JText::_($this->params->get('view_tickets_desc')); ?></p>
			</div>
		</div>
		<div class="rst_dashboard_item">
			<h1><i class="rsticketsproicon-search-circled"></i> <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=search'); ?>"><?php echo JText::_('RST_SEARCH_TICKETS'); ?></a></h1>
			<div class="caption">
				<p><?php echo JText::_($this->params->get('search_tickets_desc')); ?></p>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>

	<?php
	if ($this->params->get('show_kb', 1))
	{
		?>
		<div id="rsticketspro_dashboard_knowledgebase">
			<div>
				<h3><?php echo JText::_('RST_KNOWLEDGEBASE'); ?></h3>

				<?php
				if (count($this->categories))
				{
					$parts = array_chunk($this->categories, 3);
					foreach ($parts as $part)
					{
						?>
						<div class="rst_dashboard_kb_row">
							<?php
							foreach($part as $category)
							{
								if ($category->thumb)
								{
									$thumb = JHtml::_('image', 'components/com_rsticketspro/assets/thumbs/small/'.$category->thumb, $category->name, array(), false);
								}
								else
								{
									$thumb = JHtml::_('image', 'com_rsticketspro/kb-icon.png', $category->name, array(), true);
								}
								?>
								<div class="rst_dashboard_kb_item">
									<strong>
										<?php echo $thumb; ?>
										<a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase&cid='.$category->id.':'.JFilterOutput::stringURLSafe($category->name), true, $this->kb_itemid); ?>"><?php echo $this->escape($category->name); ?>
										</a>
									</strong>
									<?php
									if ($category->description)
									{
										?>
										<div><?php echo $category->description; ?></div>
										<?php
									}
									?>
								</div>
								<?php
							}
							?>
						</div>
						<div class="clearfix"></div>
						<?php
					}
				}
				else
				{
					?>
					<div class="rst_dashboard_center well well-small">
						<p><?php echo JText::_('RST_NO_KB_CATEGORIES'); ?></p>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	if ($this->params->get('show_tickets', 1))
	{
		?>
		<div id="rsticketspro_dashboard_tickets" class="row-fluid">
			<div class="span12">
				<h3><?php echo JText::_('RST_MY_TICKETS'); ?></h3>
				<?php
				if ($this->user->get('guest'))
				{
					?>
					<div class="alert alert-info">
						<p><?php echo JText::_('RST_YOU_HAVE_TO_BE_LOGGED_IN'); ?></p>
						<p><a class="btn btn-primary" href="<?php echo $this->login_link; ?>"><i class="icon-lock"></i> <?php echo JText::_('RST_CLICK_HERE_TO_LOGIN'); ?></a></p>
					</div>
					<?php
				}
				else
				{
					if (count($this->tickets))
					{
						?>
						<table class="table table-striped table-hover table-bordered">
							<thead>
							<tr>
								<th><?php echo JText::_('RST_TICKET_SUBJECT'); ?></th>
								<th><?php echo JText::_('RST_TICKET_STATUS'); ?></th>
							</tr>
							</thead>
							<tbody>
							<?php
							foreach ($this->tickets as $ticket)
							{
								$hasReply = isset($ticket->message);
								?>
								<tr>
									<td><?php if ($hasReply) { ?><strong><?php } ?><a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&id='.$ticket->id.':'.JFilterOutput::stringURLSafe($ticket->subject)); ?>"><?php echo $this->escape($ticket->subject); ?></a><?php if ($hasReply) { ?> (1)</strong><?php } ?></td>
									<td><?php echo $this->escape(JText::_($ticket->status_name)); ?></td>
								</tr>
								<?php
							}
							?>
							</tbody>
						</table>
						<?php
					}
					else
					{
						?>
						<div class="alert alert-info">
							<p><?php echo JText::_('RST_NO_RECENT_ACTIVITY'); ?></p>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
	}
	?>
</form>
<div class="clearfix"></div>

<input type="hidden" name="kb_itemid" value="<?php echo $this->kb_itemid; ?>" />
<input type="hidden" name="curr_itemid" value="<?php echo $this->itemid; ?>" />