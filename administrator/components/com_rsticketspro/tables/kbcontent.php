<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RsticketsproTableKbcontent extends JTable
{
	public $id = null;
	public $name = '';
	public $text = '';
	public $category_id = 0; // 0 - uncategorised
	public $meta_description = '';
	public $meta_keywords = '';
	public $private = 0;
	public $from_ticket_id = 0;
	public $hits = 0;
	public $created = null;
	public $modified = null;
	public $published = null;
	public $ordering = null;
	
	public function __construct(& $db)
	{
		parent::__construct('#__rsticketspro_kb_content', 'id', $db);
	}

	public function check()
	{
		$db = $this->getDbo();

		if (!$this->id && !$this->ordering)
		{
			$this->ordering = $this->getNextOrder($db->qn('category_id') . ' = ' . $db->q($this->category_id));
		}

		if (!$this->id)
		{
			$this->created = JFactory::getDate()->toSql();
			$this->modified = $db->getNullDate();
		}
		else
		{
			$this->modified = JFactory::getDate()->toSql();
		}

		return true;
	}
}