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
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldProjects extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'projects';

	/**
	 * Field options array.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $_options = null;

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since  1.0.0
	 */
	protected function getOptions()
	{
		if ($this->_options === null)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select(array('p.id', 'p.element'))
				->from($db->quoteName('#__swjprojects_projects', 'p'));

			// Join over translates
			$translate = ComponentHelper::getParams('com_languages')->get('site', 'en-GB');
			$query->select(array('t_p.title as title'))
				->leftJoin($db->quoteName('#__swjprojects_translate_projects', 't_p')
					. ' ON t_p.id = p.id AND ' . $db->quoteName('t_p.language') . ' = ' . $db->quote($translate));

			// Group by
			$query->group(array('p.id'));

			// Add the list ordering clause
			$query->order($db->escape('p.ordering') . ' ' . $db->escape('asc'));

			$items = $db->setQuery($query)->loadObjectList('id');

			// Prepare options
			$options = parent::getOptions();
			foreach ($items as $i => $item)
			{
				$option        = new stdClass();
				$option->value = $item->id;
				$option->text  = (!empty($item->title)) ? $item->title : $item->element;

				$options[] = $option;
			}

			$this->_options = $options;
		}

		return $this->_options;
	}
}