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

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class SWJProjectsControllerCategories extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_SWJPROJECTS_CATEGORIES';

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return  boolean  False on failure or error, true on success.
	 *
	 * @since  1.0.0
	 */
	public function rebuild()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$this->setRedirect(Route::_('index.php?option=com_swjprojects&view=categories', false));

		if ($this->getModel()->rebuild())
		{
			// Rebuild succeeded
			$this->setMessage(Text::_('COM_SWJPROJECTS_CATEGORIES_REBUILD_SUCCESS'));

			return true;
		}

		// Rebuild failed
		$this->setMessage(Text::_('COM_SWJPROJECTS_CATEGORIES_REBUILD_FAILURE'));

		return false;
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param  string $name   The model name.
	 * @param  string $prefix The class prefix.
	 * @param  array  $config The array of possible config values.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel  A model object.
	 *
	 * @since  1.0.0
	 */
	public function getModel($name = 'Category', $prefix = 'SWJProjectsModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}