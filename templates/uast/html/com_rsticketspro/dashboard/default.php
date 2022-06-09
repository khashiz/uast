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

$app  = JFactory::getApplication();
$user = JFactory::getUser();
$params = $app->getTemplate(true)->params;
$menu = $app->getMenu();
$active = $menu->getActive();
?>
<?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1 class="font uk-h4 f900"><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
<?php } ?>
<?php echo JText::_(RSTicketsProHelper::getConfig('global_message')); ?>
<form method="post" action="<?php echo $this->search_link; ?>">
	<div id="rsticketspro_dashboard_search" class="uk-hidden">
		<div class="btn-group">
			<input type="text" placeholder="<?php echo $this->escape(JText::_('RST_SEARCH_HELPDESK')); ?>" class="form-control input-xlarge" name="search" autocomplete="off" id="rsticketspro_searchinp" />
			<button type="submit" class="btn btn-primary"><i id="rstickets_search_icon" class="icon-search"></i><?php echo JHtml::_('image', 'com_rsticketspro/loading.gif', '', array('id' => 'rsticketspro_loading', 'style' => 'display:none;'), true); ?></button>
		</div>
	</div>
    <div class="uk-margin-medium-bottom">
        <div class="uk-child-width-1-1 uk-child-width-1-3@s uk-grid-divider" data-uk-grid>
            <div class="uk-text-center">
                <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=submit'); ?>" class="uk-padding uk-border-rounded uk-display-block listItemLink hover">
                    <span class="uk-display-block"><i class="far fa-message-pen fa-fw fa-4x"></i></span>
                    <span class="uk-display-block uk-margin-top font f700 uk-text-small"><?php echo JText::_('RST_SUBMIT_TICKET'); ?></span>
                </a>
                <p class="uk-hidden"><?php echo JText::_($this->params->get('submit_ticket_desc')); ?></p>
            </div>
            <div class="uk-text-center">
                <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=tickets'); ?>" class="uk-padding uk-border-rounded uk-display-block listItemLink hover">
                    <span class="uk-display-block"><i class="far fa-message-lines fa-fw fa-4x"></i></span>
                    <span class="uk-display-block uk-margin-top font f700 uk-text-small"><?php echo JText::_('RST_VIEW_TICKETS'); ?></span>
                </a>
                <p class="uk-hidden"><?php echo JText::_($this->params->get('view_tickets_desc')); ?></p>
            </div>
            <div class="uk-text-center">
                <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=search'); ?>" class="uk-padding uk-border-rounded uk-display-block listItemLink hover">
                    <span class="uk-display-block"><i class="far fa-search fa-fw fa-4x"></i></span>
                    <span class="uk-display-block uk-margin-top font f700 uk-text-small"><?php echo JText::_('RST_SEARCH_TICKETS'); ?></span>
                </a>
                <p class="uk-hidden"><?php echo JText::_($this->params->get('search_tickets_desc')); ?></p>
            </div>
        </div>
    </div>
    <hr class="uk-divider-icon uk-margin-removev-top uk-margin-medium-bottom">

	<?php
	if ($this->params->get('show_kb', 1))
	{
		?>
		<div id="rsticketspro_dashboard_knowledgebase">
			<div>
				<h3 class="font uk-h4 f900"><?php echo JText::_('RST_KNOWLEDGEBASE'); ?></h3>

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
				<h3 class="font uk-h4 f900"><?php echo JText::_('RST_MY_LAST_TICKETS'); ?></h3>
				<?php
				if ($this->user->get('guest'))
				{
					?>
                        <div class="uk-alert uk-alert-warning uk-padding-small uk-border-rounded">
                            <div class="uk-grid-small" data-uk-grid>
                                <div class="uk-width-1-1 uk-width-expand@s uk-flex uk-flex-middle"><p class="uk-margin-remove font f700 uk-text-small uk-flex uk-flex-middle"><i class="fas fa-exclamation-triangle fa-2x uk-margin-small-left"></i><?php echo JText::_('RST_YOU_HAVE_TO_BE_LOGGED_IN'); ?></p></div>
                                <div class="uk-width-1-1 uk-width-auto@s"><a href="<?php echo $this->login_link; ?>" class="uk-button uk-button-default uk-border-rounded uk-box-shadow-small uk-background-white"><i class="fas fa-sign-in-alt"></i><?php echo JText::_('RST_CLICK_HERE_TO_LOGIN'); ?></a></div>
                            </div>
                        </div>
					<?php
				}
				else
				{
					if (count($this->tickets))
					{
						?>
                            <div class="uk-child-width-1-1 uk-child-width-1-3@s uk-grid-small" data-uk-grid>
                                <?php
                                foreach ($this->tickets as $ticket)
                                {
                                    $hasReply = isset($ticket->message);
                                    ?>
                                        <div>
                                            <div class="uk-border-rounded uk-box-shadow-small uk-padding-small">
                                                <a class="uk-text-small uk-text-secondary font f700 uk-text-center uk-display-block uk-margin-small-top" href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&id='.$ticket->id.':'.JFilterOutput::stringURLSafe($ticket->subject)); ?>"><?php echo '<span class="uk-hidden@s">'.JText::_('RST_TICKET_SUBJECT').'&ensp;:&ensp;</span>'.$this->escape($ticket->subject); ?></a>
                                                <span class="uk-text-center uk-text-small font f700 uk-text-<?php if ($ticket->status_name == 'TICKET_STATUS_ANSWERED' || $ticket->status_name == 'TICKET_STATUS_PENDING') {echo 'success';} elseif ($ticket->status_name == 'TICKET_STATUS_CLOSED') {echo 'danger';} else {echo 'primary';} ?> uk-display-block uk-margin"><?php echo $this->escape(JText::_($ticket->status_name)); ?></span>
                                                <div>
                                                    <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticket&id='.$ticket->id.':'.JFilterOutput::stringURLSafe($ticket->subject)); ?>" class="uk-button uk-button-primary uk-border-rounded uk-box-shadow-small uk-width-1-1"><i class="fas fa-info-circle"></i><?php echo JText::_('VIEW_TICKET'); ?></a>
                                                </div>
                                            </div>
                                        </div>
                                <?php } ?>
                            </div>
						<?php
					}
					else
					{
						?>
                        <div class="uk-alert uk-alert-warning uk-padding-small uk-border-rounded">
                            <div class="uk-grid-small" data-uk-grid>
                                <div class="uk-width-1-1 uk-width-expand@s uk-flex uk-flex-middle"><p class="uk-margin-remove font f700 uk-text-small uk-flex uk-flex-middle"><i class="fas fa-exclamation-triangle fa-2x uk-margin-small-left"></i><?php echo JText::_('RST_NO_RECENT_ACTIVITY'); ?></p></div>
                            </div>
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
<input type="hidden" name="kb_itemid" value="<?php echo $this->kb_itemid; ?>" />
<input type="hidden" name="curr_itemid" value="<?php echo $this->itemid; ?>" />