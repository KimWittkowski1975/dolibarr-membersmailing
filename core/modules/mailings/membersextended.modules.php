<?php
/* Copyright (C) 2026 Kim Wittkowski <kim@wittkowski-it.de>
 * Based on fraise.modules.php by Laurent Destailleur <eldy@users.sourceforge.net>
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
 * \file       htdocs/custom/membersmailing/core/modules/mailings/mailing_membersextended.modules.php
 * \ingroup    membersmailing
 * \brief      Extended mailing target selector for members with extrafield filters
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/mailings/modules_mailings.php';
include_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';


/**
 *    Class to generate mailing targets for members with extrafield filters
 */
class mailing_membersextended extends MailingTargets
{
	public $name = 'MembersExtended';
	public $desc = 'Foundation members with extrafield filters (email-group)';
	public $require_admin = 0;
	public $require_module = array('adherent');
	public $enabled = 'isModEnabled("member")';
	public $picto = 'user';


	/**
	 *    Constructor
	 *
	 *  @param        DoliDB        $db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 *    On the main mailing area, there is a box with statistics.
	 *
	 *    @return        string[]        Array with SQL requests
	 */
	public function getSqlArrayForStats()
	{
		global $langs;

		$langs->load("members");

		$statssql = array();

		$statssql[0] = "SELECT '".$this->db->escape($langs->trans("FundationMembers").' (Extended)')."' as label, count(*) as nb";
		$statssql[0] .= " FROM ".MAIN_DB_PREFIX."adherent where statut = 1 and entity IN (".getEntity('member').")";

		return $statssql;
	}


	/**
	 *    Return here number of distinct emails returned by your selector.
	 *
	 *    @param      string    	$sql        SQL query for counting
	 *    @return     int|string      			Nb of recipient, or <0 if error, or '' if NA
	 */
	public function getNbOfRecipients($sql = '')
	{
		global $conf;
		$sql  = "SELECT count(distinct(a.email)) as nb";
		$sql .= " FROM ".MAIN_DB_PREFIX."adherent as a";
		$sql .= " WHERE (a.email IS NOT NULL AND a.email != '') AND a.entity IN (".getEntity('member').")";
		if (empty($this->evenunsubscribe)) {
			$sql .= " AND NOT EXISTS (SELECT rowid FROM ".MAIN_DB_PREFIX."mailing_unsubscribe as mu WHERE mu.email = a.email and mu.entity = ".((int) $conf->entity).")";
		}

		return parent::getNbOfRecipients($sql);
	}


