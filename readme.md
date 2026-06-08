# Members Mailing Extended - Module for Dolibarr

![Dolibarr](https://img.shields.io/badge/Dolibarr-v21-blue)
![Version](https://img.shields.io/badge/Version-1.0.0-orange)
![License](https://img.shields.io/badge/License-GPL--3.0-green)
![Status](https://img.shields.io/badge/Status-Production-success)

**Version:** 1.0.0  
**Dolibarr Version:** V21  
**Modul-Numero:** 550070  
**Author:** Kim Wittkowski <kim@wittkowski-it.de>  
**Copyright:** (C) 2026 Kim Wittkowski  
**License:** GPL-3.0-or-later  

---

## 📋 Description

**Members Mailing Extended** provides an enhanced mailing target selector for Dolibarr foundation members with additional filters for extrafields.

This module extends the standard "Foundation Members" mailing selector with the ability to filter members by custom extrafields, particularly the **email-group** field.

---

## ✨ Features

- ✅ Extended mailing target selector for members
- ✅ Filter members by **email-group** extrafield (dropdown with counts)
- ✅ All standard filters preserved (Status, Type, Category, Date)
- ✅ Email-group displayed in recipient details
- ✅ Compatible with Dolibarr V21
- ✅ Modulebuilder compatible
- ✅ No core modifications required

---

## 📦 Installation

### 1. Install Module

Copy the `membersmailing` folder to:
```
/custom/membersmailing/
```

### 2. Activate Module

1. Go to **Home → Setup → Modules/Applications**
2. Search for **"Members Mailing Extended"**
3. Click **Activate**

### 3. Dependencies

This module requires:
- **Members Module** (modAdherent)
- **Mailing Module** (modMailing)

---

## 🚀 Usage

### Step 1: Add Recipients to Mass Mailing

1. Navigate to: **Tools → Mass Mailing → Emailing Campaigns**
2. Create or open an emailing campaign
3. Click **"Add recipients"**

### Step 2: Select "Foundation Members (Extended)"

You will now see **TWO** options for member targeting:
- **Foundation Members** (original Dolibarr selector)
- **Foundation Members (Extended)** ← Use this one!

### Step 3: Apply Filters

Available filters:
- **Status**: Draft, Active (Paid), Active (Late), Resigned
- **Type**: Member type
- **Category**: Member category
- **Email Group**: Select from dropdown (shows count per group) ⭐ NEW!
- **Date**: Subscription end date range

### Step 4: Add Targets

Click **"Add to targets"** - the email-group will be included in recipient details.

---

## 🔧 Configuration

### Setup Page

Access: **Home → Setup → Modules/Applications → Members Mailing Extended → Setup**

Currently no specific configuration required - module works out of the box!

---

## 📊 Extrafield: email-group

This module filters on the **emailgroup** extrafield:

- **Field Name:** `emailgroup`
- **Table:** `llx_adherent_extrafields`
- **Type:** `varchar(255)` (select)
- **Label:** "email-group"

### Add email-group Extrafield

If not already present:
1. **Home → Setup → Members → Attributes (extrafields)**
2. Add new field: `emailgroup` (Type: Select/List)
3. Add values (e.g., "Newsletter", "Events", "Partners")

---

## 🛡️ Security

- No Dolibarr core files modified (Sacred Rule #1)
- Uses `GETPOST()` for input sanitization
- SQL injection protection via `$db->escape()`
- Permission checks via module dependencies

---

## 🐛 Troubleshooting

### Selector not appearing?

1. Check module is activated
2. Check Members and Mailing modules are active
3. Clear browser cache (Ctrl+F5)
4. Check error logs: `/var/log/apache2/error.log`

### Email-group filter empty?

1. Verify extrafield `emailgroup` exists in `llx_adherent_extrafields`
2. Check members have email-group values assigned
3. Ensure members have valid email addresses

### Error Logs

Always check logs after module activation:
```bash
tail -30 /var/log/apache2/error.log | grep -i "member\|mailing\|Fatal"
```

---

## 📝 Changelog

See [changelog.md](changelog.md) for version history.

---

## 🤝 Support

For issues or questions:
- **Author:** Kim Wittkowski
- **Email:** kim@wittkowski-it.de
- **Website:** https://wittkowski-it.de

---

## 📄 License

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.
