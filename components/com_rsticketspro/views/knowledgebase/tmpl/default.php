<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

if (!empty($this->category->name))
{
	?>
	<h1><?php echo $this->escape($this->category->name); ?></h1>
	<?php
}
else
{
	?>
	<h1><?php echo $this->escape($this->params->get('page_heading', $this->params->get('page_title'))); ?></h1>
	<?php
}

if ($this->params->def('show_description', 1))
{
	echo $this->category->description;
}

if (count($this->categories))
{
	?>
	<div class="rst_categories">
	<?php
	foreach ($this->categories as $category)
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
		<div class="well well-small">
			<strong><?php echo $thumb; ?> <a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase&cid='.$category->id.':'.JFilterOutput::stringURLSafe($category->name)); ?>"><?php echo $this->escape($category->name); ?></a></strong>
			<?php
			if ($this->params->def('show_description', 1) && $category->description)
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
	<?php
}
?>

<form action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase'.($this->cid ? '&cid='.$this->cid.':'.JFilterOutput::stringURLSafe($this->category->name) : '')); ?>" method="post" name="adminForm" id="adminForm">
	<?php
	if ($this->params->get('filter', 1) || $this->is_filter_active || $this->params->get('show_pagination_limit', 1))
	{
		?>
		<fieldset class="com-rsticketspro-kb-filter">
			<?php
			if ($this->params->get('filter', 1) || $this->is_filter_active)
			{
				?>
				<div class="btn-group pull-left float-left">
					<label class="filter-search-lbl element-invisible" for="filter-search">
						<?php echo JText::_('RST_FILTER').'&#160;'; ?>
					</label>
					<input type="text" class="form-control" name="search" id="filter-search" value="<?php echo $this->escape($this->filter_word); ?>" title="<?php echo JText::_('RST_FILTER'); ?>" placeholder="<?php echo JText::_('RST_FILTER'); ?>" />
					<button type="submit" class="btn btn-primary"><?php echo JText::_('RST_SEARCH'); ?></button>
					<?php
					if (strlen($this->filter_word))
					{
						?>
						<button type="button" class="btn btn-danger" onclick="document.getElementById('filter-search').value=''; this.form.submit();"><?php echo JText::_('RST_CLEAR'); ?></button>
						<?php
					}
					?>
				</div>
				<?php
			}

			if ($this->params->get('show_pagination_limit', 1))
			{
				?>
				<div class="btn-group pull-right float-right">
					<label for="limit" class="element-invisible">
						<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<?php
			}
			?>
			<div class="clearfix"></div>
		</fieldset>
		<?php
	}

	if (empty($this->items))
	{
		?>
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo JText::_('INFO'); ?></span>
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		<?php
	}
	else
	{
		?>
		<table class="table table-striped table-bordered table-hover">
			<?php
			if ($this->params->get('show_headings', 1))
			{
				?>
				<thead>
					<tr>
						<th nowrap="nowrap" style="width: 1%;"><?php echo JText::_('#'); ?></th>
						<th><?php echo JHtml::_('grid.sort', 'RST_KB_ARTICLE_NAME', 'name', $this->sortOrder, $this->sortColumn); ?></th>
						<?php
						if ($this->params->get('show_hits', 0))
						{
							?>
							<th nowrap="nowrap" style="width: 1%;"><?php echo JHtml::_('grid.sort', 'RST_KB_ARTICLE_HITS', 'hits', $this->sortOrder, $this->sortColumn); ?></th>
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
				<tr>
					<td nowrap="nowrap" style="width: 1%;">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<a href="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=article&cid='.$item->id.':'.JFilterOutput::stringURLSafe($item->name)); ?>">
							<?php echo $item->name != '' ? $item->name : JText::_('RST_NO_TITLE'); ?>
						</a>
						<?php
						if ($this->isHot($item->hits))
						{
							?>
							<em class="rst_hot"><?php echo JText::_('RST_HOT'); ?></em>
							<?php
						}
						?>
					</td>
					<?php
					if ($this->params->get('show_hits', 0))
					{
						?>
						<td nowrap="nowrap" style="width: 1%;">
							<span class="badge badge-info"><?php echo $item->hits; ?></span>
						</td>
						<?php
					}
					?>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
		if ($this->params->get('show_pagination', 1))
		{
			?>
			<div class="pagination<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
			<?php
		}
	}
	?>

	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_rsticketspro" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortOrder; ?>" />
</form>