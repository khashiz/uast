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
?>
<?php echo $this->globalMessage; ?>
<div>
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1 class="font uk-h4 f900"><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
    <?php } ?>
    <form id="rsticketspro_form" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=tickets'.$this->itemid); ?>" method="post" name="searchForm" class="searchTicketsForm">
        <div class="uk-grid-small" data-uk-grid>
        <?php
        foreach ($this->form->getFieldsets() as $fieldset)
        {
            echo '<div class="uk-width-1-1">'.$this->form->renderFieldset($fieldset->name).'</div>';
        }
        ?>
        <div class="uk-width-1-1 uk-width-1-3@s form-actions">
            <button type="submit" class="uk-button uk-button-primary uk-button-large uk-border-rounded uk-box-shadow-small uk-width-1-1"><i class="fas fa-search"></i><?php echo JText::_('RST_SEARCH'); ?></button>
            <?php /* if (!$this->advanced) { ?>
                <a class="btn btn-secondary" href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=search&advanced=true'); ?>"><?php echo JText::_('RST_OPEN_ADVANCED_SEARCH'); ?></a>
            <?php } */ ?>
        </div>
        </div>

        <?php
        if ($this->show_footer)
        {
            echo $this->footer;
        }

        if (!$this->advanced)
        {
            ?>
            <input type="hidden" name="customer" id="customer" value="" />
            <input type="hidden" name="staff" id="staff" value="" />
            <input type="hidden" name="status_id" id="status_id" value="" />
            <?php
        }
        ?>
        <input type="hidden" name="option" value="com_rsticketspro" />
        <input type="hidden" name="task" value="search" />
    </form>
</div>