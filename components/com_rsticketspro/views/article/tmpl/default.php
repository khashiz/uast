<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

if ($this->params->get('show_page_heading', 1))
{
	?>
	<h1><?php echo !empty($this->article->name) ? $this->escape($this->article->name) : $this->escape($this->params->get('page_heading')); ?></h1>
	<?php
}

echo RSTicketsProHelper::getConfig('kb_load_plugin') ? JHtml::_('content.prepare', $this->article->text) : $this->article->text;