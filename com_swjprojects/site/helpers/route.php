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

use Joomla\CMS\Helper\RouteHelper;

class SWJProjectsHelperRoute extends RouteHelper
{
	/**
	 * Fetches jupdate route.
	 *
	 * @param  int    $project_id The id of the project.
	 * @param  string $element    The element of the project.
	 *
	 * @return  string  Joomla update server view link.
	 *
	 * @since  1.0.0
	 */
	public static function getJUpdateRoute($project_id = null, $element = null)
	{
		$link = 'index.php?option=com_swjprojects&view=jupdate&key=1';

		if (!empty($project_id))
		{
			$link .= '&project_id=' . $project_id;
		}

		if (!empty($element))
		{
			$link .= '&element=' . $element;
		}

		return $link;
	}

	/**
	 * Fetches download route.
	 *
	 * @param  int    $version_id The id of the version.
	 * @param  int    $project_id The id of the project.
	 * @param  string $element    The element of the project.
	 *
	 * @return  string  Download link.
	 *
	 * @since  1.0.0
	 */
	public static function getDownloadRoute($version_id = null, $project_id = null, $element = null)
	{
		$link = 'index.php?option=com_swjprojects&task=download';

		if (!empty($version_id))
		{
			$link .= '&version_id=' . $version_id;
		}

		if (!empty($project_id))
		{
			$link .= '&project_id=' . $project_id;
		}

		if (!empty($element))
		{
			$link .= '&element=' . $element;
		}

		return $link;
	}

	/**
	 * Fetches version route.
	 *
	 * @param  int $id         The id of the version.
	 * @param  int $project_id The id of the project.
	 * @param  int $catid      The id of the category.
	 *
	 * @return  string  Version view link.
	 *
	 * @since  1.0.0
	 */
	public static function getVersionRoute($id = null, $project_id = null, $catid = null)
	{
		$link = 'index.php?option=com_swjprojects&view=version';

		if (!empty($id))
		{
			$link .= '&id=' . $id;
		}

		if (!empty($project_id))
		{
			$link .= '&project_id=' . $project_id;
		}

		if (!empty($catid))
		{
			$link .= '&catid=' . $catid;
		}

		return $link;
	}

	/**
	 * Fetches versions route.
	 *
	 * @param  int $id    The id of the project.
	 * @param  int $catid The id of the category.
	 *
	 * @return  string  Versions view link.
	 *
	 * @since  1.0.0
	 */
	public static function getVersionsRoute($id = null, $catid = null)
	{
		$link = 'index.php?option=com_swjprojects&view=versions';

		if (!empty($id))
		{
			$link .= '&id=' . $id;
		}

		if (!empty($catid))
		{
			$link .= '&catid=' . $catid;
		}

		return $link;
	}

	/**
	 * Fetches project route.
	 *
	 * @param  int $id    The id of the project.
	 * @param  int $catid The id of the category.
	 *
	 * @return  string  Project view link.
	 *
	 * @since  1.0.0
	 */
	public static function getProjectRoute($id = null, $catid = null)
	{
		$link = 'index.php?option=com_swjprojects&view=project';

		if (!empty($id))
		{
			$link .= '&id=' . $id;
		}

		if (!empty($catid))
		{
			$link .= '&catid=' . $catid;
		}

		return $link;
	}

	/**
	 * Fetches Projects route.
	 *
	 * @param  int $id The id of the category.
	 *
	 * @return  string  Projects view link.
	 *
	 * @since  1.0.0
	 */
	public static function getProjectsRoute($id = null)
	{
		$link = 'index.php?option=com_swjprojects&view=projects';

		if (!empty($id))
		{
			$link .= '&id=' . $id;
		}

		return $link;
	}
}