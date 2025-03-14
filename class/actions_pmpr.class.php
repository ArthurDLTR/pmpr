<?php
/* Copyright (C) 2024 Arthur LENOBLE <arthurl52100@gmail.com>
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
 *  \file       htdocs/pmpr/class/actions_pmpr.class.php
 *  \ingroup    pmpr
 *  \brief      class for the hook actions of pmpr
 */

class ActionsPMPR
{


	/**
	 * Overloading the addMoreMassActions function : replacing the parent function with this one below
	 * @param 	parameters		
	 */
	function formAddObjectLine($parameters, &$model, &$action, $hookmanager)
	{
		global $langs, $conf;

		global $mysoc, $soc;
		// $line vide
		// $form ne contient pas d'infos intéressantes
		// $object rien non plus 
		// $mysoc contient les informations de ma société dans Doliabrr
		// $soc contient les inforamtions de la société concerné par la commande 
		
		foreach ($object as $tr){
			if(get_class($tr) != 'TraceableDB'|| get_class($tr) != 'OrderLine'){
				print 'tr : '.$tr.'<br>';
				foreach($tr as $td)
				if(get_class($td) != 'TraceableDB'|| get_class($td) != 'OrderLine'){
					print 'td : '.$td.'<br>';
				}
			}
		}
		
		//dol_syslog("Hook enclenché dans une commande client par le module PMPR");

		if(in_array($parameters['currentcontext'], 'ordercard')){
			$ret = '<p value="PMPR">Pmpr : prix calculé dynamiquement (je crois)</p>';

			$this->resprints = $ret;
		}


		return 0;
	}


	/**
	 * Overloading _create function : replacing the parent's function with the one below
	 * 	@param		array			$parameters			Meta data of the hook (context, etc...)
	 * 	@param		MouvementStock	$object				The object you want to process
	 * 	@param						$action				Current action (if set)
	 */
	function stockMouvementCreate($parameters, $object, $action){

	}

