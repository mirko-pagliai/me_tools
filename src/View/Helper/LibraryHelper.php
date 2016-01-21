<?php
/**
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
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			http://getbootstrap.com/components/#dropdowns Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;
use MeTools\Core\Plugin;

/**
 * Library helper
 */
class LibraryHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['MeTools.Asset', 'Html' => ['className' => 'MeTools.Html']];
	
    /**
     * It will contain the output code
     * @var array 
     */
    protected $output = [];
	
	/**
	 * Internal function to generate datepicker and timepicker.
	 * 
	 * Bootstrap Datepicker and Moment.js should be installed via Composer.
     * @param string $input Target field
     * @param array $options Options for the datepicker
	 * @return string jQuery code
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
	 * @uses MeTools\View\Helper\HtmlHelper::css()
	 * @uses MeTools\View\Helper\HtmlHelper::js()
	 */
	protected function _datetimepicker($input, array $options = []) {
		$this->Html->js([
			'/vendor/moment/moment-with-locales.min',
			'/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min'
		], ['block' => 'script_bottom']);
		
        $this->Html->css('/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min', ['block' => 'css_bottom']);
		
		//Shows the "Today" button in the icon toolbar
		$options = addDefault('showTodayButton', TRUE, $options);
		
		//Shows the "Clear" button in the icon toolbar
		$options = addDefault('showClear', TRUE, $options);
		
		$options = addDefault('icons', [
			'time' => 'fa fa-clock-o',
			'date' => 'fa fa-calendar',
			'up' => 'fa fa-arrow-up',
			'down' => 'fa fa-arrow-down',
			'previous' => 'fa fa-arrow-left',
			'next' => 'fa fa-arrow-right',
			'today' => 'fa fa-dot-circle-o',
			'clear' => 'fa fa-trash'
		], $options);
		
		//Sets the current locale
		$locale = substr(\Cake\I18n\I18n::locale(), 0, 2);
		$options = addDefault('locale', empty($locale) ? 'en-gb' : $locale, $options);
		
		return sprintf('$("%s").datetimepicker(%s);', $input, json_encode($options));
	}

    /**
     * Before layout callback. beforeLayout is called before the layout is rendered.
	 * @param \Cake\Event\Event $event An Event instance
     * @param string $layoutFile The layout about to be rendered
	 * @uses MeTools\View\Helper\HtmlHelper::scriptBlock()
	 * @uses output
     */
    public function beforeLayout(\Cake\Event\Event $event, $layoutFile) {
        //Writes the output
        if(!empty($this->output)) {
            $this->output = implode(PHP_EOL, array_map(function($v){ 
				return "\t".$v;
			}, $this->output));
			
			$this->Html->scriptBlock(sprintf('$(function() {%s});', PHP_EOL.$this->output.PHP_EOL), ['block' => 'script_bottom']);
        
			//Resets the output
			$this->output = [];
		}
    }
	
	/**
	 * Create a script block for Google Analytics
	 * @param string $id Analytics ID
	 * @uses MeTools\View\Helper\HtmlHelper::scriptBlock()
	 * @return mixed Html code or NULL if is localhost
	 */
	public function analytics($id) {
		if(is_localhost())
			return NULL;
		
		return $this->Html->scriptBlock(sprintf('!function(e,a,t,n,c,o,s){e.GoogleAnalyticsObject=c,e[c]=e[c]||function(){(e[c].q=e[c].q||[]).push(arguments)},e[c].l=1*new Date,o=a.createElement(t),s=a.getElementsByTagName(t)[0],o.async=1,o.src=n,s.parentNode.insertBefore(o,s)}(window,document,"script","//www.google-analytics.com/analytics.js","ga"),ga("create","%s","auto"),ga("send","pageview");', $id), ['block' => 'script_bottom']);
	}
	
	/**
     * Loads all CKEditor scripts.
     * 
     * To know how to install and configure CKEditor, please refer to the `README.md` file.
	 * 
	 * CKEditor must be located into `APP/webroot/ckeditor`.
     * 
     * To create an input field for CKEditor, you should use the `ckeditor()` method provided by the `FormHelper`.
     * @param bool $jquery FALSE if you don't want to use the jQuery adapter
     * @see MeTools\View\Helper\FormHelper::ckeditor()
     * @see http://docs.cksource.com CKEditor documentation
	 * @uses MeTools\View\Helper\AssetHelper::js()
     */
    public function ckeditor($jquery = TRUE) {
		$path = WWW_ROOT.DS.'ckeditor'.DS;
		
		if(!is_readable($path.'ckeditor.js'))
			return; 

		$scripts = ['/ckeditor/ckeditor'];

		//Checks for the jQuery adapter
		if($jquery && is_readable($path.DS.'adapters'.DS.'jquery.js'))
			$scripts[] = '/ckeditor/adapters/jquery';

		//Checks for `APP/webroot/js/ckeditor_init.php`
		if(is_readable(WWW_ROOT.'js'.DS.'ckeditor_init.php'))
			$scripts[] = 'ckeditor_init.php?';
		//Checks for `APP/webroot/js/ckeditor_init.js`
		elseif(is_readable(WWW_ROOT.'js'.DS.'ckeditor_init.js'))
			$scripts[] = 'ckeditor_init';
		//Else, uses `APP/plugin/MeTools/webroot/js/ckeditor_init.js`
		else
			$scripts[] = 'MeTools.ckeditor_init.php?';
		
		$this->Asset->js($scripts, ['block' => 'script_bottom']);
    }

    /**
     * Adds a datepicker to the `$input` field.
     * 
     * To create an input field compatible with datepicker, you should use the `datepicker()` method provided by the `FormHelper`.
	 * 
	 * Bootstrap Datepicker and Moment.js should be installed via Composer.
     * @param string $input Target field. Default is `.datepicker`
     * @param array $options Options for the datepicker
     * @see MeTools\View\Helper\FormHelper::datepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
	 * @uses output
	 * @uses _datetimepicker()
     */
	public function datepicker($input = NULL, array $options = []) {
		$input = empty($input) ? '.datepicker' : $input;
		
		$options = addDefault('format', 'YYYY/MM/DD', $options);
		
        $this->output[] = self::_datetimepicker($input, $options);
	}
	
	 /**
     * Adds a datetimepicker to the `$input` field.
     * 
     * To create an input field compatible with datetimepicker, you should use the `datetimepicker()` method provided by the `FormHelper`.
	 * 
	 * Bootstrap Datepicker and Moment.js should be installed via Composer.
     * @param string $input Target field. Default is `.datetimepicker`
     * @param array $options Options for the datetimepicker
     * @see MeTools\View\Helper\FormHelper::datetimepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
	 * @uses output
	 * @uses _datetimepicker()
     */
	public function datetimepicker($input = NULL, array $options = []) {
		$input = empty($input) ? '.datetimepicker' : $input;
		
        $this->output[] = self::_datetimepicker($input, $options);
	}
	
	/**
     * Loads all FancyBox scripts.
	 * 
	 * FancyBox must be installed via Composer.
     * @see http://fancyapps.com/fancybox/#docs FancyBox documentation
	 * @uses MeTools\View\Helper\AssetHelper::css()
	 * @uses MeTools\View\Helper\AssetHelper::js()
	 * @uses MeTools\Core\Plugin::path()
	 */
	public function fancybox() {		
		$this->Asset->css([
			'/vendor/fancybox/jquery.fancybox',
			'/vendor/fancybox/helpers/jquery.fancybox-buttons',
			'/vendor/fancybox/helpers/jquery.fancybox-thumbs'
		], ['block' => 'css_bottom']);
		
		$this->Asset->js([
			'/vendor/fancybox/jquery.fancybox.pack',
			'/vendor/fancybox/helpers/jquery.fancybox-buttons',
			'/vendor/fancybox/helpers/jquery.fancybox-thumbs'
		], ['block' => 'script_bottom']);
		
		//Checks for the init script into `APP/webroot/js/`
		if(is_readable(WWW_ROOT.'js'.DS.'fancybox_init.js'))
			$script = 'fancybox_init';
		//Else, checks for the init script into `APP/plugin/MeTools/webroot/fancybox/`
		elseif(is_readable(Plugin::path('MeTools', 'webroot'.DS.'fancybox'.DS.'fancybox_init.js')))
			$script = 'MeTools./fancybox/fancybox_init';
		else
			return;
		
		$this->Asset->js($script, ['block' => 'script_bottom']);
	}
	
	/**
	 * Create a script block for Shareaholic.
	 * 
	 * Note that this code only adds the Shareaholic "setup code".
	 * To render the "share buttons", you have to use the `HtmlHelper`.
	 * @param string $site_id Shareaholic site ID
	 * @return mixed Html code
	 * @see MeTools\View\Helper\HtmlHelper::shareaholic()
	 * @uses MeTools\View\Helper\HtmlHelper::js()
	 */
	public function shareaholic($site_id) {
		return $this->Html->js('//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js', ['async' => 'async', 'block' => 'script_bottom', 'data-cfasync' => 'false', 'data-shr-siteid' => $site_id]);
	}

	/**
     * Through `slugify.js`, it provides the slug of a field. 
     * 
     * It reads the value of the `$sourceField` field and it sets its slug in the `$targetField`.
     * @param string $sourceField Source field
     * @param string $targetField Target field
	 * @uses MeTools\View\Helper\AssetHelper::js()
	 * @uses output
     */
    public function slugify($sourceField = 'form #title', $targetField = 'form #slug') {
        $this->Asset->js('MeTools.slugify.min', ['block' => 'script_bottom']);
		
        $this->output[] = sprintf('$().slugify("%s", "%s");', $sourceField, $targetField);
    }
	
    /**
     * Adds a timepicker to the `$input` field.
     * 
     * To create an input field compatible with datepicker, you should use the `timepicker()` method provided by the `FormHelper`.
	 * 
	 * Bootstrap Datepicker and Moment.js should be installed via Composer.
     * @param string $input Target field. Default is `.timepicker`
     * @param array $options Options for the timepicker
     * @see MeTools\View\Helper\FormHelper::timepicker()
     * @see https://github.com/Eonasdan/bootstrap-datetimepicker Bootstrap v3 datetimepicker widget documentation
	 * @uses output
	 * @uses _datetimepicker()
     */
	public function timepicker($input = NULL, array $options = []) {
		$input = empty($input) ? '.timepicker' : $input;
		
		$options = addDefault('pickTime', FALSE, $options);
		
		$this->output[] = self::_datetimepicker($input, $options);
	}
}