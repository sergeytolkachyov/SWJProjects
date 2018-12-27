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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;

abstract class SWJProjectsHelperAssociation
{
	/**
	 * Item associations.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected static $_associations = array();

	/**
	 * Method to get the associations for a given item.
	 *
	 * @param  integer $id         Id of the item.
	 * @param  string  $view       Name of the view.
	 * @param  integer $catid      Id of the category.
	 * @param  integer $project_id Id of the project.
	 *
	 * @throws  Exception
	 *
	 * @return  array  Array of associations for the item.
	 *
	 * @since  1.0.0
	 */
	public static function getAssociations($id = 0, $view = null, $catid = 0, $project_id = 0)
	{
		$app        = Factory::getApplication();
		$id         = (!empty($id)) ? $id : $app->input->getInt('id', 0);
		$view       = (!empty($view)) ? $view : $app->input->getCmd('view', '');
		$catid      = (!empty($catid)) ? $catid : $app->input->getInt('catid', 1);
		$project_id = (!empty($project_id)) ? $project_id : $app->input->getInt('project_id', 0);
		$hash       = md5(serialize(array($id, $view, $catid, $project_id)));

		if (!isset(self::$_associations[$hash]))
		{
			$associations = array();
			foreach (array_keys(LanguageHelper::getLanguages('lang_code')) as $code)
			{
				$link = false;
				if ($view == 'version')
				{
					$link = SWJProjectsHelperRoute::getVersionRoute($id, $project_id, $catid);
				}

				if ($view == 'versions')
				{
					$link = SWJProjectsHelperRoute::getVersionsRoute($id, $catid);
				}

				if ($view == 'projects')
				{
					$link = SWJProjectsHelperRoute::getProjectsRoute($id);
				}

				if ($view == 'project')
				{
					$link = SWJProjectsHelperRoute::getProjectRoute($id, $catid);
				}

				if ($link)
				{
					$associations[$code] = $link;
				}
			}
			self::$_associations[$hash] = $associations;
		}

		return self::$_associations[$hash];
	}
}