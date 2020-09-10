<?php
/*
 * @author  Andi SiBmius <sibmius@gmail.com>
 * @license LGPL 3.0
 * @copyright 2007 RoW-Team
 */


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");


$MODULE_ID = 'nasledie.multilang';

use \Bitrix\Main\Localization\Loc as Loc;

\Bitrix\Main\Loader::includeModule($MODULE_ID);

Loc::loadMessages(__FILE__);

$sTableID = "tbl_seo"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "ID", "asc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка


$FilterArr = Array(
	"find_id",
	"find_obtype",
	"find_obid",
	"find_lang",
);

$lAdmin->InitFilter($FilterArr);

$arFilter = Array(
	"ID" => $find_id,
	"OBJECT_TYPE" => $find_obtype,
	"OBJECT_ID" => $find_obid,
	"LANG" => $find_lang,
);

global $DB;

$sql = "SELECT * FROM `n_multilang_seo`";


if ($set_filter == 'Y') {

	$where = array();
	if ($find != '' && $find_type != '') {
		$where[] = "`" . $find_type . "` = '" . $DB->ForSql($find) . "' ";
	}

	foreach ($arFilter as $F => $V) {
		if ($V != '') {
			$where[] = "`" . $F . "` = '" . $DB->ForSql($V) . "' ";
		}
	}
	if (!empty($where)) {
		$sql .= " WHERE " . implode(' AND ', $where);
	}
}

if ($by != '' && $order != '') {
	$sql .= " ORDER BY `" . $by . "` " . $order;
}
$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("IBLOCK_TITLE")));


$lAdmin->AddHeaders(array(
	array("id" => "ID",
		"content" => "ID",
		"sort" => "ID",
		"default" => true,
	),
	array("id" => "OBJECT_TYPE",
		"content" => GetMessage("TBL_OBJECT_TYPE"),
		"sort" => "OBJECT_TYPE",
		"default" => true,
	),
	array("id" => "OBJECT_ID",
		"content" => GetMessage("TBL_OBJECT_ID"),
		"sort" => "OBJECT_ID",
		"default" => true,
	),
	array("id" => "TITLE",
		"content" => GetMessage("TBL_TITLE"),
		"sort" => "TITLE",
		"default" => true,
	),
	array("id" => "KEY",
		"content" => GetMessage("TBL_KEY"),
		"sort" => "KEY",
		"default" => true,
	),
	array("id" => "DESC",
		"content" => GetMessage("TBL_DESC"),
		"sort" => "DESC",
		"default" => true,
	),
	array("id" => "H1",
		"content" => GetMessage("TBL_H1"),
		"sort" => "H1",
		"default" => true,
	),
	array("id" => "LANG",
		"content" => GetMessage("TBL_LANG"),
		"sort" => "LANG",
		"default" => true,
	),
));

$arPropsList = array();

while ($arRes = $rsData->NavNext(true, "f_")) {  // создаем строку. результат - экземпляр класса CAdminListRow
	$row = & $lAdmin->AddRow($f_ID, $arRes);

	$row->AddViewField("ID", '<a href="nasledie.multilang_seo_edit.php?ID=' . $f_ID . '&lang=' . LANG . '">' . $f_ID . '</a>');


	$row->AddViewField("OBJECT_TYPE", GetMessage("TBL_OBJECT_TYPE_".$f_OBJECT_TYPE));
	$row->AddViewField("OBJECT_ID", $f_OBJECT_ID);
	$row->AddViewField("TITLE", $f_TITLE);
	$row->AddViewField("KEY", $f_KEY);
	$row->AddViewField("DESC", $f_DESC);
	$row->AddViewField("H1", $f_H1);
	$row->AddViewField("LANG", $f_LANG);

	$arActions = Array();

	$arActions[] = array(
		"ICON" => "edit",
		"DEFAULT" => true,
		"TEXT" => GetMessage("IBLOCK_EDIT"),
		"ACTION" => $lAdmin->ActionRedirect("nasledie.multilang_seo_edit.php?ID=" . $f_ID)
	);

	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => GetMessage("IBLOCK_DEL"),
		"ACTION" => "if(confirm('" . GetMessage('DEL_CONFIRM') . "')) " . $lAdmin->ActionRedirect("nasledie.multilang_seo_del.php?ID=" . $f_ID)
	);

	$row->AddActions($arActions);
}
$lAdmin->AddFooter(
	array(
		array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()), // кол-во элементов
		array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"), // счетчик выбранных элементов
	)
);

$aContext = array(
	array(
		"TEXT" => GetMessage("POST_ADD"),
		"LINK" => "nasledie.multilang_seo_edit.php?lang=" . LANG,
		"TITLE" => GetMessage("POST_ADD_TITLE"),
		"ICON" => "btn_new",
	),
);
$lAdmin->CheckListMode();


$lAdmin->AddAdminContextMenu($aContext);

$APPLICATION->SetTitle(GetMessage("IBLOCK_TITLE"));


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");




$oFilter = new CAdminFilter(
	$sTableID . "_filter", array(
	"ID",
	GetMessage("TBL_OBJECT_TYPE"),
	GetMessage("TBL_OBJECT_ID"),
	GetMessage("TBL_LANG")
	)
);
?>
<form name="find_form" method="get" action="<? echo $APPLICATION->GetCurPage(); ?>">
	<? $oFilter->Begin(); ?>
	<tr>
		<td><b><?= GetMessage("TBL_FIND") ?>:</b></td>
		<td>
			<input type="text" size="25" name="find" value="<? echo htmlspecialchars($find) ?>">
			<?
			$arr = array(
				"reference" => array(
					"ID",
					GetMessage("TBL_OBJECT_TYPE"),
					GetMessage("TBL_OBJECT_ID"),
					GetMessage("TBL_LANG")
				),
				"reference_id" => array(
					"ID",
					"OBJECT_TYPE",
					"OBJECT_ID",
					"LANG",
				)
			);
			echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
			?>
		</td>
	</tr>

	<tr>
		<td><?= "ID" ?>:</td>
		<td>
			<input type="text" name="find_id"  value="<? echo htmlspecialchars($find_id) ?>">
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("TBL_OBJECT_TYPE") ?>:</td>
		<td>
			<?
			$arr = array(
				"reference" => array(
					GetMessage("TBL_OBJECT_TYPE_ALL"),
					GetMessage("TBL_OBJECT_TYPE_I"),
					GetMessage("TBL_OBJECT_TYPE_S"),
					GetMessage("TBL_OBJECT_TYPE_E"),
				),
				"reference_id" => array(
					"",
					"I",
					"S",
					"E",
				)
			);
			echo SelectBoxFromArray("find_obtype", $arr, $find_type, "", "");
			?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("TBL_OBJECT_ID") . ":" ?></td>
		<td>
			<input type="text" name="find_obid"  value="<? echo htmlspecialchars($find_obid) ?>">
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("TBL_LANG") . ":" ?></td>
		<td><input type="text" name="find_lang"  value="<? echo htmlspecialchars($find_lang) ?>"></td>
	</tr>

	<?php
	$oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
	$oFilter->End();
	?>
</form>

<?php
// выведем таблицу списка элементов
$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");

