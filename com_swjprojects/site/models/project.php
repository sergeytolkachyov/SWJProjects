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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class SWJProjectsModelProject extends ItemModel
{
	/**
	 * Model context string.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $_context = 'swjprojects.project';

	/**
	 * Category parent object.
	 *
	 * @var  object
	 *
	 * @since  1.0.0
	 */
	protected $_categoryParent = null;

	/**
	 * Translates languages.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $translates = null;

	/**
	 * Path to files.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $filesPath = null;

	/**
	 * Constructor.
	 *
	 * @param  array $config An optional associative array of configuration settings.
	 *
	 * @throws  Exception
	 *
	 * @since  1.0.0
	 */
	public function __construct($config = array())
	{
		$params = ComponentHelper::getParams('com_swjprojects');

		// Set files paths
		$root            = $params->get('files_folder');
		$this->filesPath = array(
			'root'     => $root,
			'versions' => $root . '/versions'
		);

		// Set translates
		$this->translates = array(
			'current' => Factory::getLanguage()->getTag(),
			'default' => ComponentHelper::getParams('com_languages')->get('site', 'en-GB'),
		);

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @throws  Exception
	 *
	 * @since  1.0.0
	 */
	protected function populateState()
	{
		$app = Factory::getApplication('site');

		// Set request states
		$this->setState('project.id', $app->input->getInt('id', 0));
		$this->setState('category.id', $app->input->getInt('catid', 1));

		// Merge Global and Menu Item params into new object
		$params     = $app->getParams();
		$menuParams = new Registry();
		$menu       = $app->getMenu()->getActive();
		if ($menu)
		{
			$menuParams->loadString($menu->getParams());
		}
		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);

		// Set params state
		$this->setState('params', $mergedParams);

		// Set published && debug state
		if ($app->input->getInt('debug', 0))
		{
			$this->setState('filter.published', array(0, 1));
			$this->setState('debug', 1);
		}
		else
		{
			$this->setState('filter.published', 1);
		}
	}

	/**
	 * Method to get project data.
	 *
	 * @param  integer $pk The id of the project.
	 *
	 * @throws  Exception
	 *
	 * @return  object|boolean|Exception  Project object on success, false or exception on failure.
	 *
	 * @since  1.0.0
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('project.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true)
					->select(array('p.*'))
					->from($db->quoteName('#__swjprojects_projects', 'p'))
					->where('p.id = ' . (int) $pk);

				// Join over the categories
				$query->select(array('c.id as category_id', 'c.alias as category_alias'))
					->leftJoin($db->quoteName('#__swjprojects_categories', 'c') . ' ON c.id = p.catid');

				// Join over current translates
				$current = $this->translates['current'];
				$query->select(array('t_p.*', 'p.id as id'))
					->leftJoin($db->quoteName('#__swjprojects_translate_projects', 't_p')
						. ' ON t_p.id = p.id AND ' . $db->quoteName('t_p.language') . ' = ' . $db->quote($current));

				$query->select(array('t_c.title as category_title'))
					->leftJoin($db->quoteName('#__swjprojects_translate_categories', 't_c')
						. '  ON t_c.id = c.id AND ' . $db->quoteName('t_c.language') . ' = ' . $db->quote($current));

				// Join over default translates
				$default = $this->translates['default'];
				if ($current != $default)
				{
					$query->select(array('td_p.title as default_title'))
						->leftJoin($db->quoteName('#__swjprojects_translate_projects', 'td_p')
							. ' ON td_p.id = p.id AND ' . $db->quoteName('td_p.language') . ' = ' . $db->quote($default));

					$query->select(array('td_c.title as default_category_title'))
						->leftJoin($db->quoteName('#__swjprojects_translate_categories', 'td_c')
							. ' ON td_c.id = c.id AND ' . $db->quoteName('td_c.language') . ' = ' . $db->quote($default));
				}

				// Select last version
				$subQuery = $db->getQuery(true)
					->select(array('CONCAT(lv.id, ":", lv.alias, "|", lv.major, ".", lv.minor, ".", lv.micro)'))
					->from($db->quoteName('#__swjprojects_versions', 'lv'))
					->where('lv.project_id = p.id')
					->where($db->quoteName('lv.tag') . ' = ' . $db->quote('stable'))
					->order($db->escape('lv.major') . ' ' . $db->escape('desc'))
					->order($db->escape('lv.minor') . ' ' . $db->escape('desc'))
					->order($db->escape('lv.micro') . ' ' . $db->escape('desc'))
					->setLimit(1);
				$query->select('(' . $subQuery->__toString() . ') as last_version');

				// Filter by published state
				$published = $this->getState('filter.published');
				if (is_numeric($published))
				{
					$query->where('p.state = ' . (int) $published)
						->where('c.state = ' . (int) $published);
				}
				elseif (is_array($published))
				{
					$published = ArrayHelper::toInteger($published);
					$published = implode(',', $published);

					$query->where('p.state IN (' . $published . ')')
						->where('c.state IN (' . $published . ')');
				}

				$data = $db->setQuery($query)->loadObject();

				if (empty($data))
				{
					throw new Exception(Text::_('COM_SWJPROJECTS_ERROR_CATEGORY_NOT_FOUND'), 404);
				}

				// Set default translates data
				if ($this->translates['current'] != $this->translates['default'])
				{
					$data->title = (empty($data->title)) ? $data->default_title : $data->title;

					$data->category_title = (empty($data->category_title)) ? $data->default_category_title
						: $data->category_title;
				}

				// Set title
				$data->title = (empty($data->title)) ? $data->element : $data->title;

				// Set introtext
				$data->introtext = nl2br($data->introtext);

				// Set joomla
				$data->joomla = new Registry($data->joomla);

				// Set urls
				$data->urls = new Registry($data->urls);

				// Set link
				$data->slug     = $data->id . ':' . $data->alias;
				$data->cslug    = $data->category_id . ':' . $data->category_alias;
				$data->link     = Route::_(SWJProjectsHelperRoute::getProjectRoute($data->slug, $data->cslug));
				$data->versions = Route::_(SWJProjectsHelperRoute::getVersionsRoute($data->slug, $data->cslug));
				$data->download = Route::_(SWJProjectsHelperRoute::getDownloadRoute(null, $data->id));

				// Set category
				$data->category        = new stdClass();
				$data->category->id    = $data->category_id;
				$data->category->title = (!empty($data->category_title)) ? $data->category_title : $data->category_alias;
				$data->category->alias = $data->category_alias;
				$data->category->slug  = $data->cslug;
				$data->category->link  = Route::_(SWJProjectsHelperRoute::getProjectsRoute($data->cslug));

				// Set version
				$data->version = false;
				if (!empty($data->last_version))
				{
					$data->version = new stdClass();
					list($data->version->slug, $data->version->version) = explode('|', $data->last_version, 2);
					list($data->version->id, $data->version->alias) = explode(':', $data->version->slug, 2);
					$data->version->link = Route::_(SWJProjectsHelperRoute::getVersionRoute($data->version->slug,
						$data->slug, $data->cslug));
				}

				// Set params
				$params       = $data->params;
				$data->params = clone $this->getState('params');
				$data->params->merge($params);

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					throw new Exception(Text::_($e->getMessage()), 404);
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Method to get category parent data.
	 *
	 * @param  integer $pk The id of the version.
	 *
	 * @throws  Exception
	 *
	 * @return  object|boolean|Exception  Category object on success, false or exception on failure.
	 *
	 * @since  1.0.0
	 */
	public function getCategoryParent($pk = null)
	{
		if (empty($pk)) return false;

		if ($this->_categoryParent === null)
		{
			$this->_categoryParent = array();
		}

		if (!isset($this->_categoryParent[$pk]))
		{
			try
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true)
					->select(array('c.id', 'c.alias'))
					->from($db->quoteName('#__swjprojects_categories', 'child'))
					->innerJoin($db->quoteName('#__swjprojects_categories', 'c') . ' ON c.id = child.parent_id')
					->where('child.id = ' . (int) $pk);

				// Join over current translates
				$current = $this->translates['current'];
				$query->select(array('t_c.title as title'))
					->leftJoin($db->quoteName('#__swjprojects_translate_categories', 't_c')
						. '  ON t_c.id = c.id AND ' . $db->quoteName('t_c.language') . ' = ' . $db->quote($current));

				// Join over default translates
				$default = $this->translates['default'];
				if ($current != $default)
				{
					$query->select(array('td_c.title as default_title'))
						->leftJoin($db->quoteName('#__swjprojects_translate_categories', 'td_c')
							. ' ON td_c.id = c.id AND ' . $db->quoteName('td_c.language') . ' = ' . $db->quote($default));
				}

				// Filter by published state
				$published = $this->getState('filter.published');
				if (is_numeric($published))
				{
					$query->where('c.state = ' . (int) $published)
						->where('child.state = ' . (int) $published);
				}
				elseif (is_array($published))
				{
					$published = ArrayHelper::toInteger($published);
					$published = implode(',', $published);

					$query->where('c.state IN (' . $published . ')')
						->where('child.state IN (' . $published . ')');
				}

				$data = $db->setQuery($query)->loadObject();

				if (empty($data))
				{
					throw new Exception(Text::_('COM_SWJPROJECTS_ERROR_CATEGORY_NOT_FOUND'), 404);
				}

				// Set default translates data
				if ($this->translates['current'] != $this->translates['default'])
				{
					$data->title = (empty($data->title)) ? $data->default_title : $data->title;
				}

				// Set link
				$data->link = Route::_(SWJProjectsHelperRoute::getProjectsRoute($data->id . ':' . $data->alias));

				// Set title
				$data->title = (!empty($data->title)) ? $data->title : $data->alias;

				$this->_categoryParent[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					throw new Exception(Text::_($e->getMessage()), 404);
				}
				else
				{
					$this->setError($e);
					$this->_categoryParent[$pk] = false;
				}
			}
		}

		return $this->_categoryParent[$pk];
	}
}