	/**
	 *   Display filter form in recipient selection page
	 *
	 *   @return     string      Returns select zone
	 */
	public function formFilter()
	{
		global $conf, $langs;

		$langs->loadLangs(array("members", "companies", "categories"));

		$form = new Form($this->db);

		$s = '';

		// ========== STATUS FILTER ==========
		$s .= '<select id="filter_membersext" name="filter" class="flat">';
		$s .= '<option value="-1">'.$langs->trans("Status").'</option>';
		$s .= '<option value="draft">'.$langs->trans("MemberStatusDraft").'</option>';
		$s .= '<option value="1a">'.$langs->trans("MemberStatusActiveShort").' ('.$langs->trans("MemberStatusPaidShort").')</option>';
		$s .= '<option value="1b">'.$langs->trans("MemberStatusActiveShort").' ('.$langs->trans("MemberStatusActiveLateShort").')</option>';
		$s .= '<option value="0">'.$langs->trans("MemberStatusResiliatedShort").'</option>';
		$s .= '</select> ';
		$s .= ajax_combobox("filter_membersext");

		// ========== TYPE FILTER ==========
		$s .= '<select id="filter_type_membersext" name="filter_type" class="flat">';
		$sql = "SELECT rowid, libelle as label, statut";
		$sql .= " FROM ".MAIN_DB_PREFIX."adherent_type";
		$sql .= " WHERE entity IN (".getEntity('member_type').")";
		$sql .= " ORDER BY rowid";
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			$s .= '<option value="-1">'.$langs->trans("Type").'</option>';
			if (!$num) {
				$s .= '<option value="0" disabled="disabled">'.$langs->trans("NoCategoriesDefined").'</option>';
			}

			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				$s .= '<option value="'.$obj->rowid.'">'.dol_trunc($obj->label, 38, 'middle').'</option>';
				$i++;
			}
			$s .= ajax_combobox("filter_type_membersext");
		} else {
			dol_print_error($this->db);
		}
		$s .= '</select>';

		$s .= ' ';

		// ========== CATEGORY FILTER ==========
		$s .= '<select id="filter_category_membersext" name="filter_category" class="flat">';
		$sql = "SELECT rowid, label, type, visible";
		$sql .= " FROM ".MAIN_DB_PREFIX."categorie";
		$sql .= " WHERE type = 3"; // Only member categories
		$sql .= " AND entity = ".$conf->entity;
		$sql .= " ORDER BY label";

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			$s .= '<option value="-1">'.$langs->trans("Category").'</option>';
			if (!$num) {
				$s .= '<option value="0" disabled>'.$langs->trans("NoCategoriesDefined").'</option>';
			}

			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				$s .= '<option value="'.$obj->rowid.'">'.dol_trunc($obj->label, 38, 'middle').'</option>';
				$i++;
			}
			$s .= ajax_combobox("filter_category_membersext");
		} else {
			dol_print_error($this->db);
		}
		$s .= '</select>';

		$s .= ' ';

		// ========== EXTRAFIELD: EMAIL-GROUP FILTER ==========
		$s .= '<select id="filter_emailgroup_membersext" name="filter_emailgroup" class="flat">';
		$s .= '<option value="">'.$langs->trans("EmailGroup").'</option>';

		// Load extrafield definition to get select options
		$extrafields = new ExtraFields($this->db);
		$extrafields->fetch_name_optionals_label('adherent');
		
		// Get emailgroup options from extrafield definition
		$emailgroup_options = array();
		if (!empty($extrafields->attributes['adherent']['param']['emailgroup']['options'])) {
			$emailgroup_options = $extrafields->attributes['adherent']['param']['emailgroup']['options'];
		}

		// Get count for each option (by key)
		$emailgroup_counts = array();
		$sql = "SELECT ae.emailgroup, COUNT(DISTINCT a.email) as nb";
		$sql .= " FROM ".MAIN_DB_PREFIX."adherent_extrafields ae";
		$sql .= " INNER JOIN ".MAIN_DB_PREFIX."adherent a ON a.rowid = ae.fk_object";
		$sql .= " WHERE ae.emailgroup IS NOT NULL AND ae.emailgroup != ''";
		$sql .= " AND a.email IS NOT NULL AND a.email != ''";
		$sql .= " AND a.entity IN (".getEntity('member').")";
		$sql .= " GROUP BY ae.emailgroup";

		$resql = $this->db->query($sql);
		if ($resql) {
			while ($obj = $this->db->fetch_object($resql)) {
				$emailgroup_counts[$obj->emailgroup] = $obj->nb;
			}
		}

		// Build dropdown with labels from extrafield options
		if (!empty($emailgroup_options)) {
			foreach ($emailgroup_options as $key => $label) {
				$count = isset($emailgroup_counts[$key]) ? $emailgroup_counts[$key] : 0;
				if ($count > 0) {
					$s .= '<option value="'.dol_escape_htmltag($key).'">';
					$s .= dol_escape_htmltag($label).' ('.$count.')';
					$s .= '</option>';
				}
			}
		}

		if (empty($emailgroup_options) || empty($emailgroup_counts)) {
			$s .= '<option value="" disabled>'.$langs->trans("None").'</option>';
		}

		$s .= '</select>';
		$s .= ajax_combobox("filter_emailgroup_membersext");

		// ========== DATE FILTERS ==========
		$s .= '<br><span class="opacitymedium">';
		$s .= $langs->trans("DateEndSubscription").': &nbsp;';
		$s .= $langs->trans("After").' > </span>'.$form->selectDate(-1, 'subscriptionafter', 0, 0, 1, 'membersext', 1, 0, 0);
		$s .= ' &nbsp; ';
		$s .= '<span class="opacitymedium">'.$langs->trans("Before").' < </span>'.$form->selectDate(-1, 'subscriptionbefore', 0, 0, 1, 'membersext', 1, 0, 0);

		return $s;
	}


	/**
	 *  Provide the URL to the member card
	 *
	 *  @param	int		$id		Member ID
	 *  @return string      	URL link
	 */
	public function url($id)
	{
		return '<a href="'.DOL_URL_ROOT.'/adherents/card.php?rowid='.$id.'">'.img_object('', "user").'</a>';
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Add recipients into target table
	 *
	 *  @param    int        $mailing_id        Id of emailing
	 *  @return int                       Return integer < 0 if error, nb added if ok
	 */
	public function add_to_target($mailing_id)
	{
		// phpcs:enable
		global $conf, $langs;

		$langs->loadLangs(array("members", "companies"));

		$cibles = array();
		$now = dol_now();

		// Load extrafield options for label mapping
		$extrafields = new ExtraFields($this->db);
		$extrafields->fetch_name_optionals_label('adherent');
		$emailgroup_options = array();
		if (!empty($extrafields->attributes['adherent']['param']['emailgroup']['options'])) {
			$emailgroup_options = $extrafields->attributes['adherent']['param']['emailgroup']['options'];
		}

		// Date filters
		$dateendsubscriptionafter = dol_mktime(GETPOSTINT('subscriptionafterhour'), GETPOSTINT('subscriptionaftermin'), GETPOSTINT('subscriptionaftersec'), GETPOSTINT('subscriptionaftermonth'), GETPOSTINT('subscriptionafterday'), GETPOSTINT('subscriptionafteryear'));
		$dateendsubscriptionbefore = dol_mktime(GETPOSTINT('subscriptionbeforehour'), GETPOSTINT('subscriptionbeforemin'), GETPOSTINT('subscriptionbeforesec'), GETPOSTINT('subscriptionbeforemonth'), GETPOSTINT('subscriptionbeforeday'), GETPOSTINT('subscriptionbeforeyear'));

		// ========== SQL QUERY WITH EXTRAFIELD JOIN ==========
		$sql = "SELECT a.rowid as id, a.email as email, null as fk_contact,";
		$sql .= " a.lastname, a.firstname,";
		$sql .= " a.datefin, a.civility as civility_id, a.login, a.societe,";
		$sql .= " ae.emailgroup"; // Extrafield
		$sql .= " FROM ".MAIN_DB_PREFIX."adherent as a";
		
		// LEFT JOIN for extrafields (may not exist for all members)
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."adherent_extrafields ae ON ae.fk_object = a.rowid";
		
		// Category join (if category filter is set)
		if (GETPOSTINT('filter_category') > 0) {
			$sql .= " INNER JOIN ".MAIN_DB_PREFIX."categorie_member as cm ON cm.fk_member = a.rowid";
			$sql .= " INNER JOIN ".MAIN_DB_PREFIX."categorie as c ON c.rowid = cm.fk_categorie AND c.rowid = ".(GETPOSTINT('filter_category'));
		}
		
		$sql .= " , ".MAIN_DB_PREFIX."adherent_type as ta";
		$sql .= " WHERE a.entity IN (".getEntity('member').") AND a.email <> ''";
		$sql .= " AND a.email NOT IN (SELECT email FROM ".MAIN_DB_PREFIX."mailing_cibles WHERE fk_mailing=".((int) $mailing_id).")";
		
		// ========== STATUS FILTER ==========
		if (GETPOST("filter", 'aZ09') == 'draft') {
			$sql .= " AND a.statut = -1";
		} elseif (GETPOST("filter", 'aZ09') == '1a') {
			$sql .= " AND a.statut=1 AND (a.datefin >= '".$this->db->idate($now)."' OR ta.subscription = 0)";
		} elseif (GETPOST("filter", 'aZ09') == '1b') {
			$sql .= " AND a.statut=1 AND ((a.datefin IS NULL or a.datefin < '".$this->db->idate($now)."') AND ta.subscription = 1)";
		} elseif (GETPOST("filter", 'aZ09') === '0') {
			$sql .= " AND a.statut=0";
		}
		
		// ========== DATE FILTER ==========
		if ($dateendsubscriptionafter > 0) {
			$sql .= " AND datefin > '".$this->db->idate($dateendsubscriptionafter)."'";
		}
		if ($dateendsubscriptionbefore > 0) {
			$sql .= " AND datefin < '".$this->db->idate($dateendsubscriptionbefore)."'";
		}
		
		// ========== TYPE FILTER ==========
		$sql .= " AND a.fk_adherent_type = ta.rowid";
		if (GETPOSTINT('filter_type') > 0) {
			$sql .= " AND ta.rowid = ".(GETPOSTINT('filter_type'));
		}
		
		// ========== EXTRAFIELD: EMAIL-GROUP FILTER ==========
		$filter_emailgroup = GETPOST('filter_emailgroup', 'alpha');
		if (!empty($filter_emailgroup)) {
			$sql .= " AND ae.emailgroup = '".$this->db->escape($filter_emailgroup)."'";
		}
		
		// ========== UNSUBSCRIBE CHECK ==========
		if (empty($this->evenunsubscribe)) {
			$sql .= " AND NOT EXISTS (SELECT rowid FROM ".MAIN_DB_PREFIX."mailing_unsubscribe as mu WHERE mu.email = a.email and mu.entity = ".((int) $conf->entity).")";
		}
		
		$sql .= " ORDER BY a.email";

		// ========== EXECUTE QUERY AND BUILD TARGETS ==========
		dol_syslog(get_class($this)."::add_to_target", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			$num = $this->db->num_rows($result);
			$i = 0;
			$j = 0;

			dol_syslog(get_class($this)."::add_to_target mailing ".$num." targets found");

			$old = '';
			while ($i < $num) {
				$obj = $this->db->fetch_object($result);
				if ($old != $obj->email) {
					// Map emailgroup key to label
					$emailgroup_label = '-';
					if (!empty($obj->emailgroup) && isset($emailgroup_options[$obj->emailgroup])) {
						$emailgroup_label = $emailgroup_options[$obj->emailgroup];
					} elseif (!empty($obj->emailgroup)) {
						$emailgroup_label = $obj->emailgroup; // Fallback to key if label not found
					}

					$cibles[$j] = array(
						'email' => $obj->email,
						'fk_contact' => (int) $obj->fk_contact,
						'lastname' => $obj->lastname,
						'firstname' => $obj->firstname,
						'other' =>
							($langs->transnoentities("Login").'='.$obj->login).';'.
							($langs->transnoentities("UserTitle").'='.($obj->civility_id ? $langs->transnoentities("Civility".$obj->civility_id) : '')).';'.
							($langs->transnoentities("DateEnd").'='.dol_print_date($this->db->jdate($obj->datefin), 'day')).';'.
							($langs->transnoentities("Company").'='.$obj->societe).';'.
							($langs->trans("EmailGroup").'='.$emailgroup_label),
						'source_url' => $this->url($obj->id),
						'source_id' => (int) $obj->id,
						'source_type' => 'member'
					);
					$old = $obj->email;
					$j++;
				}

				$i++;
			}
		} else {
			dol_syslog($this->db->error());
			$this->error = $this->db->error();
			return -1;
		}

		return parent::addTargetsToDatabase($mailing_id, $cibles);
	}
}
