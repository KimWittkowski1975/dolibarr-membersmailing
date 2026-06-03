<?php
/* Copyright (C) 2026 Kim Wittkowski <kim@wittkowski-it.de>
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
 * \defgroup   membersmailing     Module MembersMailing
 * \brief      MembersMailing module descriptor.
 *
 * \file       htdocs/custom/membersmailing/core/modules/modMembersMailing.class.php
 * \ingroup    membersmailing
 * \brief      Description and activation file for module MembersMailing
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 * Description and activation class for module MembersMailing
 */
class modMembersMailing extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $conf, $langs;

		$this->db = $db;

		// Id for module (must be unique).
		$this->numero = 550070;

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'membersmailing';

		// Family can be 'base','crm','financial','hr','projects','products','ecm','technic','interface','other'
		$this->family = "crm";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '95';

		// Module label (no space allowed)
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Module description
		$this->description = "Enhanced member mailing selector with extrafield filters";
		$this->descriptionlong = "Provides an extended mailing target selector for foundation members with additional extrafield filters (email-group). Based on Dolibarr core fraise.modules.php with additional filter capabilities.";

		// Author
		$this->editor_name = 'Kim Wittkowski';
		$this->editor_url = 'https://wittkowski-it.de';

		// Version
		$this->version = '1.0.0';

		// Key used in llx_const table to save module status
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Module icon
		$this->picto = 'email';

		// Define some features supported by module
		$this->module_parts = array(
			'triggers' => 0,
			'login' => 0,
			'substitutions' => 0,
			'menus' => 0,
			'tpl' => 0,
			'barcode' => 0,
			'models' => 0,
			'printing' => 0,
			'theme' => 0,
			'css' => array(),
			'js' => array(),
			'hooks' => array(),
			'moduleforexternal' => 0,
		);

		// Data directories to create when module is enabled
		$this->dirs = array();

		// Config pages
		$this->config_page_url = array("setup.php@membersmailing");

		// Dependencies
		$this->hidden = false;
		$this->depends = array('modAdherent', 'modMailing');
		$this->requiredby = array();
		$this->conflictwith = array();

		// Constants
		$this->const = array();

		// Permissions
		$this->rights = array();

		// Main menu entries
		$this->menu = array();
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		$result = $this->_load_tables('/membersmailing/sql/');
		if ($result < 0) {
			return -1;
		}

		return $this->_init($this->depends, $this->conflictwith, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when disabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		return $this->_remove($this->depends, $options);
	}
}
