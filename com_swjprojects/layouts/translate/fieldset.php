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

use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);

/**
 * Layout variables
 * -----------------
 *
 * @var  array  $forms Translates forms array.
 * @var  string $name  Name of the fieldset for which to get the values.
 *
 */

$form = current($forms);
foreach ($form->getFieldSet($name) as $field)
{
	echo LayoutHelper::render('components.swjprojects.translate.field', array(
		'forms' => $forms,
		'name'  => $field->getAttribute('name'),
		'group' => $field->group));
}