<?

namespace Nasledie\MultiLang;

use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Handler
 * @package Nasledie\MultiLang
 */
class Handler {

	static private $module_id = 'nasledie_multilang';

	public static function main_onBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu) {
		$arGlobalMenu['global_menu_nasledie'] = Array(
			'menu_id' => 'nasledie',
			'text' => Loc::getMessage(self::$module_id . '_HANDLER.GLOBAL_MENU_TEXT'),
			'title' => Loc::getMessage(self::$module_id . '_HANDLER.GLOBAL_MENU_TITLE'),
			'sort' => '10',
			'items_id' => 'global_menu_seolink',
			'help_section' => 'nasledie',
			'items' => Array()
		);
	}

}
