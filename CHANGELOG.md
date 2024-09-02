## 1.0.0 (September 02, 2024)


## 0.1.0 (September 02, 2024)
  - Merge pull request #3 from Bolius/v11
  - Change static domain value to env variable
  - Improved script readability.
  - Fixed some problems.
  - Refactored code and migrated to typo3 v11.5.
  - Added composer.json, added rector changes to ext_* files. Removed old tests.
  - Adjust regexp to allow for colon in attribute names - for vue.js
  - Add crossorigin if url is changed - not if host is different from requested host
  - Expand typo3 ddep range
  - Create README.md
  - Add posibility to disable on host names
  - Add crossorigin to tags
  - Add possibility to disable on backend login
  - Add ext_conf_template
  - Add possibility to add crossorigin to script tag
  - Only return domain name if it exists
  - Add multi-domain possibility
  - Remove unused file and remove ll
  - Add querybuilder for v9 compat.
  - Add source tag src attr
  - Deactivate static domains when BE login is present
  - Only rewrite domains if scheme is http(s), explicitly or implicitly
  - Never replace host
  - Revert "Add appendStaticUrl to ResourceStorage::SIGNAL_PreGeneratePublicUrl" Didn't work - will try again some other time
  - Add appendStaticUrl to ResourceStorage::SIGNAL_PreGeneratePublicUrl
  - Add rewriting of references in header- and footerData
  - Disable replace domain
  - Add handling of no domain found, and reformat
  - Adjust logic a bit
  - Remove hardcoded domain
  - Restructure, cleaning
  - Merge the two testpages to one
  - Add two test pages
  - Initial - functional, but needs cleaning, documentation and testing

