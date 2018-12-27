<?php
/**
 * @package    SW JProjects Component
 * @version    1.0.1
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2018 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;

class SWJProjectsModelProjects extends ListModel
{
	/**
	 * Site default translate language.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $translate = null;

	/**
	 * Constructor.
	 *
	 * @param  array $config An optional associative array of configuration settings.
	 *
	 * @since  1.0.0
	 */
	public function __construct($config = array())
	{
		// Set translate
		$this->translate = ComponentHelper::getParams('com_languages')->get('site', 'en-GB');

		// Add the ordering filtering fields whitelist
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'p.id',
				'title', 'p.title',
				'published', 'state', 'p.state',
				'category', 'category_id', 'c.id', 'e.catid', 'catid', 'category_title', 'cl.title'
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param  string $ordering  An optional ordering field.
	 * @param  string $direction An optional direction (asc|desc).
	 *
	 * @since  1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Set search filter state
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Set published filter state
		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// Set category   filter state
		$category = $this->getUserStateFromRequest($this->context . '.filter.category  ', 'filter_category  ', '');
		$this->setState('filter.category  ', $category);

		// List state information
		$ordering  = empty($ordering) ? 'p.ordering' : $ordering;
		$direction = empty($direction) ? 'asc' : $direction;

		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @param  string $id A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since  1.0.0
	 */
	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.category');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load projects list.
	 *
	 * @return  JDatabaseQuery  Database query to load projects list.
	 *
	 * @since  1.0.0
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select(array('p.*'))
			->from($db->quoteName('#__swjprojects_projects', 'p'));

		// Join over the categories
		$query->select(array('c.id as category_id', 'c.alias as category_alias'))
			->leftJoin($db->quoteName('#__swjprojects_categories', 'c') . ' ON c.id = p.catid');

		// Join over the versions
		$query->select(array('SUM(downloads) as downloads'))
			->leftJoin($db->quoteName('#__swjprojects_versions', 'v') . ' ON v.project_id = p.id');

		// Join over translates
		$translate = $this->translate;
		$query->select(array('t_p.title as title'))
			->leftJoin($db->quoteName('#__swjprojects_translate_projects', 't_p')
				. ' ON t_p.id = p.id AND ' . $db->quoteName('t_p.language') . ' = ' . $db->quote($translate));

		$query->select(array('t_c.title as category_title'))
			->leftJoin($db->quoteName('#__swjprojects_translate_categories', 't_c')
				. ' ON t_c.id = c.id AND ' . $db->quoteName('t_c.language') . ' = ' . $db->quote($translate));

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('p.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(p.state = 0 OR p.state = 1)');
		}

		// Filter by category state
		$category = $this->getState('filter.category');
		if (is_numeric($category))
		{
			$query->where('p.catid = ' . (int) $category);
		}

		// Filter by search
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('p.id = ' . (int) substr($search, 3));
			}
			else
			{
				$sql     = array();
				$columns = array('p.element', 'c.alias', 't_c.title', 'ta_p.title', 'ta_p.introtext', 'ta_p.fulltext');

				foreach ($columns as $column)
				{
					$sql[] = $db->quoteName($column) . ' LIKE '
						. $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				}

				$query->leftJoin($db->quoteName('#__swjprojects_translate_projects', 'ta_p') . ' ON ta_p.id = p.id')
					->where('(' . implode(' OR ', $sql) . ')');
			}
		}

		// Group by
		$query->group(array('p.id'));

		// Add the list ordering clause
		$ordering  = $this->state->get('list.ordering', 'p.ordering');
		$direction = $this->state->get('list.direction', 'asc');
		$query->order($db->escape($ordering) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Method to get an array of projects data.
	 *
	 * @return  mixed  Projects objects array on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public function getItems()
	{
		if ($items = parent::getItems())
		{
			foreach ($items as &$item)
			{
				// Set title
				$item->title = (empty($item->title)) ? $item->element : $item->title;

				// Set category title
				$item->category_title = (empty($item->category_title)) ? $item->category_alias : $item->category_title;
			}
		}

		return $items;
	}
}