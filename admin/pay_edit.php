<?php
/*
 * @author  Andi SiBmius <sibmius@gmail.com>
 * @license LGPL 3.0
 * @copyright 2007 RoW-Team
 */

use Bitrix\Main\Loader;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Loader::includeModule('iblock');

$MODULE_ID = 'nasledie.multilang';

use \Bitrix\Main\Localization\Loc as Loc;

\Bitrix\Main\Loader::includeModule($MODULE_ID);

Loc::loadMessages(__FILE__);


$bVarsFromForm = false;

$arLang = array();

$aTabs = array();
$resLANG = CLanguage::GetList();
while ($L = $resLANG->Fetch()) {
	if ($L['LID'] == 'en') {
		continue;
	}
	if ($L['LID'] == 'ru') {
		continue;
	}
	$aTabs[] = array("DIV" => "edit" . $L['LID'], "TAB" => $L['LID'], "ICON" => "main_user_edit", "TITLE" => $L['LID']);
	$arLang[$L['LID']] = $L;
}



$tabControl = new CAdminTabControl("tabControl", $aTabs);



$message = null;  // сообщение об ошибке
$bVarsFromForm = false;

if (
	$REQUEST_METHOD == "POST" // проверка метода вызова страницы
	&&
	($save != "" || $apply != "") // проверка нажатия кнопок "Сохранить" и "Применить"
	&&
	check_bitrix_sessid()  // проверка идентификатора сессии
) {
	global $DB;



	foreach ($arLang as $LANG => $L) {
		if ($PAY_ID > 0) {
			// обработка данных формы
			$arFields = array(
				"PAY_ID" => "'" . $DB->ForSql($PAY_ID) . "'",
				"NAME" => "'" . $DB->ForSql($NAME[$LANG]) . "'",
				"DESCRIPTION" => "'" . $DB->ForSql(${'DESCRIPTION_' . $LANG}) . "'",
				"DESCRIPTION_TYPE" => "'" . $DB->ForSql(${'DESCRIPTION_TYPE_' . $LANG}) . "'",
				"LANG" => "'" . $DB->ForSql($LANG) . "'"
			);
			$DB->StartTransaction();
			if ($ID[$LANG] > 0) {
				$DB->Update("n_multilang_pay", $arFields, "WHERE ID='" . $ID[$LANG] . "'", $err_mess . __LINE__);
			} else {
				$DB->Insert("n_multilang_pay", $arFields, $err_mess . __LINE__);
			}
			$DB->Commit();
		}
	}

	if ($PAY_ID > 0) {
		// если сохранение прошло удачно - перенаправим на новую страницу 
		// (в целях защиты от повторной отправки формы нажатием кнопки "Обновить" в браузере)
		if ($apply != "") {
			// если была нажата кнопка "Применить" - отправляем обратно на форму.
			LocalRedirect("/bitrix/admin/nasledie.multilang_pay_edit.php?PAY_ID=" . $PAY_ID . "&mess=ok&lang=" . LANG . "&" . $tabControl->ActiveTabParam());
		} else {
			// если была нажата кнопка "Сохранить" - отправляем к списку элементов.
			LocalRedirect("/bitrix/admin/nasledie.multilang_pay.php?lang=" . LANG);
		}
	} else {
		// если в процессе сохранения возникли ошибки - получаем текст ошибки и меняем вышеопределённые переменные
	}
}

$str_ID = false;
$str_PAY_ID = false;
$str_NAME = false;
$str_DESCRIPTION = false;
$str_DESCRIPTION_TYPE = false;
$str_LANG = false;

if ($PAY_ID > 0) {
	$sql = "SELECT * FROM `n_multilang_pay` WHERE `PAY_ID`='" . $DB->ForSql($PAY_ID) . "'";
	$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
	while ($row = $rsData->Fetch()) {
		$str_ID[$row['LANG']] = $row['ID'];
		$str_PAY_ID[$row['LANG']] = $row['PAY_ID'];
		$str_NAME[$row['LANG']] = $row['NAME'];
		$str_DESCRIPTION[$row['LANG']] = $row['DESCRIPTION'];
		$str_DESCRIPTION_TYPE[$row['LANG']] = $row['DESCRIPTION_TYPE'];
		$str_LANG[$row['LANG']] = $row['LANG'];
	}
	$sql = "SELECT `b_sale_pay_system_action`.* FROM `b_sale_pay_system_action`"
		. "  WHERE `b_sale_pay_system_action`.`ID`='" . $DB->ForSql($PAY_ID) . "' ";
	$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
	while ($row = $rsData->Fetch()) {
		$arElement = $row;
	}
}











