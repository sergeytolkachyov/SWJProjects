<?php
/**
 * @package    SW JProjects Component
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2020 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

class SWJProjectsHelper extends ContentHelper
{
	/**
	 * Configure the linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @since  1.0.0
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(Text::_('COM_SWJPROJECTS_VERSIONS'),
			'index.php?option=com_swjprojects&view=versions',
			$vName == 'versions');

		JHtmlSidebar::addEntry(Text::_('COM_SWJPROJECTS_PROJECTS'),
			'index.php?option=com_swjprojects&view=projects',
			$vName == 'projects');

		JHtmlSidebar::addEntry(Text::_('COM_SWJPROJECTS_KEYS'),
			'index.php?option=com_swjprojects&view=keys',
			$vName == 'keys');

		JHtmlSidebar::addEntry(Text::_('COM_SWJPROJECTS_DOCUMENTATION'),
			'index.php?option=com_swjprojects&view=documentation',
			$vName == 'documentation');

		JHtmlSidebar::addEntry(Text::_('COM_SWJPROJECTS_CATEGORIES'),
			'index.php?option=com_swjprojects&view=categories',
			$vName == 'categories');
	}
}