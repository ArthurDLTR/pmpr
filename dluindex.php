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
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';

// Load translation files required
$langs->loadLangs(array("pmpr@pmpr"));

/*
 * Actions
 */
$action = GETPOST('action', 'aZ09');
$array = dol_getdate(dol_now());
if(GETPOST('limit_period', 'alpha')){
    $limit = GETPOST('limit_period', 'alpha');
} else {
    $limit = ($array['year']-1).'-'.$array['mon'].'-'.$array['mday'];
}

if (GETPOST('stock_rmv', 'alpha')){
    $stock_rmv = GETPOST('stock_rmv', 'alpha');
} 

if (GETPOST('id_prod', 'alpha')){
    $id_prod = GETPOST('id_prod', 'alpha');
}

if (GETPOSTISSET('update-btn', 'bool')){
//     if (GETPOST('stock_rmv', 'alpha') && GETPOST('id_prod', 'alpha')){
//         print 'Produit concerné : '.$id_prod.' en quantité : '.$stock_rmv;   
//         $move = new MouvementStock($db);
//         $move->create($user, $id_prod, 1, -$stock_rmv, 1, 10, 'Stock mouvement by DLU page', '', dol_now());
//    }

    // Créer bien la ligne dans la BDD pour les mouvements de stock mais pas la bonne valeur de stock qui change (0 au lieu de $stock_rmv)
    $object = new Product($db);
    $result = $object->fetch($id_prod);
    
    $result = $object->correct_stock(
        $user,
        1,
        $stock_rmv,
        1,
        'Stock mouvement by DLU page',
        0,
        '',
        0,
        0,
        0
    );

    // Part of the code to update Database, useless if the MouvementStock is working
    /*
    if (GETPOST('stock_rmv', 'alpha') && GETPOST('id_prod', 'alpha')){
        // First request to update the value of reel in product_stock
        $sql = "UPDATE ".MAIN_DB_PREFIX."product_stock as p_s ";
        $sql.= "SET p_s.reel = p_s.reel - ".$stock_rmv;
        $sql.= " WHERE p_s.fk_product = ".$id_prod." AND p_s.fk_entrepot = 1";
        $resql = $db->query($sql);
        $db->free($resql);

        // Second request to update the value of stock in product
        $sql = "UPDATE ".MAIN_DB_PREFIX."product as p ";
        $sql.= "JOIN ".MAIN_DB_PREFIX."product_stock as p_s ON p.rowid = p_s.fk_product ";
        $sql.= "SET p.stock = p_s.reel";
        $sql.= " WHERE p.rowid = ".$id_prod;
        $resql = $db->query($sql);
        $db->free($resql);
    }
    */
}


/*
 * View
 */

$sql = "SELECT p.label AS prod_label, p.rowid as prod_id, p.ref as prod_ref, p.description as prod_descr, p.tobuy as prod_tobuy, p.tosell as prod_tosell, p.entity as prod_entity, p.stock AS prod_stock, c_fd.qty AS comm_qty, c_f.date_commande AS comm_date FROM ".MAIN_DB_PREFIX."commande_fournisseur as c_f ";
$sql.= "LEFT JOIN ".MAIN_DB_PREFIX."commande_fournisseurdet AS c_fd ON c_fd.fk_commande = c_f.rowid ";
$sql.= "LEFT JOIN ".MAIN_DB_PREFIX."product_stock AS p_s ON p_s.fk_product = c_fd.fk_product ";
$sql.= "LEFT JOIN ".MAIN_DB_PREFIX."product AS p ON p.rowid = p_s.fk_product ";
$sql.= "ORDER BY p.rowid, c_f.date_commande DESC";

llxHeader("", $langs->trans("DLUArea"), '', '', 0, 0, '', '', '', 'mod-pmpr page-index');

$resql = $db->query($sql);

$nb_prod_max = $db->num_rows($resql);

print load_fiche_titre($langs->trans("DLUArea"), '', 'dlu.png@pmpr');

print '<form method="POST" id="searchFormList" action"'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<label for="limit_period">'.$langs->trans('LIMIT_PERIOD').' : </label>';
print '<input type="date" id="limit_period" name="limit_period" value="'.$limit.'">';
print '<br>';
print '<input type="submit" value="'.$langs->trans("REFRESH").'">';
//print_barre_liste($langs->trans("DLUProducts"), 0, $_SERVER["PHP_SELF"], '', '', '', '', 0, 0, 'product', 0, '', '', 10, 0, 0, 1);
print '</form>';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<th>'.$langs->trans("PRODUCT_REF").'</th>';
print '<th>'.$langs->trans("PRODUCT_LABEL").'</th>';
print '<th>'.$langs->trans("QTY_DLU").'</th>';
print '<th>'.$langs->trans("TOTAL_QTY").'</th>';
print '<th>'.$langs->trans("BUTTON").'</th>';
print '</tr>';

$prod = new Product($db);
if($resql)
{

    if($nb_prod_max > 0)
    {
        $i = 0;
        $stock = 0; // Variable for the 'reel' value in the request
        $qty = 0; // Variable for the quantity in each purchase order
        while ($i < $nb_prod_max)
        {
            $obj = $db->fetch_object($resql);
            // Values to change only when the product changes
            if ($prod->id != $obj->prod_id){
                // Filling the informations about the product to get the link for the product
                $prod->id = $obj->prod_id;
                $prod->ref = $obj->prod_ref;
                $prod->description = $obj->prod_descr;
                $prod->label = $obj->prod_label;
                $prod->status_buy = $obj->prod_tobuy;
                $prod->status = $obj->prod_tosell;
                $prod->entity = $obj->prod_entity;

                // Get the stock reel value to compare with the quantity in the orders
                $stock = $obj->prod_stock;
            }

            // Values to change at every row
            $qty = $obj->comm_qty;
            

            // Printing content for each line only if the $stock > 0 and the limit date is passed
            if($stock > 0 && $limit > $obj->comm_date)
            {
                // getNomUrl() not working, check later to see what is the problem
                print '<tr class="oddeven">';
                print '<td class="nowrap">' . $prod->getNomUrl(1) . '</td>';
                print '<td class="tdoverflowmax200">'.$obj->prod_label.'</td>';
                print '<td class="nowrap">'.$stock.'</td>';
                print '<td class="nowrap">'.$obj->prod_stock.'</td>';
                print '<td class="tdoverflowmax100"><form method="POST" action"'.$_SERVER["PHP_SELF"].'"><input type="hidden" name="token" value="'.newToken().'"><input type="hidden" id="limit_period" name="limit_period" value="'.$limit.'"><input type="hidden" name="stock_rmv" value="'.$stock.'"><input type="hidden" name="id_prod" value="'.$prod->id.'"><input class="butAction" name="update-btn" type="submit" value="'.$langs->trans("UPDATE_STOCK").'"></form></td>';

                print '</tr>';

                $stock = 0;
            }
            $stock-= $qty;
            $i++;
        }
    }
}
print '</table><br>';
$db->free($resql);

llxFooter();
$db->close();