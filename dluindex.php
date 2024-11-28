<?php
/* Copyright (C) 2024 Lenoble Arthur <arthurl52100@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

 /**
 *	\file       pmpr/dluindex.php
 *	\ingroup    pmpr
 *	\brief      Page for the devaluated products
 */

 // Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/exports/class/export.class.php';

// Load translation files required
$langs->loadLangs(array("pmpr@pmpr"));

$action = GETPOST('action', 'aZ09');

$sql = "";
$nb_prod = 0;

//if($resql)
//{
    llxHeader("", $langs->trans("DLUArea"), '', '', 0, 0, '', '', '', 'mod-pmpr page-index');

    print load_fiche_titre($langs->trans("DLUArea"), '', 'dlu.png@pmpr');

    print '<form method="POST" id="searchFormList" action"'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<label for="limit_period">'.$langs->trans('LIMIT_PERIOD').'</label>';
    print '<input type="date" id="limit_period" name="limit_period" value="'.$limit.'">';
    print '<br>';
    print '<input type="submit" value="'.$langs->trans("REFRESH").'">';
    //print_barre_liste($langs->trans("DLUProducts"), 0, $_SERVER["PHP_SELF"], '', '', '', '', 0, 0, 'product', 0, '', '', 10, 0, 0, 1);
    print '</form>';

    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print '<th>'.$langs->trans("PRODUCT_LABEL").'<span class="badge marginleftonlyshort">'.$nb_prod.'</span></th>';
    print '<th>'.$langs->trans("QTY_DLU").'<span class="badge marginleftonlyshort">'.$nb_prod.'</span></th>';
    print '<th>'.$langs->trans("QTY").'<span class="badge marginleftonlyshort">'.$nb_prod.'</span></th>';
    print '<th>'.$langs->trans("BUTTON").'<span class="badge marginleftonlyshort">'.$nb_prod.'</span></th>';
    print '</tr>';

    $test = '';

    $i = -2;
    while ($i < $nb_prod)
    {
        // Replace $test with $obj->smth
        print '<tr class="oddeven">';
        // For this one use $prod->getNomUrl(1) and don't forget to initialize the Product obj before
        print '<td class="tdoverflowmax200" data-ker="ref">'.$test.'</td>';
        print '<td class="nowrap">'.$test.'</td>';
        print '<td class="nowrap">'.$test.'</td>';
        print '<td class="tdoverflowmax50"><input class="butAction" type="submit" value="'.$langs->trans("UPDATE_STOCK").'"></td>';

        print '</tr>';
        $i++;
    }
//}

print '</table><br>';
//$db->free($resql);