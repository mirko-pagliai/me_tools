<?php
App::uses('FormHelper', 'View/Helper');

/**
 * Provides functionalities for forms.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = array('Form' => array('className' => 'MeTools.MeForm'));
 * </code>
 * 
 * MeFormHelper extends {@link http://api.cakephp.org/2.4/class-FormHelper.html FormHelper}
 *
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools.View.Helper
 */
class MeFormHelper extends FormHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));

	/**
	 * Creates a simple button. Rewrites <i>$this->Form->button()</i>
	 * @param string $caption The button label or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function button($caption, $options=array()) {
		//"type" option default "button"
		$options['type'] = empty($options['type']) ? 'button' : $options['type'];

		//"class" option default "btn"
		$options['class'] = empty($options['class']) ? 'btn' : $this->Html->cleanAttribute($options['class'].' btn');

		//Adds an icon to the label, if the "icon" option exists
		$caption = !empty($options['icon']) ? $this->Html->icon($options['icon']).$caption : $caption;
		unset($options['icon']);

		//Adds the "tooltip" rel
		$options['rel'] = empty($options['rel']) ? 'tooltip' : $this->Html->cleanAttribute($options['rel'].' tooltip');

		return parent::button($caption, $options);
	}
	
	/**
	 * Creates a textarea for CKEditor. It uses the <i>input()</i> method.
	 * 
	 * To add the script for CKEditor, you should use the <i>Library</i> helper.
	 * 
	 * To know the options to use with datepicker, please refer to the `README` file.
	 * @param string $fieldName Field name. Should be "Modelname.fieldname"
	 * @param array $options Options
	 * @return string Html
	 */
	public function ckeditor($fieldName, $options=array()) {
		//Adds "wysiwyg" to the class
		$options['class'] = empty($options['class']) ? 'wysiwyg' : $options['class'].' wysiwyg';
		
		$options['label'] = false;
		
		//Set the "require" attribute to FALSE, otherwise it will fail the field validation
		$options['required'] = false;
		
		$options['type'] = 'textarea';
		
		return $this->input($fieldName, $options);
	}
	
	/**
	 * Creates a text input for datepicker. It uses the <i>input()</i> method.
	 * 
	 * To add the script for datepicker, you should use the <i>Library</i> helper.
	 * @param string $fieldName Field name. Should be "Modelname.fieldname"
	 * @param array $options Options
	 * @return string Html
	 */
	public function datepicker($fieldName, $options=array()) {
		//"class" option default "datepicker"
		$options['class'] = !empty($options['class']) ? $this->Html->cleanAttribute('datepicker '.$options['class']) : 'datepicker';

		$options['type'] = "text";
		
		return $this->input($fieldName, $options);
	}
	
	/**
	 * Closes a form. Rewrites <i>$this->Form->end()</i>.
	 * 
	 * If you don't want to have the submit button, <i>$caption</i> must be FALSE.
	 * If <i>$caption</i> is null, it will use a default submit button.
	 * @param string $caption The submit button label or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function end($caption=null, $options=null) {
		//Unsets the "label" option 
		unset($options['label']);
		
		$submit = !empty($caption) || is_null($caption) ? $this->submit($caption, $options) : null;
		
		return $submit.parent::end();
	}
	
	/**
	 * Checks and returns a value if this is not empty, else returns a default value.
	 * 
	 * It can be useful with the "selected" option, to get a value if this exists or use a default. For example:  
	 * <code>
	 * 'selected' => @$this->MeForm->getDefault($this->request->data['User']['group'], 'user')
	 * </code>
	 * will set the "selected" option to 
	 * <code>
	 * $this->request->data['User']['group']
	 * </code>
	 * if this exists (for example, if the form has already been sent), else it will use the "user" default value.
	 * 
	 * It must be used with the "@" operator, otherwise it will generate a notice.
	 * @param string $value Value to check
	 * @param string $default Default value
	 * @return string Value to check if this is not empty, else default value
	 */
	public function getDefault($value, $default) {
		return !empty($value) ? $value : $default;
	}

	/**
	 * Creates an input element. Rewrites <i>$this->Form->input()</i>
	 * @param string $fieldName Field name. Should be "Modelname.fieldname"
	 * @param array $options Options
	 * @return string Html
	 */
	public function input($fieldName, $options=array()) {
		//"escape" option default FALSE
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];
		
		//"escape" options for errors default FALSE
		if(!empty($options['error']) && empty($options['error']['attributes']['escape']))
			$options['error']['attributes']['escape'] = false;
		
		if(empty($options['after']))
			$options['after'] = null;
		
		//"after" option (tip text after the input)
		if(!empty($options['tip'])) {
			if(!is_array($options['tip']))
				$options['after'] .= '<div class="tip">'.trim($options['tip']).'</div>';
			else
				$options['after'] .= '<div class="tip">'.implode('', array_map(function($v){ return '<p>'.trim($v).'</p>'; }, $options['tip'])).'</div>';
		}
			
		//If the div class is not empty, prepend the "input" class and the input type
		if(!empty($options['div']['class']))
			$options['div']['class'] = $this->Html->cleanAttribute('input '.$this->getInputType($options).' '.$options['div']['class']);

		return parent::input($fieldName, $options);
	}
	
	/**
	 * Gets the input type
	 * @param array $options Options
	 * @return string Type name
	 */
	protected function getInputType($options) {
		$options = parent::_parseOptions($options);
		return($options['type']);
	}

	/**
	 * Creates a button with a surrounding form that submits via POST. Rewrites <i>$this->Form->postButton()</i> 
	 * and uses <i>$this->Form->postLink()</i>
	 *
	 * This method creates a form element. So don't use this method in an already opened form
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function postButton($title, $url, $options=array(), $confirmMessage=false) {
		//"class" option default "btn"
		$options['class'] = empty($options['class']) ? 'btn' : $this->Html->cleanAttribute($options['class'].' btn');

		return $this->postLink($title, $url, $options, $confirmMessage);
	}
	
	/**
	 * Creates a link with a surrounding form that submits via POST. Rewrites <i>$this->Form->postLink()</i>
	 * 
	 * This method creates a form element. So don't use this method in an already opened form
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html
	 */
	public function postLink($title, $url=null, $options=array(), $confirmMessage=false) {
		//Adds an icon to the title, if the "icon" option exists
		$title = !empty($options['icon']) ? $this->Html->icon($options['icon']).$title : $title;
		unset($options['icon']);
		
		//"escape" option default FALSE
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];
		
		//Adds the tooltip, if there's the "tooptip" option
		if(!empty($options['tooltip'])) {
			$options['data-toggle'] = 'tooltip';
			$options['title'] = $options['tooltip'];
			unset($options['tooltip']);
		}
		
		return parent::postLink($title, $url, $options, $confirmMessage);
	}

	/**
	 * Creates a set of radio button inputs. Rewrites <i>$this->Form->radio()</i>
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Radio button options array
	 * @param array $attributes HTML attributes
	 * @return string Html
	 */
	public function radio($fieldName, $options=array(), $attributes=array()) {
		//"legend" attribute default FALSE
		$attributes['legend'] = empty($attributes['legend']) ? false : $attributes['legend'];

		//"separator" attribute default "<br />"
		$attributes['separator'] = empty($attributes['separator']) ? '<br />' : $attributes['separator'];

		return parent::radio($fieldName, $options, $attributes);
	}

	/**
	 * Creates a select input. Rewrites <i>$this->Form->select()</i>
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Radio button options array
	 * @param array $attributes HTML attributes
	 * @return string Html
	 */
	public function select($fieldName, $options=array(), $attributes=array()) {
		//It sets the "empty" attribute to "Select an option" only if:
		// 1) "empty", "default" and "selected" attributes are empty
		// 2) the "multiple" attribute is empty or its value is not "checkbox"
		// 3) the "value" attribute is null
		if(empty($attributes['empty']) &&
				empty($attributes['default']) &&
				empty($attributes['selected']) &&
				(empty($attributes['multiple']) || $attributes['multiple']!=="checkbox") &&
				is_null($attributes['value']))
			$attributes['empty'] = __d('me_tools', 'Select an option');
		
		//"escape" attribute default FALSE
		$attributes['escape'] = empty($attributes['escape']) ? false : $attributes['escape'];
		
		return parent::select($fieldName, $options, $attributes);
	}

	/**
	 * Creates a submit button. Rewrites <i>$this->Form->Submit()</i> and uses the <i>$this->button()</i> method
	 * @param string $caption The label appearing on the submit button or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function submit($caption=null, $options=array()) {
		//Caption default "Submit"
		$caption = !empty($caption) ? $caption : __d('me_tools', 'Submit');
		
		//"type" must be "submit"
		$options['type'] = 'submit';

		//"icon" option default "fa-check"
		$options['icon'] = !isset($options['icon']) ? 'fa-check' : $options['icon'];

		//"class" option default "btn btn-success"
		$options['class'] = !isset($options['class']) ? 'btn btn-success' : $this->Html->cleanAttribute($options['class'].' btn');

		//If isset "div" option and this is false, returns the button
		if(isset($options['div']) && !$options['div'])
			return $this->button($caption, $options);
		//Else, returns the button in a wrapper
		else {
			//"div" option default "submit"
			$div = empty($options['div']) ? 'submit' : $this->Html->cleanAttribute($options['div'].' submit');
			unset($options['div']);

			return $this->Html->tag('div', $this->button($caption, $options), array('class' => $div));
		}
	}	
	
	/**
	 * Creates a text input for timepicker. It uses the <i>input()</i> method.
	 * 
	 * To add the script for timepicker, you should use the <i>Library</i> helper.
	 * @param string $fieldName Field name. Should be "Modelname.fieldname"
	 * @param array $options Options
	 * @return string Html
	 */
	public function timepicker($fieldName, $options=array()) {
		//"class" option default "timepicker"
		$options['class'] = !empty($options['class']) ? $this->Html->cleanAttribute('timepicker '.$options['class']) : 'timepicker';

		//div "class" option default "bootstrap-timepicker"
		$options['div']['class'] = !empty($options['div']['class']) ? $this->Html->cleanAttribute('bootstrap-timepicker '.$options['div']['class']) : 'bootstrap-timepicker';

		$options['type'] = "text";
		
		return $this->input($fieldName, $options);
	}
}