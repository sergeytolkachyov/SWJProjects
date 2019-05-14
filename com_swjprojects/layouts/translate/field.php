<?php
/**
 * @package    SW JProjects Component
 * @version    1.2.0
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 *
 * @var  array  $forms Translates forms array.
 * @var  string $name  Name of the field for which to get the value.
 * @var  string $group Optional dot-separated form group path on which to get the value.
 */

$group        = (isset($group)) ? $group : '';
$languages    = LanguageHelper::getLanguages('lang_code');
?>
<?php foreach ($forms as $code => $form):
	$field = (!empty($form->getField($name, $group))) ? $form->getField($name, $group) : false;
	$language = (!empty($languages[$code])) ? $languages[$code] : false;
	?>
	<?php if ($field && $language): ?>
	<div class="control-group" style="display: none" data-translate-input
		 data-translate="<?php echo $code; ?>"
		 data-id="<?php echo $field->id; ?>"
		 data-name="<?php echo $field->name; ?>">
		<div class="control-label">
			<label id="<?php echo $field->id; ?>-lbl" for="<?php echo $field->id; ?>" class="hasPopover"
				   title="<?php echo Text::_($field->getAttribute('label')); ?>"
				   data-content="<?php echo Text::_($field->getAttribute('description')); ?>">
				<?php echo Text::_($field->getAttribute('label')); ?>
				<sup>
					&#160;<?php echo HTMLHelper::_('image', 'mod_languages/' . $language->image . '.gif', '', null, true); ?>
				</sup>
				<?php if ($field->required): ?>
					<span class="star">&#160;*</span>
				<?php endif; ?>
			</label>
		</div>
		<div class="controls"><?php echo $field->input; ?></div>
	</div>
<?php endif; ?>
<?php endforeach; ?>