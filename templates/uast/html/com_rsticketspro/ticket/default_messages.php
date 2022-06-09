<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

?>
    <div class="uk-margin-medium-bottom">
        <div class="uk-child-width-1-2 uk-child-width-auto@s uk-grid-small" id="ticket-buttons" data-uk-grid>
            <?php
            if (!$this->isPrint)
            {
                if ($this->canViewHistory)
                {
                    echo RSTicketsProHelper::renderModal('rsticketsproHistoryModal', array(
                        'title' => JText::_('RST_TICKET_VIEW_HISTORY'),
                        'url' 	=> JRoute::_('index.php?option=com_rsticketspro&view=history&id='.$this->ticket->id.'&tmpl=component', false),
                        'height' => 400,
                        'backdrop' => 'static'));
                    ?>
                        <div>
                            <a href="#" class="btn btn-secondary" onclick="<?php echo RSTicketsProHelper::openModal('rsticketsproHistoryModal'); ?>"><i class="icon-calendar"></i> <?php echo JText::_('RST_TICKET_VIEW_HISTORY'); ?></a>
                        </div>
                    <?php
                }
                if ($this->canViewNotes)
                {
                    echo RSTicketsProHelper::renderModal('rsticketsproNotesModal', array(
                        'title'    => JText::_('RST_TICKET_VIEW_NOTES'),
                        'url' 	   => JRoute::_('index.php?option=com_rsticketspro&view=notes&ticket_id='.$this->ticket->id.'&tmpl=component', false),
                        'height'   => 400,
                        'backdrop' => 'static'));
                    ?>
                    <a href="#" class="btn btn-secondary" onclick="<?php echo RSTicketsProHelper::openModal('rsticketsproNotesModal'); ?>"><i class="icon-file"></i> <?php echo $this->ticket->notes ? JText::sprintf('RST_TICKET_VIEW_NOTES_NO', $this->ticket->notes) : JText::_('RST_TICKET_VIEW_NOTES'); ?></a>
                    <?php
                }
                ?>
                    <div>
                        <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&view=ticket&id='.$this->ticket->id.'&tmpl=component&print=1'); ?>" class="uk-button uk-button-default uk-border-rounded uk-box-shadow-small uk-width-1-1" onclick="window.open(this.href,'printWindow','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=600,directories=no,location=no'); return false;"><i class="fas fa-print"></i><?php echo JText::_('RST_TICKET_PRINT'); ?></a>
                    </div>
                <?php
                if ($this->ticket->status_id == RST_STATUS_CLOSED && $this->canOpenTicket)
                {
                    ?>
                        <div>
                            <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=ticket.reopen&id='.$this->ticket->id); ?>" class="uk-button uk-button-success uk-border-rounded uk-box-shadow-small uk-width-1-1"><i class="fas fa-refresh"></i><?php echo JText::_('RST_TICKET_OPEN'); ?></a>
                        </div>
                    <?php
                }
                elseif ($this->ticket->status_id != RST_STATUS_CLOSED && $this->canCloseTicket)
                {
                    ?>
                        <div>
                            <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=ticket.close&id='.$this->ticket->id); ?>" class="uk-button uk-button-danger uk-border-rounded uk-box-shadow-small uk-width-1-1"><i class="fas fa-close"></i><?php echo JText::_('RST_TICKET_CLOSE'); ?></a>
                        </div>
                    <?php
                }
            }
            else
            {
                ?>
                <a href="javascript:void(0)" onclick="window.print();" class="uk-button uk-button-success uk-border-pill uk-box-shadow-small uk-width-1-1"><i class="fas fa-print"></i> <?php echo JText::_('RST_TICKET_PRINT'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
    <div class="uk-margin-medium-bottom">
        <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid>
            <?php
            foreach ($this->ticketMessages as $message)
            {
                $user = $message->user_id != '-1' ? JFactory::getUser($message->user_id) : null;
                $submitter = $message->submitted_by_staff != '0' ? JFactory::getUser($message->submitted_by_staff) : null;

                ?>

                <?php if (is_null($user)) { ?>
                    <div>
                        <div class="uk-alert uk-alert-primary uk-padding-small uk-border-rounded">
                            <p class="uk-text-center uk-text-tiny font f700"><?php echo '<span class="f700">'.fnum($this->showDate($message->date)).'</span> ، '.strip_tags(RSTicketsProHelper::showMessage($message),'<span>'); ?></p>
                        </div>
                        <?php if (!$this->isPrint && !is_null($user)) { ?>
                            <div>
                                <?php
                                if ($this->canEditMessage($message))
                                {
                                    echo RSTicketsProHelper::renderModal('rsticketsproMessageModal' . $message->id, array(
                                        'title'    => JText::_('RST_TICKET_EDIT_MESSAGE'),
                                        'url' 	   => JRoute::_('index.php?option=com_rsticketspro&task=ticketmessage.edit&id='.$message->id.'&tmpl=component', false),
                                        'height'   => 400,
                                        'backdrop' => 'static'));
                                    ?>
                                    <a class="btn btn-secondary" onclick="<?php echo RSTicketsProHelper::openModal('rsticketsproMessageModal' . $message->id); ?>" href="#"><i class="icon-edit"></i> <?php echo JText::_('RST_TICKET_EDIT_MESSAGE'); ?></a>
                                    <?php
                                }
                                if ($this->canDeleteMessage($message))
                                {
                                    ?>
                                    <a class="btn btn-danger" onclick="return confirm(Joomla.JText._('RST_DELETE_TICKET_MESSAGE_CONFIRM'));" href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=ticketmessages.delete&cid='.$message->id . '&ticket_id=' . $message->ticket_id . '&' . JSession::getFormToken() . '=1'); ?>"><i class="icon-delete"></i> <?php echo JText::_('RST_TICKET_DELETE_MESSAGE'); ?></a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                <div>
                    <?php
                    if (!is_null($user))
                    {
                        if ($this->showEmailLink)
                        {
                            $text = JHtml::_('link', 'mailto:' . $this->escape($user->email), $this->escape($user->{$this->userField}));
                        }
                        else
                        {
                            $text = $this->escape($user->{$this->userField});
                        }

                        ?>
                    <?php } ?>
                    <?php if (RSTicketsProHelper::isStaff($message->user_id)) { ?>
                        <div class="uk-grid-small" data-uk-grid>
                            <div class="uk-width-expand"></div>
                            <div class="uk-width-2-3">
                                <div class="uk-background-muted uk-border-rounded uk-padding-small">
                                    <div class="font uk-text-tiny uk-margin-small-bottom f300 uk-text-muted fnum"><?php echo JText::sprintf('TICKET_INFO', '<span class="uk-display-inline-block f500">'.$text.'</span>', '<span class="uk-display-inline-block f500">'.fnum($this->showDate($message->date)).'</span>') ?></div>
                                    <div class="uk-text-justify font f700 uk-text-secondary"><?php echo RSTicketsProHelper::showMessage($message); ?></div>
                                </div>
                            </div>
                            <div class="uk-width-auto uk-text-primary"><i class="far fa-fw fa-2x fa-headset"></i></div>
                        </div>
                    <?php } else { ?>
                        <div class="uk-grid-small" data-uk-grid>
                            <div class="uk-width-auto uk-text-secondary"><i class="far fa-fw fa-2x fa-user-alt"></i></div>
                            <div class="uk-width-2-3">
                                <div class="uk-background-muted uk-border-rounded uk-padding-small">
                                    <div class="font uk-text-tiny uk-margin-small-bottom f300 uk-text-muted fnum"><?php echo JText::sprintf('TICKET_INFO', '<span class="uk-display-inline-block f500">'.$text.'</span>', '<span class="uk-display-inline-block f500">'.fnum($this->showDate($message->date)).'</span>') ?></div>
                                    <div class="uk-text-justify font f700 uk-text-secondary"><?php echo RSTicketsProHelper::showMessage($message); ?></div>
                                </div>
                            </div>
                            <div class="uk-width-expand"></div>
                        </div>
                    <?php } ?>
                    <?php
                    /*
                if (!empty($message->files))
                {
                    ?>
                    <ul>
                        <?php
                        foreach ($message->files as $file)
                        {
                            ?>
                            <li><?php if (!$this->isPrint && $this->canDeleteMessage($message)) { ?><a class="btn btn-danger btn-small btn-sm" href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=ticketmessages.deleteattachment&ticket_id=' . $message->ticket_id . '&cid=' . $file->id . '&' . JSession::getFormToken() . '=1'); ?>" onclick="return confirm(Joomla.JText._('RST_DELETE_TICKET_ATTACHMENT_CONFIRM').replace('%s', this.getAttribute('data-filename')));" data-filename="<?php echo $this->escape($file->filename); ?>"><i class="icon-remove"></i></a><?php } ?> <i class="icon-file"></i> <a href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=ticket.downloadfile&id='.$file->id); ?>"><?php echo JText::sprintf('RST_TICKET_FILE_DOWNLOADS_SMALL', $this->escape($file->filename), $file->downloads); ?></a></li>
                            <?php
                        }
                        ?>
                    </ul>
                    <?php
                }

                if (!$this->isPrint && !is_null($user))
                {
                    ?>
                    <div>
                        <?php
                        if ($this->canEditMessage($message))
                        {
                            echo RSTicketsProHelper::renderModal('rsticketsproMessageModal' . $message->id, array(
                                'title'    => JText::_('RST_TICKET_EDIT_MESSAGE'),
                                'url' 	   => JRoute::_('index.php?option=com_rsticketspro&task=ticketmessage.edit&id='.$message->id.'&tmpl=component', false),
                                'height'   => 400,
                                'backdrop' => 'static'));
                            ?>
                            <a class="btn btn-secondary" onclick="<?php echo RSTicketsProHelper::openModal('rsticketsproMessageModal' . $message->id); ?>" href="#"><i class="icon-edit"></i> <?php echo JText::_('RST_TICKET_EDIT_MESSAGE'); ?></a>
                            <?php
                        }
                        if ($this->canDeleteMessage($message))
                        {
                            ?>
                            <a class="btn btn-danger" onclick="return confirm(Joomla.JText._('RST_DELETE_TICKET_MESSAGE_CONFIRM'));" href="<?php echo JRoute::_('index.php?option=com_rsticketspro&task=ticketmessages.delete&cid='.$message->id . '&ticket_id=' . $message->ticket_id . '&' . JSession::getFormToken() . '=1'); ?>"><i class="icon-delete"></i> <?php echo JText::_('RST_TICKET_DELETE_MESSAGE'); ?></a>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                */
                    ?>
                </div>
                <?php } ?>

            <?php } ?>
        </div>
    </div>

<?php if (!$this->isPrint) { echo $this->loadTemplate('reply'); } ?>