<?php
/**
 * @package    logregsms
 * @subpackage F:
 * @author     Mohammad Miri {@link joomina.ir}
 * @author     Created on 02-Dec-2015
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');


use Joomla\Utilities\ArrayHelper;

//-- Import the class JModelList
jimport('joomla.application.component.modellist');

/**
 * logregsmsList Model.
 *
 * @package logregsms
 * @subpackage Models
 */
class logregsmsModelSmsArchives extends JModelList
{
	
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'date', 'a.date',
				'to', 'a.to',
				'from', 'a.from',
				'result', 'a.result'
			);

			if (JLanguageAssociations::isEnabled())
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}// function
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'a.id', $direction = 'DESC')
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$created_on = $this->getUserStateFromRequest($this->context . '.filter.created_on', 'filter_created_on');
		$this->setState('filter.created_on', $created_on);
				
		
		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');

		return parent::getStoreId($id);
	}
	
    /**
     * Method to build an SQL query to load the list data.
     * Funktion um einen SQL Query zu erstellen der die Daten für die Liste läd.
     *
     * @return string SQL query
     */
    protected function getListQuery()
    {
		
    	// Ein Datenbankobjekt beziehen.
			$db = JFactory::getDBO();

			// Ein neues (leeres) Queryobjekt beziehen.
			$query = $db->getQuery(true);

			$query->select(
				$this->getState(
					'list.select','a.*'
				)
			);
      $query->from('#__logregsms_smsarchives AS a');

			// Filter by created on date
			$created_on = $this->getState('filter.created_on');
			if (!empty($created_on) && $created_on != "0000-00-00 00:00:00")
			{
				$query->where('a.created_on = ' . $db->quote($created_on));
			}
		
			// Filter by search in title.
			$search = $this->getState('filter.search');
			if (!empty($search))
			{
				if (stripos($search, 'id:') === 0)
				{
					$query->where('a.id = ' . (int) substr($search, 3));
				}
				else
				{
					$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
					$query->where('(a.message LIKE ' . $search . ' || a.to LIKE ' . $search . ' || a.from LIKE ' . $search . ' || a.result LIKE ' . $search . ')');
				}
			}
		
			// Add the list ordering clause
			$listOrdering = $this->getState('list.ordering', 'a.id');
			$listDirn = $db->escape($this->getState('list.direction', 'DESC'));
			$query->order($listOrdering . ' ' . $listDirn);
			// Group by on Categories for JOIN with component tables to count items
		
    	return $query;
    }
	
		public function getItems() {
			$items = parent::getItems();
			return $items;
    }
}
