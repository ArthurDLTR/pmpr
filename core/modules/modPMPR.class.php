<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2024  Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2024 SuperAdmin
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
 * 	\defgroup   pmpr     Module PMPR
 *  \brief      PMPR module descriptor.
 *
 *  \file       htdocs/pmpr/core/modules/modPMPR.class.php
 *  \ingroup    pmpr
 *  \brief      Description and activation file for module PMPR
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module PMPR
 */
class modPMPR extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;

		// Loading the lang file
		$langs->load("pmpr@pmpr");

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 207401; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'pmpr';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "Atu home made";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModulePMPRName' not found (PMPR is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// DESCRIPTION_FLAG
		// Module description, used if translation string 'ModulePMPRDesc' not found (PMPR is name of module).
		$this->description = "PMPRDescription";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "PMPRDescription";

		// Author
		$this->editor_name = 'Atu';
		$this->editor_url = '';		// Must be an external online web site
		$this->editor_squarred_logo = '';					// Must be image filename into the module/img directory followed with @modulename. Example: 'myimage.png@pmpr'

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated', 'experimental_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Url to the file with your last numberversion of this module
		//$this->url_last_version = 'http://www.example.com/versionmodule.txt';

		// Key used in llx_const table to save module status enabled/disabled (where PMPR is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = $this->name.'.png@'.$this->name;

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 1,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 1,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 0,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
				//    '/pmpr/css/pmpr.css.php',
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				//   '/pmpr/js/pmpr.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			/* BEGIN MODULEBUILDER HOOKSCONTEXTS */
			'hooks' => array('ordercard', 'globalcard'),
			/* END MODULEBUILDER HOOKSCONTEXTS */
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
			// Set this to 1 if the module provides a website template into doctemplates/websites/website_template-mytemplate
			'websitetemplates' => 0
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/pmpr/temp","/pmpr/subdir");
		$this->dirs = array("/pmpr/temp");

		// Config pages. Put here list of php page, stored into pmpr/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@".$this->name);

		// Dependencies
		// A condition to hide module
		$this->hidden = getDolGlobalInt('MODULE_PMPR_DISABLED'); // A condition to disable module;
		// List of module class names that must be enabled if this module is enabled. Example: array('always'=>array('modModuleToEnable1','modModuleToEnable2'), 'FR'=>array('modModuleToEnableFR')...)
		$this->depends = array();
		// List of module class names to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->requiredby = array();
		// List of module class names this module is in conflict with. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array();

		// The language file dedicated to your module
		$this->langfiles = array("pmpr@pmpr");

		// Prerequisites
		$this->phpmin = array(7, 1); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(19, -3); // Minimum version of Dolibarr required by module
		$this->need_javascript_ajax = 0;

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		//$this->automatic_activation = array('FR'=>'PMPRWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('PMPR_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('PMPR_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array();

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isModEnabled("pmpr")) {
			$conf->pmpr = new stdClass();
			$conf->pmpr->enabled = 0;
		}

		// Array to add new pages in new tabs
		/* BEGIN MODULEBUILDER TABS */
		//$this->tabs = array('product:+DevaluatedProducts:DLU:pmpr@pmpr:/pmpr/dluproducts.php?id=__ID__');
		/* END MODULEBUILDER TABS */
		// Example:
		// To add a new tab identified by code tabname1
		// $this->tabs[] = array('data'=>'objecttype:+tabname1:Title1:mylangfile@pmpr:$user->hasRight('pmpr', 'read'):/pmpr/mynewtab1.php?id=__ID__');
		// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		// $this->tabs[] = array('data'=>'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@pmpr:$user->hasRight('othermodule', 'read'):/pmpr/mynewtab2.php?id=__ID__',
		// To remove an existing tab identified by code tabname
		// $this->tabs[] = array('data'=>'objecttype:-tabname:NU:conditiontoremove');
		//
		// Where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in foundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in sale order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view


		// Dictionaries
		/* Example:
		 $this->dictionaries=array(
		 'langs'=>'pmpr@pmpr',
		 // List of tables we want to see into dictonnary editor
		 'tabname'=>array("table1", "table2", "table3"),
		 // Label of tables
		 'tablib'=>array("Table1", "Table2", "Table3"),
		 // Request to select fields
		 'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),
		 // Sort order
		 'tabsqlsort'=>array("label ASC", "label ASC", "label ASC"),
		 // List of fields (result of select to show dictionary)
		 'tabfield'=>array("code,label", "code,label", "code,label"),
		 // List of fields (list of fields to edit a record)
		 'tabfieldvalue'=>array("code,label", "code,label", "code,label"),
		 // List of fields (list of fields for insert)
		 'tabfieldinsert'=>array("code,label", "code,label", "code,label"),
		 // Name of columns with primary key (try to always name it 'rowid')
		 'tabrowid'=>array("rowid", "rowid", "rowid"),
		 // Condition to show each dictionary
		 'tabcond'=>array(isModEnabled('pmpr'), isModEnabled('pmpr'), isModEnabled('pmpr')),
		 // Tooltip for every fields of dictionaries: DO NOT PUT AN EMPTY ARRAY
		 'tabhelp'=>array(array('code'=>$langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), array('code'=>$langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), ...),
		 );
		 */
		/* BEGIN MODULEBUILDER DICTIONARIES */
		$this->dictionaries = array();
		/* END MODULEBUILDER DICTIONARIES */

		// Boxes/Widgets
		// Add here list of php file(s) stored in pmpr/core/boxes that contains a class to show a widget.
		/* BEGIN MODULEBUILDER WIDGETS */
		$this->boxes = array(
			//  0 => array(
			//      'file' => 'pmprwidget1.php@pmpr',
			//      'note' => 'Widget provided by PMPR',
			//      'enabledbydefaulton' => 'Home',
			//  ),
			//  ...
		);
		/* END MODULEBUILDER WIDGETS */

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		/* BEGIN MODULEBUILDER CRON */
		$this->cronjobs = array(
			//  0 => array(
			//      'label' => 'MyJob label',
			//      'jobtype' => 'method',
			//      'class' => '/pmpr/class/myobject.class.php',
			//      'objectname' => 'MyObject',
			//      'method' => 'doScheduledJob',
			//      'parameters' => '',
			//      'comment' => 'Comment',
			//      'frequency' => 2,
			//      'unitfrequency' => 3600,
			//      'status' => 0,
			//      'test' => 'isModEnabled("pmpr")',
			//      'priority' => 50,
			//  ),
		);
		/* END MODULEBUILDER CRON */
		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'isModEnabled("pmpr")', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'isModEnabled("pmpr")', 'priority'=>50)
		// );

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
		/* BEGIN MODULEBUILDER PERMISSIONS */
		/*
		$o = 1;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($o * 10) + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Read objects of PMPR'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->hasRight('pmpr', 'myobject', 'read'))
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($o * 10) + 2); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/Update objects of PMPR'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->hasRight('pmpr', 'myobject', 'write'))
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", ($o * 10) + 3); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete objects of PMPR'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->hasRight('pmpr', 'myobject', 'delete'))
		$r++;
		*/
		/* END MODULEBUILDER PERMISSIONS */


		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus
		/* BEGIN MODULEBUILDER TOPMENU */
		
		$this->menu[$r++] = array(
			'fk_menu'=>'fk_mainmenu=products', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left', // This is a Top menu entry
			'titre'=>'DLU',
			'leftmenu'=>'DLUProducts',
			'url'=>'/pmpr/dluindex.php',
			'langs'=>'pmpr@pmpr', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100 + $r,
			'enabled'=>'getDolGlobalInt("DLU_YESNO")', // Define condition to show or hide menu entry. Use 'isModEnabled("pmpr")' if entry must be visible if module is enabled.
			'perms'=>'1', // Use 'perms'=>'$user->hasRight("pmpr", "myobject", "read")' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
		);
		
		$this->menu[$r++] = array(
			'fk_menu'=>'fk_mainmenu=products', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left', // This is a Top menu entry
			'titre'=>'DLU_bis',
			'leftmenu'=>'DLUProducts',
			'url'=>'/pmpr/dluindex_bis.php',
			'langs'=>'pmpr@pmpr', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>100 + $r,
			'enabled'=>'getDolGlobalInt("DLU_YESNO")', // Define condition to show or hide menu entry. Use 'isModEnabled("pmpr")' if entry must be visible if module is enabled.
			'perms'=>'1', // Use 'perms'=>'$user->hasRight("pmpr", "myobject", "read")' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
		);

		/* END MODULEBUILDER TOPMENU */

		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT */
		/*
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=pmpr',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'MyObject',
			'prefix' => img_picto('', $this->picto, 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu'=>'pmpr',
			'leftmenu'=>'myobject',
			'url'=>'/pmpr/pmprindex.php',
			'langs'=>'pmpr@pmpr',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("pmpr")', // Define condition to show or hide menu entry. Use 'isModEnabled("pmpr")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("pmpr", "myobject", "read")',
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=pmpr,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'New_MyObject',
			'mainmenu'=>'pmpr',
			'leftmenu'=>'pmpr_myobject_new',
			'url'=>'/pmpr/myobject_card.php?action=create',
			'langs'=>'pmpr@pmpr',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("pmpr")', // Define condition to show or hide menu entry. Use 'isModEnabled("pmpr")' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->hasRight("pmpr", "myobject", "write")'
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=pmpr,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'List_MyObject',
			'mainmenu'=>'pmpr',
			'leftmenu'=>'pmpr_myobject_list',
			'url'=>'/pmpr/myobject_list.php',
			'langs'=>'pmpr@pmpr',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'isModEnabled("pmpr")', // Define condition to show or hide menu entry. Use 'isModEnabled("pmpr")' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("pmpr", "myobject", "read")'
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
			'object'=>'MyObject'
		);
		*/
		/* END MODULEBUILDER LEFTMENU MYOBJECT */


		// Exports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER EXPORT MYOBJECT */
		/*
		
		$this->export_code[$r] = $this->rights_class.'_'.$r;
		$this->export_label[$r] = 'MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_icon[$r] = $this->picto;
		// Define $this->export_fields_array, $this->export_TypeFields_array and $this->export_entities_array
		$keyforclass = 'MyObject'; $keyforclassfile='/pmpr/class/myobject.class.php'; $keyforelement='myobject@pmpr';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		//$this->export_fields_array[$r]['t.fieldtoadd']='FieldToAdd'; $this->export_TypeFields_array[$r]['t.fieldtoadd']='Text';
		//unset($this->export_fields_array[$r]['t.fieldtoremove']);
		//$keyforclass = 'MyObjectLine'; $keyforclassfile='/pmpr/class/myobject.class.php'; $keyforelement='myobjectline@pmpr'; $keyforalias='tl';
		//include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		$keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@pmpr';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$keyforselect='myobjectline'; $keyforaliasextra='extraline'; $keyforelement='myobjectline@pmpr';
		//include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$this->export_dependencies_array[$r] = array('myobjectline'=>array('tl.rowid','tl.ref')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		//$this->export_special_array[$r] = array('t.field'=>'...');
		//$this->export_examplevalues_array[$r] = array('t.field'=>'Example');
		//$this->export_help_array[$r] = array('t.field'=>'FieldDescHelp');
		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'pmpr_myobject as t';
		//$this->export_sql_end[$r]  .=' LEFT JOIN '.MAIN_DB_PREFIX.'pmpr_myobject_line as tl ON tl.fk_myobject = t.rowid';
		$this->export_sql_end[$r] .=' WHERE 1 = 1';
		$this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('myobject').')';
		$r++; */
		/* END MODULEBUILDER EXPORT MYOBJECT */

		// Imports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER IMPORT MYOBJECT */
		/*
		$langs->load("pmpr@pmpr");
		$this->import_code[$r] = $this->rights_class.'_'.$r;
		$this->import_label[$r] = 'MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->import_icon[$r] = $this->picto;
		$this->import_tables_array[$r] = array('t' => MAIN_DB_PREFIX.'pmpr_myobject', 'extra' => MAIN_DB_PREFIX.'pmpr_myobject_extrafields');
		$this->import_tables_creator_array[$r] = array('t' => 'fk_user_author'); // Fields to store import user id
		$import_sample = array();
		$keyforclass = 'MyObject'; $keyforclassfile='/pmpr/class/myobject.class.php'; $keyforelement='myobject@pmpr';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinimport.inc.php';
		$import_extrafield_sample = array();
		$keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@pmpr';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinimport.inc.php';
		$this->import_fieldshidden_array[$r] = array('extra.fk_object' => 'lastrowid-'.MAIN_DB_PREFIX.'pmpr_myobject');
		$this->import_regex_array[$r] = array();
		$this->import_examplevalues_array[$r] = array_merge($import_sample, $import_extrafield_sample);
		$this->import_updatekeys_array[$r] = array('t.ref' => 'Ref');
		$this->import_convertvalue_array[$r] = array(
			't.ref' => array(
				'rule'=>'getrefifauto',
				'class'=>(!getDolGlobalString('PMPR_MYOBJECT_ADDON') ? 'mod_myobject_standard' : getDolGlobalString('PMPR_MYOBJECT_ADDON')),
				'path'=>"/core/modules/pmpr/".(!getDolGlobalString('PMPR_MYOBJECT_ADDON') ? 'mod_myobject_standard' : getDolGlobalString('PMPR_MYOBJECT_ADDON')).'.php',
				'classobject'=>'MyObject',
				'pathobject'=>'/pmpr/class/myobject.class.php',
			),
			't.fk_soc' => array('rule' => 'fetchidfromref', 'file' => '/societe/class/societe.class.php', 'class' => 'Societe', 'method' => 'fetch', 'element' => 'ThirdParty'),
			't.fk_user_valid' => array('rule' => 'fetchidfromref', 'file' => '/user/class/user.class.php', 'class' => 'User', 'method' => 'fetch', 'element' => 'user'),
			't.fk_mode_reglement' => array('rule' => 'fetchidfromcodeorlabel', 'file' => '/compta/paiement/class/cpaiement.class.php', 'class' => 'Cpaiement', 'method' => 'fetch', 'element' => 'cpayment'),
		);
		$this->import_run_sql_after_array[$r] = array();
		$r++; */
		/* END MODULEBUILDER IMPORT MYOBJECT */
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		//$result = $this->_load_tables('/install/mysql/', 'pmpr');
		//$result = $this->_load_tables('/pmpr/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Create extrafields during init
		//include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		//$extrafields = new ExtraFields($this->db);
		//$result0=$extrafields->addExtraField('pmpr_separator1', "Separator 1", 'separator', 1,  0, 'thirdparty',   0, 0, '', array('options'=>array(1=>1)), 1, '', 1, 0, '', '', 'pmpr@pmpr', 'isModEnabled("pmpr")');
		//$result1=$extrafields->addExtraField('pmpr_myattr1', "New Attr 1 label", 'boolean', 1,  3, 'thirdparty',   0, 0, '', '', 1, '', -1, 0, '', '', 'pmpr@pmpr', 'isModEnabled("pmpr")');
		//$result2=$extrafields->addExtraField('pmpr_myattr2', "New Attr 2 label", 'varchar', 1, 10, 'project',      0, 0, '', '', 1, '', -1, 0, '', '', 'pmpr@pmpr', 'isModEnabled("pmpr")');
		//$result3=$extrafields->addExtraField('pmpr_myattr3', "New Attr 3 label", 'varchar', 1, 10, 'bank_account', 0, 0, '', '', 1, '', -1, 0, '', '', 'pmpr@pmpr', 'isModEnabled("pmpr")');
		//$result4=$extrafields->addExtraField('pmpr_myattr4', "New Attr 4 label", 'select',  1,  3, 'thirdparty',   0, 1, '', array('options'=>array('code1'=>'Val1','code2'=>'Val2','code3'=>'Val3')), 1,'', -1, 0, '', '', 'pmpr@pmpr', 'isModEnabled("pmpr")');
		//$result5=$extrafields->addExtraField('pmpr_myattr5', "New Attr 5 label", 'text',    1, 10, 'user',         0, 0, '', '', 1, '', -1, 0, '', '', 'pmpr@pmpr', 'isModEnabled("pmpr")');

		// Permissions
		$this->remove($options);

		$sql = array();

		// Document templates
		$moduledir = dol_sanitizeFileName('pmpr');
		$myTmpObjects = array();
		$myTmpObjects['MyObject'] = array('includerefgeneration'=>0, 'includedocgeneration'=>0);

		foreach ($myTmpObjects as $myTmpObjectKey => $myTmpObjectArray) {
			if ($myTmpObjectKey == 'MyObject') {
				continue;
			}
			if ($myTmpObjectArray['includerefgeneration']) {
				$src = DOL_DOCUMENT_ROOT.'/install/doctemplates/'.$moduledir.'/template_myobjects.odt';
				$dirodt = DOL_DATA_ROOT.'/doctemplates/'.$moduledir;
				$dest = $dirodt.'/template_myobjects.odt';

				if (file_exists($src) && !file_exists($dest)) {
					require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
					dol_mkdir($dirodt);
					$result = dol_copy($src, $dest, 0, 0);
					if ($result < 0) {
						$langs->load("errors");
						$this->error = $langs->trans('ErrorFailToCopyFile', $src, $dest);
						return 0;
					}
				}

				$sql = array_merge($sql, array(
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'standard_".strtolower($myTmpObjectKey)."' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('standard_".strtolower($myTmpObjectKey)."', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")",
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'generic_".strtolower($myTmpObjectKey)."_odt' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('generic_".strtolower($myTmpObjectKey)."_odt', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")"
				));
			}
		}

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}

	/**
	 * Function to calculate the PMPR of a product
	 * A quantity to ignore can be determined
	 * 
	 * @param		int		$fk_product		ID of the product
	 * @param		int		$qty			The quantity to ignore of the product, useful only for the dynamic calculation of the pmpr in order card
	 */
	public function calc_PMPR($fk_product, $qty = 0)
	{
		global $db;
		dol_syslog("Calcul du PMPR en cours...");

		// Request to get the informations (stock) about the product
		$sql = 'SELECT p.stock as prod_stock FROM '.MAIN_DB_PREFIX.'product AS p WHERE p.rowid = '.$fk_product;
		$resql = $db->query($sql);
		$obj = $db->fetch_object($resql);
		$stock = $obj->prod_stock;
		$db->free($resql);

		// dol_syslog("Stock du produit ".$fk_product." : ".$stock);

		// Simplify the calculus when the stock is at 0
		if($stock <= 0){
			return 0;
		}

		// Request to get the last purchase orders for this product 
		$sql = 'SELECT c_fd.subprice as cf_subprice, c_fd.remise_percent as cf_remise, c_fd.qty as cf_qty ';
		$sql.= 'FROM '.MAIN_DB_PREFIX.'commande_fournisseur as c_f ';
		$sql.= 'LEFT JOIN '.MAIN_DB_PREFIX.'commande_fournisseurdet AS c_fd ON c_fd.fk_commande = c_f.rowid ';
		$sql.= 'LEFT JOIN '.MAIN_DB_PREFIX.'product_stock AS p_s ON p_s.fk_product = c_fd.fk_product ';
		$sql.= 'WHERE c_fd.fk_product = '.$fk_product.' ';
		$sql.= 'ORDER BY c_f.date_creation DESC';

		// Variables
		$real_qty = $stock - $qty;
		// Preventing the case where the quantity wanted is higher than the stock
		if ($real_qty < 0){
			$real_qty = 0;
		}
		$tmp_qty = $real_qty; // To decrease the quantity until all the needed products are considered
		$unused_qty = $qty; // The quantity of product to ignore
		$total = 0; // The total price of this quantity of the product
		$real_price = 0; // The result to print on command line

		$resql = $db->query($sql);
		if(!$resql){
			exit(0);
		}
		// dol_syslog(" Variables : quantitée à ignorer : ".$qty." quantitée à prendre en compte : ".$real_qty);
		// Looping through the last purchase order for this product to get the needed informations
		while($tmp_qty > 0){
			// On each loop, get the next older order
			$obj_tmp = $db->fetch_object($resql);
			// Buying price for this purchase order
			$subprice = $obj_tmp->cf_subprice;
			// Discount on this product
			$remise = 1-$obj_tmp->cf_remise/100;
			// Quantity of this product
			$prod_qty = $obj_tmp->cf_qty;
			// dol_syslog("Infos sur la commande fournisseur qty = ".$prod_qty." remise = ".$remise." et prix unitaire =".$subprice);
			// Checking if the quantity in the order is less important than the quantity needed for the calculations
			// To update $tmp_qty depending on $prod_qty
			if ($prod_qty >= $tmp_qty){
				// Total HT for the product * remise in percent
				$total+= ($tmp_qty-$unused_qty)*($subprice*$remise);
				// All the temporary variables decreased to 0 to exit the loop
				$tmp_qty = 0;
				$unused_qty = 0;
				$real_price = $total / $real_qty;
			} else {
				$total+= ($prod_qty-$unused_qty)*($subprice*$remise);
				// Adapt the formula depending on the comparaison between the quantity left after the command and the quantity wanted
				if ($unused_qty > $prod_qty){
					$unused_qty-= $prod_qty;
				} else {
					$unused_qty = 0;	
				}
				// Update the quantity to break from the loop when needed
				$tmp_qty-= $prod_qty;
				// dol_syslog("Calcul en cours, pour le moment total = ".$total." avec ".$tmp_qty." produits restants à prendre en compte");
			}

			/* Old version
			// A refaire completement car plus du tout adapté dans le cas où on utilise un trigger
			if ($prod_qty >= $tmp_qty){
				//$new_pmp = ($new_pmp * ($qty - $tmp_qty) + $tmp_qty * ($subprice * $remise)) / $qty;
				$total+= $tmp_qty * ($subprice * $remise);
				if($unused_qty > 0){
					$total-= $unused_qty * ($subprice * $remise);
				}
				$tmp_qty = 0; // Put all the temporary variables at 0 to break out of the loop
				$unused_qty = 0;
				// Division to get the real price for margins
				$real_price = $total / $real_qty; // Need a new variable to prevent the case where the quantity in the command is more important than the stock
			} else {
				//$new_pmp = ($new_pmp * ($qty - $tmp_qty) + ($tmp_qty - $prod_qty) * ($subprice * $remise)) / ($qty - $tmp_qty);
				$total+= $prod_qty * ($subprice * $remise);
				
				// Adapt the formula depending on the comparaison between the quantity left after the command and the quantity wanted
				if ($unused_qty > $prod_qty){
					$unused_qty-= $prod_qty;
				} else {
					$unused_qty = 0;	
				}
				// Update the quantity to break from the loop when needed
				$tmp_qty-= $prod_qty;
			}
			*/
		}
		$db->free($resql);
		dol_syslog("total : ".$total." Prix réel : ".$real_price." quantité prise en compte : ".$real_qty);
		return $real_price;
	}
}
