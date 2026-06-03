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
 * \file       membersmailing/admin/setup.php
 * \ingroup    membersmailing
 * \brief      MembersMailing module setup page
 */

// Load Dolibarr environment
$res = 0;
if (!$res && file_exists("../../main.inc.php")) {
	$res = include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = include "../../../main.inc.php";
}
if (!$res) {
	die("Main include failed");
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once '../core/modules/modMembersMailing.class.php';

// Load translation files
$langs->loadLangs(array("admin", "membersmailing@membersmailing"));

// Security check
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');

/*
 * Actions
 */

// Future: Add configuration actions here

/*
 * View
 */

$page_name = "MembersMailingSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration des modules (button)
$head = array();
$h = 0;

print dol_get_fiche_head($head, 'settings', $langs->trans($page_name), -1, '');

print '<div class="info">';
print $langs->trans("MembersMailingSetupInfo");
print '</div>';

print '<br>';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("MembersMailingModule").'</td>';
print '<td>'.$langs->trans("Enabled").'</td>';
print '</tr>';

print '</table>';

print '<br>';

print '<div class="tabsAction">';
print '</div>';

print dol_get_fiche_end();

// Footer
llxFooter();
$db->close();