$APPLICATION->SetTitle(GetMessage("IBLOCK_TITLE_EDIT"));



require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


if ($_REQUEST["mess"] == "ok" && $ID > 0) {
	CAdminMessage::ShowMessage(array("MESSAGE" => GetMessage("IBLOCK_OK"), "TYPE" => "OK"));
}

if ($message) {
	echo $message->Show();
} elseif ($error) {
	CAdminMessage::ShowMessage($$error);
}
CModule::IncludeModule("iblock");
$sql = "SELECT * FROM `b_sale_pay_system_action`";
$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
while ($IB = $rsData->Fetch()) {

	$arSECT[] = $IB;
}
?>

<form method="POST" Action="<? echo $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">
	<? echo bitrix_sessid_post(); ?>
	<input type="hidden" name="lang" value="<?= LANG ?>">
	<? if ($PAY_ID > 0 && !$bCopy) { ?>
		<input type="hidden" name="PAY_ID" value="<?= $PAY_ID ?>">
	<? } else { ?>
		<div class="adm-detail-block">
			<div class="adm-detail-content-wrap">
				<div class="adm-detail-content" style="padding: 12px 18px 12px 12px;">
					<div class="adm-detail-content-item-block">
						<table class="adm-detail-content-table edit-table">
							<tr>
								<td><span class="required">*</span><?= GetMessage("TBL_PAY") ?></td>
								<td>
									<select name="PAY_ID">
										<? foreach ($arSECT as $type => $IB) { ?>
											<option value="<?= $IB['ID'] ?>">[<?= $IB['ID'] ?>]<?= $IB['NAME'] ?></option>
										<? } ?>
									</select>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	<? }; ?>
	<?
// отобразим заголовки закладок
	$tabControl->Begin();
	?>

	<?php
	foreach ($arLang as $LANG => &$L) {

		$tabControl->BeginNextTab();
		?>
		<tr>
			<td><span class="required">*</span><? echo GetMessage("TBL_NAME") ?></td>

			<td>
				<textarea readonly  style="width:100%;"><?php echo $arElement['NAME'] ?></textarea>
				<input type="text" name="NAME[<?= $LANG ?>]" value="<? echo $str_NAME[$LANG]; ?>" size="30" maxlength="100">
				<input type="hidden" name="ID[<?= $LANG ?>]" value="<? echo $str_ID[$LANG]; ?>">
			</td>
		</tr>
		<tr>
			<td><span class="required">*</span><? echo GetMessage("TBL_DESCRIPTION") ?></td>
			<td>
				<textarea readonly  style="width:100%;"><?php echo $arElement['DESCRIPTION'] ?></textarea>
				<? if (COption::GetOptionString("iblock", "use_htmledit", "Y") == "Y" && Loader::includeModule("fileman")) { ?>
					<?php
					CFileMan::AddHTMLEditorFrame(
						"DESCRIPTION_" . $LANG . "", $str_DESCRIPTION[$LANG], "DESCRIPTION_TYPE_" . $LANG . "", $str_DESCRIPTION_TYPE[$LANG], array(
						'height' => 450,
						'width' => '100%'
						)
					);
					?>
				<? } else { ?>
					<input type="radio" name="DESCRIPTION_TYPE_<?= $LANG ?>" id="DESCRIPTION_TYPE_<?= $LANG ?>1" value="text"<? if ($str_DESCRIPTION_TYPE[$LANG] != "html") echo " checked" ?>><label for="DESCRIPTION_TYPE_<?= $LANG ?>1"> <? echo GetMessage("IB_E_DESCRIPTION_TYPE_TEXT") ?></label> /
					<input type="radio" name="DESCRIPTION_TYPE_<?= $LANG ?>" id="DESCRIPTION_TYPE_<?= $LANG ?>2" value="html"<? if ($str_DESCRIPTION_TYPE[$LANG] == "html") echo " checked" ?>><label for="DESCRIPTION_TYPE_<?= $LANG ?>2"> <? echo GetMessage("IB_E_DESCRIPTION_TYPE_HTML") ?></label>
					<br>
					<textarea cols="60" rows="15" name="DESCRIPTION_<?= $LANG ?>" style="width:100%;"><? echo $str_DESCRIPTION[$LANG] ?></textarea>
				<? } ?>
			</td>
		</tr>

		<?
	}
	$tabControl->Buttons(
		array(
			"back_url" => "nasledie.multilang_pay.php?lang=" . LANG,
		)
	);
	$tabControl->End();
	?>
</form>
<? echo BeginNote(); ?>
<span class="required">*</span><? echo GetMessage("REQUIRED_FIELDS") ?>
<? echo EndNote(); ?>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
