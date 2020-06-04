<?

namespace Nasledie\MultiLang;

global $DB, $APPLICATION, $MESS, $DBType;

$MODULE_ID = 'nasledie.multilang';


/*
  \Bitrix\Main\Loader::registerAutoLoadClasses(
  $MODULE_ID,
  array(
  'NSeo' => 'classes/general/seo_utils.php',
  'CSeoKeywords' => 'classes/general/seo_keywords.php',
  'CSeoPageChecker' => 'classes/general/seo_page_checker.php'
  )
  );
 */

class NMLAgent {

	public static function Calc() {
		if (\CModule::IncludeModule("iblock")) {
			global $DB;
			/*$sql = "SELECT * FROM `n_seolink_url`;";
			$res = $DB->Query($sql, false, $err_mess . __LINE__);
			while ($row = $res->Fetch()) {
				if ($row['GLOBAL_URL'] != '') {
					$row['GLOBAL_URL'] = '"' . $row['GLOBAL_URL'] . '"';
				}
				if ($row['LOCAL_URL'] != '') {
					$row['LOCAL_URL'] = '"' . $row['LOCAL_URL'] . '"';
				}
				self::$URL[$row['ID']] = $row;
			}
			
			*/

			
		}


		
		return '\Nasledie\MultiLang\NMLAgent::Calc();';
	}

}

?>