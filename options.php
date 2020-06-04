<?php
/*
 * @author  Andi SiBmius <sibmius@gmail.com>
 * @license LGPL 3.0
 * @copyright 2007 RoW-Team
 */

$module_id = 'nasledie.multilang';

use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Config\Option,
	Bitrix\Main;

if (!$USER->isAdmin())
	return;

Loader::includeModule($module_id);
Loader::includeModule('iblock');
Loc::loadMessages(__FILE__);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid()) {
	Option::set($module_id, 'iblock_id', json_encode($_POST['iblock_id']));
	LocalRedirect($_SERVER['REQUEST_URI']);
}

if (!empty($strWarning))
	CAdminMessage::ShowMessage($strWarning);

if (!empty($strOK))
	CAdminMessage::ShowNote($strOK);


$aTabs = array(
	array("DIV" => "edit1", "TAB" => Loc::getMessage("NE_TAB_1_TITLE"), "ICON" => "catalog_settings", "TITLE" => Loc::getMessage("NE_TAB_1_TITLE")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);

$currentSettings = array();
$currentSettings['iblock_id'] = (string) Option::get($module_id, 'iblock_id');
if ($currentSettings['iblock_id'] != '') {
	$currentSettings['iblock_id'] = json_decode($currentSettings['iblock_id'],true);
}

$arIB = array();
$db_iblock_type = CIBlockType::GetList();
while ($ar_iblock_type = $db_iblock_type->Fetch()) {
	if ($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG)) {
		$arIB[$arIBType['ID']]['NAME'] = $arIBType["NAME"];
	}
}
$res = CIBlock::GetList(Array(), Array());

while ($ar_res = $res->Fetch()) {
	$arIB[$ar_res['IBLOCK_TYPE_ID']]['IB'][] = $ar_res;
}

$tabControl->Begin();
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>?lang=<?= LANGUAGE_ID; ?>&mid=<?= htmlspecialcharsbx($mid); ?>&mid_menu=1" name="ara">
<? echo bitrix_sessid_post() ?><?
$tabControl->BeginNextTab();
?>
	<tr class="heading">
		<td colspan="2"><?= Loc::getMessage("BX_CAT_SYSTEM_SETTINGS"); ?></td>
	</tr>
	<tr>
		<td width="40%"><label for="discsave_apply"><? echo Loc::getMessage("NE_ML_IBLOCK"); ?></label></td>
		<td width="60%">
			<select name="iblock_id[]" id="iblock_id" size="10" multiple>
<? foreach ($arIB as $code => $type) { ?>
					<optgroup label="<?= $type['NAME'] ?>">
	<? foreach ($type['IB'] as &$IB) { ?>
							<option value="<? echo $IB['ID']; ?>" <? echo (in_array($IB['ID'], $currentSettings['iblock_id']) ? 'selected' : ''); ?>><? echo $IB['NAME']; ?></option>
				<? } ?>
					</optgroup>
				<? } ?>
			</select>
		</td>
	</tr>
					<?
					$tabControl->Buttons();
					?>
	<input type="submit" class="adm-btn-save" <? if ($bReadOnly) echo "disabled" ?> name="Update" value="<? echo Loc::getMessage("CAT_OPTIONS_BTN_SAVE"); ?>">
	<input type="hidden" name="Update" value="Y">
	<input type="reset" name="reset" value="<? echo Loc::getMessage("CAT_OPTIONS_BTN_RESET") ?>">.

</form>
	<?
	$tabControl->End();
	?>