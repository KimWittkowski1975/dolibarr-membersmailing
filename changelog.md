# Changelog - Members Mailing Extended

All notable changes to this module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [1.0.0] - 2026-06-03

### ✨ Added
- Initial release of Members Mailing Extended module
- Extended mailing target selector for foundation members
- Email-group extrafield filter with dropdown + counts
- All standard filters preserved (Status, Type, Category, Date)
- Email-group displayed in recipient details ("Other" field)
- Admin setup page (basic structure)
- English translations (en_US)
- Module Descriptor (numero 550070)
- Git repository initialization
- Documentation (readme.md, changelog.md)

### 🔧 Technical
- Class: `mailing_membersextended` extends `MailingTargets`
- Dependencies: modAdherent, modMailing
- LEFT JOIN pattern for `llx_adherent_extrafields`
- SQL injection protection via `$db->escape()`
- Input sanitization via `GETPOST()`

### 📋 Requirements
- Dolibarr V21
- Members Module (modAdherent)
- Mailing Module (modMailing)
- Extrafield: `emailgroup` (varchar 255, type: select)

### 🎯 Target Use Case
- User request: "im dolibarr core module für Members habe ich ein Extrafeld angelegt dieses Möchte ich als filter in der Seite der Massen E-Mails verwenden"
- Solution: Custom module with extended selector (NO core modifications)
- Workflow: UNCHANGED (same page, just additional selector option)

---

## [Unreleased]

### Future Enhancements (planned)
- [ ] Add more extrafield filters (configurable)
- [ ] German translations (de_DE)
- [ ] Statistics dashboard integration
- [ ] Bulk actions for email-groups

---

**Version Numbering:**
- Major.Minor.Patch (e.g., 1.0.0)
- Increment Patch for bug fixes (1.0.1)
- Increment Minor for new features (1.1.0)
- Increment Major for breaking changes (2.0.0)