    /**
     * Overloading _create function : replacing the parent's function with the one below
     * 
     *	@param		User			$user				User object
	 *	@param		int				$fk_product			Id of product
	 *	@param		int				$entrepot_id		Id of warehouse
	 *	@param		float			$qty				Qty of movement (can be <0 or >0 depending on parameter type)
	 *	@param		int				$type				Direction of movement:
	 *													0=input (stock increase by a stock transfer), 1=output (stock decrease by a stock transfer),
	 *													2=output (stock decrease), 3=input (stock increase)
	 *                          		            	Note that qty should be > 0 with 0 or 3, < 0 with 1 or 2.
	 *	@param		int				$price				Unit price HT of product, used to calculate average weighted price (AWP or PMP in french). If 0, average weighted price is not changed.
	 *	@param		string			$label				Label of stock movement
	 *	@param		string			$inventorycode		Inventory code
	 *	@param		integer|string	$datem				Force date of movement
	 *	@param		integer|string	$eatby				eat-by date. Will be used if lot does not exists yet and will be created.
	 *	@param		integer|string	$sellby				sell-by date. Will be used if lot does not exists yet and will be created.
	 *	@param		string			$batch				batch number
	 *	@param		boolean			$skip_batch			If set to true, stock movement is done without impacting batch record
	 * 	@param		int				$id_product_batch	Id product_batch (when skip_batch is false and we already know which record of product_batch to use)
	 *  @param		int				$disablestockchangeforsubproduct	Disable stock change for sub-products of kit (useful only if product is a subproduct)
	 *  @param		int				$donotcleanemptylines				Do not clean lines in stock table with qty=0 (because we want to have this done by the caller)
	 * 	@param		boolean			$force_update_batch	Allows to add batch stock movement even if $product doesn't use batch anymore
	 *	@return		int									Return integer <0 if KO, 0 if fk_product is null or product id does not exists, >0 if OK
	 */
    /*
	public function stockMouvementCreate($user, $fk_product, $entrepot_id, $qty, $type, $price = 0, $label = '', $inventorycode = '', $datem = '', $eatby = '', $sellby = '', $batch = '', $skip_batch = false, $id_product_batch = 0, $disablestockchangeforsubproduct = 0, $donotcleanemptylines = 0, $force_update_batch = false)
	{
		// phpcs:enable
		global $conf, $langs;

		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		require_once DOL_DOCUMENT_ROOT.'/product/stock/class/productlot.class.php';
		require_once '../core/modules/modPMPR.class.php';

		$error = 0;
		dol_syslog(get_class($this)."::_create start userid=$user->id, fk_product=$fk_product, warehouse_id=$entrepot_id, qty=$qty, type=$type, price=$price, label=$label, inventorycode=$inventorycode, datem=".$datem.", eatby=".$eatby.", sellby=".$sellby.", batch=".$batch.", skip_batch=".json_encode($skip_batch));

		// Call hook at beginning
		global $action, $hookmanager;
		$hookmanager->initHooks(array('mouvementstock'));

		if (is_object($hookmanager)) {
			$parameters = array(
				'currentcontext'   => 'mouvementstock',
				'user'             => &$user,
				'fk_product'       => &$fk_product,
				'entrepot_id'      => &$entrepot_id,
				'qty'              => &$qty,
				'type'             => &$type,
				'price'            => &$price,
				'label'            => &$label,
				'inventorycode'    => &$inventorycode,
				'datem'            => &$datem,
				'eatby'            => &$eatby,
				'sellby'           => &$sellby,
				'batch'            => &$batch,
				'skip_batch'       => &$skip_batch,
				'id_product_batch' => &$id_product_batch
			);
			$reshook = $hookmanager->executeHooks('stockMovementCreate', $parameters, $this, $action);    // Note that $action and $object may have been modified by some hooks

			if ($reshook < 0) {
				if (!empty($hookmanager->resPrint)) {
					dol_print_error(null, $hookmanager->resPrint);
				}
				return $reshook;
			} elseif ($reshook > 0) {
				return $reshook;
			}
		}
		// end hook at beginning

		// Clean parameters
		$price = price2num($price, 'MU'); // Clean value for the casse we receive a float zero value, to have it a real zero value.
		if (empty($price)) {
			$price = 0;
		}
		$now = (!empty($datem) ? $datem : dol_now());

		// Check parameters
		if (!($fk_product > 0)) {
			return 0;
		}
		if (!($entrepot_id > 0)) {
			return 0;
		}

		if (is_numeric($eatby) && $eatby < 0) {
			dol_syslog(get_class($this)."::_create start ErrorBadValueForParameterEatBy eatby = ".$eatby);
			$this->errors[] = 'ErrorBadValueForParameterEatBy';
			return -1;
		}
		if (is_numeric($sellby) && $sellby < 0) {
			dol_syslog(get_class($this)."::_create start ErrorBadValueForParameterSellBy sellby = ".$sellby);
			$this->errors[] = 'ErrorBadValueForParameterSellBy';
			return -1;
		}

		// Set properties of movement
		$this->product_id = $fk_product;
		$this->entrepot_id = $entrepot_id; // deprecated
		$this->warehouse_id = $entrepot_id;
		$this->qty = $qty;
		$this->type = $type;
		$this->price = price2num($price);
		$this->label = $label;
		$this->inventorycode = $inventorycode;
		$this->datem = $now;
		$this->batch = $batch;

		$mvid = 0;

		$product = new Product($this->db);

		$result = $product->fetch($fk_product);
		if ($result < 0) {
			$this->error = $product->error;
			$this->errors = $product->errors;
			dol_print_error(null, "Failed to fetch product");
			return -1;
		}
		if ($product->id <= 0) {	// Can happen if database is corrupted (a product id exist in stock with product that has been removed)
			return 0;
		}

		// Define if we must make the stock change (If product type is a service or if stock is used also for services)
		// Only record into stock tables will be disabled by this (the rest like writing into lot table or movement of subproucts are done)
		$movestock = 0;
		if ($product->type != Product::TYPE_SERVICE || getDolGlobalString('STOCK_SUPPORTS_SERVICES')) {
			$movestock = 1;
		}

		$this->db->begin();

		// Set value $product->stock_reel and detail per warehouse into $product->stock_warehouse array
		if ($movestock) {
			$product->load_stock('novirtual');
		}

		// Test if product require batch data. If yes, and there is not or values are not correct, we throw an error.
		if (isModEnabled('productbatch') && $product->hasbatch() && !$skip_batch) {
			if (empty($batch)) {
				$langs->load("errors");
				$this->errors[] = $langs->transnoentitiesnoconv("ErrorTryToMakeMoveOnProductRequiringBatchData", $product->ref);
				dol_syslog("Try to make a movement of a product with status_batch on without any batch data", LOG_ERR);

				$this->db->rollback();
				return -2;
			}

			// Check table llx_product_lot from batchnumber for same product
			// If found and eatby/sellby defined into table and provided and differs, return error
			// If found and eatby/sellby defined into table and not provided, we take value from table
			// If found and eatby/sellby not defined into table and provided, we update table
			// If found and eatby/sellby not defined into table and not provided, we do nothing
			// If not found, we add record
			$sql = "SELECT pb.rowid, pb.batch, pb.eatby, pb.sellby FROM ".$this->db->prefix()."product_lot as pb";
			$sql .= " WHERE pb.fk_product = ".((int) $fk_product)." AND pb.batch = '".$this->db->escape($batch)."'";

			dol_syslog(get_class($this)."::_create scan serial for this product to check if eatby and sellby match", LOG_DEBUG);

			$resql = $this->db->query($sql);
			if ($resql) {
				$num = $this->db->num_rows($resql);
				$i = 0;
				if ($num > 0) {
					while ($i < $num) {
						$obj = $this->db->fetch_object($resql);
						if ($obj->eatby) {
							if ($eatby) {
								$tmparray = dol_getdate($eatby, true);
								$eatbywithouthour = dol_mktime(0, 0, 0, $tmparray['mon'], $tmparray['mday'], $tmparray['year']);
								if ($this->db->jdate($obj->eatby) != $eatby && $this->db->jdate($obj->eatby) != $eatbywithouthour) {    // We test date without hours and with hours for backward compatibility
									// If found and eatby/sellby defined into table and provided and differs, return error
									$langs->load("stocks");
									$this->errors[] = $langs->transnoentitiesnoconv("ThisSerialAlreadyExistWithDifferentDate", $batch, dol_print_date($this->db->jdate($obj->eatby), 'dayhour'), dol_print_date($eatbywithouthour, 'dayhour'));
									dol_syslog("ThisSerialAlreadyExistWithDifferentDate batch=".$batch.", eatby found into product_lot = ".$obj->eatby." = ".dol_print_date($this->db->jdate($obj->eatby), 'dayhourrfc')." so eatbywithouthour = ".$eatbywithouthour." = ".dol_print_date($eatbywithouthour)." - eatby provided = ".$eatby." = ".dol_print_date($eatby, 'dayhourrfc'), LOG_ERR);
									$this->db->rollback();
									return -3;
								}
							} else {
								$eatby = $obj->eatby; // If found and eatby/sellby defined into table and not provided, we take value from table
							}
						} else {
							if ($eatby) { // If found and eatby/sellby not defined into table and provided, we update table
								$productlot = new Productlot($this->db);
								$result = $productlot->fetch($obj->rowid);
								$productlot->eatby = $eatby;
								$result = $productlot->update($user);
								if ($result <= 0) {
									$this->error = $productlot->error;
									$this->errors = $productlot->errors;
									$this->db->rollback();
									return -5;
								}
							}
						}
						if ($obj->sellby) {
							if ($sellby) {
								$tmparray = dol_getdate($sellby, true);
								$sellbywithouthour = dol_mktime(0, 0, 0, $tmparray['mon'], $tmparray['mday'], $tmparray['year']);
								if ($this->db->jdate($obj->sellby) != $sellby && $this->db->jdate($obj->sellby) != $sellbywithouthour) {    // We test date without hours and with hours for backward compatibility
									// If found and eatby/sellby defined into table and provided and differs, return error
									$this->errors[] = $langs->transnoentitiesnoconv("ThisSerialAlreadyExistWithDifferentDate", $batch, dol_print_date($this->db->jdate($obj->sellby)), dol_print_date($sellby));
									dol_syslog($langs->transnoentities("ThisSerialAlreadyExistWithDifferentDate", $batch, dol_print_date($this->db->jdate($obj->sellby)), dol_print_date($sellby)), LOG_ERR);
									$this->db->rollback();
									return -3;
								}
							} else {
								$sellby = $obj->sellby; // If found and eatby/sellby defined into table and not provided, we take value from table
							}
						} else {
							if ($sellby) { // If found and eatby/sellby not defined into table and provided, we update table
								$productlot = new Productlot($this->db);
								$result = $productlot->fetch($obj->rowid);
								$productlot->sellby = $sellby;
								$result = $productlot->update($user);
								if ($result <= 0) {
									$this->error = $productlot->error;
									$this->errors = $productlot->errors;
									$this->db->rollback();
									return -5;
								}
							}
						}

						$i++;
					}
				} else { // If not found, we add record
					$productlot = new Productlot($this->db);
					$productlot->origin = !empty($this->origin_type) ? $this->origin_type : '';
					$productlot->origin_id = !empty($this->origin_id) ? $this->origin_id : 0;
					$productlot->entity = $conf->entity;
					$productlot->fk_product = $fk_product;
					$productlot->batch = $batch;
					// If we are here = first time we manage this batch, so we used dates provided by users to create lot
					$productlot->eatby = $eatby;
					$productlot->sellby = $sellby;
					$result = $productlot->create($user);
					if ($result <= 0) {
						$this->error = $productlot->error;
						$this->errors = $productlot->errors;
						$this->db->rollback();
						return -4;
					}
				}
			} else {
				dol_print_error($this->db);
				$this->db->rollback();
				return -1;
			}
		}

		// Check if stock is enough when qty is < 0
		// Note that qty should be > 0 with type 0 or 3, < 0 with type 1 or 2.
		if ($movestock && $qty < 0 && !getDolGlobalInt('STOCK_ALLOW_NEGATIVE_TRANSFER')) {
			if (isModEnabled('productbatch') && $product->hasbatch() && !$skip_batch) {
				$foundforbatch = 0;
				$qtyisnotenough = 0;
				if (isset($product->stock_warehouse[$entrepot_id])) {
					foreach ($product->stock_warehouse[$entrepot_id]->detail_batch as $batchcursor => $prodbatch) {
						if ((string) $batch != (string) $batchcursor) {        // Lot '59' must be different than lot '59c'
							continue;
						}

						$foundforbatch = 1;
						if ($prodbatch->qty < abs($qty)) {
							$qtyisnotenough = $prodbatch->qty;
						}
						break;
					}
				}
				if (!$foundforbatch || $qtyisnotenough) {
					$langs->load("stocks");
					include_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
					$tmpwarehouse = new Entrepot($this->db);
					$tmpwarehouse->fetch($entrepot_id);

					$this->error = $langs->trans('qtyToTranferLotIsNotEnough', $product->ref, $batch, $qtyisnotenough, $tmpwarehouse->ref);
					$this->errors[] = $langs->trans('qtyToTranferLotIsNotEnough', $product->ref, $batch, $qtyisnotenough, $tmpwarehouse->ref);
					$this->db->rollback();
					return -8;
				}
			} else {
				if (isset($product->stock_warehouse[$entrepot_id]) && (empty($product->stock_warehouse[$entrepot_id]->real) || $product->stock_warehouse[$entrepot_id]->real < abs($qty))) {
					$langs->load("stocks");
					$this->error = $langs->trans('qtyToTranferIsNotEnough').' : '.$product->ref;
					$this->errors[] = $langs->trans('qtyToTranferIsNotEnough').' : '.$product->ref;
					$this->db->rollback();
					return -8;
				}
			}
		}

		if ($movestock) {	// Change stock for current product, change for subproduct is done after
			// Set $origin_type, origin_id and fk_project
			$fk_project = $this->fk_project;
			if (!empty($this->origin_type)) {			// This is set by caller for tracking reason
				$origin_type = $this->origin_type;
				$origin_id = $this->origin_id;
				if (empty($fk_project) && $origin_type == 'project') {
					$fk_project = $origin_id;
					$origin_type = '';
					$origin_id = 0;
				}
			} else {
				$fk_project = 0;
				$origin_type = '';
				$origin_id = 0;
			}

			$sql = "INSERT INTO ".$this->db->prefix()."stock_mouvement(";
			$sql .= " datem, fk_product, batch, eatby, sellby,";
			$sql .= " fk_entrepot, value, type_mouvement, fk_user_author, label, inventorycode, price, fk_origin, origintype, fk_projet";
			$sql .= ")";
			$sql .= " VALUES ('".$this->db->idate($this->datem)."', ".((int) $this->product_id).", ";
			$sql .= " ".($batch ? "'".$this->db->escape($batch)."'" : "null").", ";
			$sql .= " ".($eatby ? "'".$this->db->idate($eatby)."'" : "null").", ";
			$sql .= " ".($sellby ? "'".$this->db->idate($sellby)."'" : "null").", ";
			$sql .= " ".((int) $this->entrepot_id).", ".((float) $this->qty).", ".((int) $this->type).",";
			$sql .= " ".((int) $user->id).",";
			$sql .= " '".$this->db->escape($label)."',";
			$sql .= " ".($inventorycode ? "'".$this->db->escape($inventorycode)."'" : "null").",";
			$sql .= " ".((float) price2num($price)).",";
			$sql .= " ".((int) $origin_id).",";
			$sql .= " '".$this->db->escape($origin_type)."',";
			$sql .= " ".((int) $fk_project);
			$sql .= ")";

			dol_syslog(get_class($this)."::_create insert record into stock_mouvement", LOG_DEBUG);
			$resql = $this->db->query($sql);

			if ($resql) {
				$mvid = $this->db->last_insert_id($this->db->prefix()."stock_mouvement");
				$this->id = $mvid;
			} else {
				$this->error = $this->db->lasterror();
				$this->errors[] = $this->error;
				$error = -1;
			}

			// Define current values for qty and pmp
			$oldqty = $product->stock_reel;
			$oldpmp = $product->pmp;
			$oldqtywarehouse = 0;

			// Test if there is already a record for couple (warehouse / product), so later we will make an update or create.
			$alreadyarecord = 0;
			if (!$error) {
				$sql = "SELECT rowid, reel FROM ".$this->db->prefix()."product_stock";
				$sql .= " WHERE fk_entrepot = ".((int) $entrepot_id)." AND fk_product = ".((int) $fk_product); // This is a unique key

				dol_syslog(get_class($this)."::_create check if a record already exists in product_stock", LOG_DEBUG);
				$resql = $this->db->query($sql);
				if ($resql) {
					$obj = $this->db->fetch_object($resql);
					if ($obj) {
						$alreadyarecord = 1;
						$oldqtywarehouse = $obj->reel;
						$fk_product_stock = $obj->rowid;
					}
					$this->db->free($resql);
				} else {
					$this->errors[] = $this->db->lasterror();
					$error = -2;
				}
			}

			// Calculate new AWP (PMP)
			$newpmp = 0;
			if (!$error) {
				if ($type == 0 || $type == 3) {
					// After a stock increase
					// Note: PMP is calculated on stock input only (type of movement = 0 or 3). If type == 0 or 3, qty should be > 0.
					// Note: Price should always be >0 or 0. PMP should be always >0 (calculated on input)
					if ($price > 0 || (getDolGlobalString('STOCK_UPDATE_AWP_EVEN_WHEN_ENTRY_PRICE_IS_NULL') && $price == 0 && in_array($this->origin_type, array('order_supplier', 'invoice_supplier')))) {
						$oldqtytouse = ($oldqty >= 0 ? $oldqty : 0);
						// We make a test on oldpmp>0 to avoid to use normal rule on old data with no pmp field defined
						if ($oldpmp > 0) {
							$newpmp = price2num((($oldqtytouse * $oldpmp) + ($qty * $price)) / ($oldqtytouse + $qty), 'MU');
						} else {
							$newpmp = $price; // For this product, PMP was not yet set. We set it to input price.
						}
						//print "oldqtytouse=".$oldqtytouse." oldpmp=".$oldpmp." oldqtywarehousetouse=".$oldqtywarehousetouse." ";
						//print "qty=".$qty." newpmp=".$newpmp;
						//exit;
					} else {
						$newpmp = $oldpmp;
					}
				} else {
					// Cases of correction of the stock in Dolibarr or movements out of stock
					if ($type == 1 || $type == 2){
						if ($qty > 0){
							$pmpr = new PMPR($db);
							$newpmp = $pmpr->calc_PMPR($qty, $stock, $fk_product);
						} else {
							// If the stock decrease to 0, no need to calculate the AWP
							$newpmp = $oldpmp;
						}
						
					}
					//   -> After a stock decrease, we don't change value of the AWP/PMP of a product.
					// else
					//   Type of movement unknown
					//$newpmp = $oldpmp;
				}
			}
			// Update stock quantity
			if (!$error) {
				if ($alreadyarecord > 0) {
					$sql = "UPDATE ".$this->db->prefix()."product_stock SET reel = " . ((float) $oldqtywarehouse + (float) $qty);
					$sql .= " WHERE fk_entrepot = ".((int) $entrepot_id)." AND fk_product = ".((int) $fk_product);
				} else {
					$sql = "INSERT INTO ".$this->db->prefix()."product_stock";
					$sql .= " (reel, fk_entrepot, fk_product) VALUES ";
					$sql .= " (".((float) $qty).", ".((int) $entrepot_id).", ".((int) $fk_product).")";
				}

				dol_syslog(get_class($this)."::_create update stock value", LOG_DEBUG);
				$resql = $this->db->query($sql);
				if (!$resql) {
					$this->errors[] = $this->db->lasterror();
					$error = -3;
				} elseif (empty($fk_product_stock)) {
					$fk_product_stock = $this->db->last_insert_id($this->db->prefix()."product_stock");
				}
			}

			// Update detail of stock for the lot.
			if (!$error && isModEnabled('productbatch') && (($product->hasbatch() && !$skip_batch) || $force_update_batch)) {
				if ($id_product_batch > 0) {
					$result = $this->createBatch($id_product_batch, $qty);
					if ($result == -2 && $fk_product_stock > 0) {	// The entry for this product batch does not exists anymore, bu we already have a llx_product_stock, so we recreate the batch entry in product_batch
						$param_batch = array('fk_product_stock' => $fk_product_stock, 'batchnumber' => $batch);
						$result = $this->createBatch($param_batch, $qty);
					}
				} else {
					$param_batch = array('fk_product_stock' => $fk_product_stock, 'batchnumber' => $batch);
					$result = $this->createBatch($param_batch, $qty);
				}
				if ($result < 0) {
					$error++;
				}
			}

			// Update PMP and denormalized value of stock qty at product level
			if (!$error) {
				$newpmp = price2num($newpmp, 'MU');

				// $sql = "UPDATE ".$this->db->prefix()."product SET pmp = ".$newpmp.", stock = ".$this->db->ifsql("stock IS NULL", 0, "stock") . " + ".$qty;
				// $sql.= " WHERE rowid = ".((int) $fk_product);
				// Update pmp + denormalized fields because we change content of produt_stock. Warning: Do not use "SET p.stock", does not works with pgsql
				$sql = "UPDATE ".$this->db->prefix()."product as p SET pmp = ".((float) $newpmp).",";
				$sql .= " stock=(SELECT SUM(ps.reel) FROM ".$this->db->prefix()."product_stock as ps WHERE ps.fk_product = p.rowid)";
				$sql .= " WHERE rowid = ".((int) $fk_product);

				dol_syslog(get_class($this)."::_create update AWP", LOG_DEBUG);
				$resql = $this->db->query($sql);
				if (!$resql) {
					$this->errors[] = $this->db->lasterror();
					$error = -4;
				}
			}

			if (empty($donotcleanemptylines)) {
				// If stock is now 0, we can remove entry into llx_product_stock, but only if there is no child lines into llx_product_batch (detail of batch, because we can imagine
				// having a lot1/qty=X and lot2/qty=-X, so 0 but we must not loose repartition of different lot.
				$sql = "DELETE FROM ".$this->db->prefix()."product_stock WHERE reel = 0 AND rowid NOT IN (SELECT fk_product_stock FROM ".$this->db->prefix()."product_batch as pb)";
				$resql = $this->db->query($sql);
				// We do not test error, it can fails if there is child in batch details
			}
		}

		// Add movement for sub products (recursive call)
		if (!$error && getDolGlobalString('PRODUIT_SOUSPRODUITS') && !getDolGlobalString('INDEPENDANT_SUBPRODUCT_STOCK') && empty($disablestockchangeforsubproduct)) {
			$error = $this->_createSubProduct($user, $fk_product, $entrepot_id, $qty, $type, 0, $label, $inventorycode, $datem); // we use 0 as price, because AWP must not change for subproduct
		}

		if ($movestock && !$error) {
			// Call trigger
			$result = $this->call_trigger('STOCK_MOVEMENT', $user);
			if ($result < 0) {
				$error++;
			}
			// End call triggers
			// Check unicity for serial numbered equipment once all movement were done.
			if (!$error && isModEnabled('productbatch') && $product->hasbatch() && !$skip_batch) {
				if ($product->status_batch == 2 && $qty > 0) {	// We check only if we increased qty
					if ($this->getBatchCount($fk_product, $batch) > 1) {
						$error++;
						$this->errors[] = $langs->trans("TooManyQtyForSerialNumber", $product->ref, $batch);
					}
				}
			}
		}

		if (!$error) {
			$this->db->commit();
			return $mvid;
		} else {
			$this->db->rollback();
			dol_syslog(get_class($this)."::_create error code=".$error, LOG_ERR);
			return -6;
		}
	}
		*/
}