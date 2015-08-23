<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class ReplaceTinyMCE extends Module
{
	public function __construct()
	{
		$this->name = 'replacetinymce';
		$this->displayName = $this->l('Replace TinyMCE');
		$this->description = $this->l('Replace TinyMCE with CodeMirror for fun and profit.');
		$this->tab = 'administration';
		$this->version = '1.0.0';
		$this->author = 'fmdj';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();
	}

	private function setupConfiguration()
	{
		Configuration::updateValue('PS_USE_HTMLPURIFIER', false);
		return true;
	}

	public function install()
	{
		return parent::install() && $this->registerHook('displayBackOfficeHeader') && $this->setupConfiguration();
	}

	private function addResource()
	{
		$path = implode(
			DIRECTORY_SEPARATOR,
			array_merge(
				array($this->_path),
				func_get_args()
			)
		);

		$method = null;
		if (preg_match('/\.js$/', $path)) {
			$method = 'addJS';
		} else if (preg_match('/\.css/', $path)) {
			$method = 'addCSS';
		} else {
			throw new Exception(sprintf(
				'Resource type of file `%s` is unknown.',
				$path
			));
		}

		$this->context->controller->$method($path);

		return $this;
	}

	public function hookDisplayBackOfficeHeader()
	{
		$this
			->addResource('vendor', 'codemirror', 'codemirror.js')
			->addResource('vendor', 'codemirror', 'codemirror.css')
			->addResource('vendor', 'codemirror', 'css.js')
			->addResource('vendor', 'codemirror', 'javascript.js')
			->addResource('vendor', 'codemirror', 'xml.js')
			->addResource('vendor', 'codemirror', 'htmlmixed.js')
			->addResource('replacetinymce.js')
		;
	}

	public function getContent()
	{
		$stylesheetRelativePath = implode(DIRECTORY_SEPARATOR, [
			$this->context->shop->theme_directory,
			'css', 'autoload', $this->context->shop->theme_name . '.css'
		]);

		$stylesheetPath = implode(DIRECTORY_SEPARATOR, [
			_PS_ALL_THEMES_DIR_,
			$stylesheetRelativePath
		]);

		$stylesheetContents = '';

		if (Tools::getValue('saveStylesheetContents') === "1") {
			$response = array('ok' => false, 'reason' => $this->l('An unspecified error occurred.'));

			$stylesheetContents = Tools::getValue('stylesheetContents');

			if (file_put_contents($stylesheetPath, $stylesheetContents) !== false) {
				$response = array('ok' => true, 'reason' => $this->l('Stylesheet successfully saved!'));
			} else {
				$response = array('ok' => false, 'reason' => $this->l('Could not save stylesheet, weird.'));
			}

			header('Content-Type: application/json');
			die(Tools::jsonEncode($response));
		}

		if (file_exists($stylesheetPath)) {
			$stylesheetContents = file_get_contents($stylesheetPath);
		}

		$this->context->smarty->assign(array(
			'stylesheetContents' => $stylesheetContents,
			'stylesheetRelativePath' => $stylesheetRelativePath
		));

		return $this->display(__FILE__, 'configuration.tpl');
	}
}
