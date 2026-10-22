# Changelog: Fraud Prevention for Magento 2

All releases of this extension, newest first. The released versions are commits on `main`, each tagged with its version number (e.g. [`100.4.6`](https://github.com/PluginCompany/magento2-fraud-prevention/releases/tag/100.4.6)); Composer installs these tags.

## 100.4.6 — 2023-03-29

- Added form validation to blacklisting rule strikes field, which now requires a number greater than zero as value.
- If strikes value is set to 0, it's now treated as 1. This fixes an issue which caused blacklisting rules to be triggered if strikes is set to 0, even though no matching fraud records were present.

## 100.4.5 — 2023-02-24

- Fixed issue which caused disabled blacklisting rules to still be applied during checkout

## 100.4.4 — 2022-07-02

- Fix for updating or installing using composer: Changed license manager dependency version constraint to latest version. (Latest version supports PHP 8.x.)

## 100.4.3 — 2022-07-01

- Validated Magento 2.4.4 and PHP 8.1 compatibility. Updated composer.json file accordingly.

## 100.4.2 — 2021-08-13

- Fixed issue which caused customer grid CSV export to break

## 100.4.1 — 2021-05-24

- Fixed admin area ACL rules issue: Added support for fine grained access control.

## 100.4.0 — 2021-04-11

- Added feature which allows disabling specific payment methods when a (suspected) fraudulent address is used during checkout

## 100.3.4 — 2020-10-29

- Blacklisting rules now only use Region field as opposed to both Region and Region ID.
- Fraud Suspicion Records now only use one Region field and value of Region ID field is automatically copied to this field.
- Fixed problem which in some cases caused false positives when manually created Fraud Suspicion Records have only 1 or 2 fields filled in.
- Fixed issue causing billing address Fraud Suspicion weight to be 0 (instant block) when created based on quote address. Issue occurred during checkout and payment transaction failed event.
- Other minor bugfixes / tweaks.

## 100.3.3 — 2020-08-27

- Fixed issue causing customer account screen in admin area to hang for couple of seconds while loading. (Related to Magento core attribute sort order bug)
- Validated compatibility with Magento 2.4 + PHP 7.4

## 100.3.2 — 2020-07-24

- Added e-mail address validation to admin area fraud record management
- Fixed issue where invalid e-mail address format caused all orders to be blacklisted when using e-mail domain rule
- Fixed font CSS conflict with Magento EE / ECE PageBuilder module

## 100.3.1 — 2020-04-06

- Fixed "InvalidArgumentException" on Blacklisting Rules and Fraud Record pages in the admin area. Error occurred on Magento 2.3.4 and when Temando_Shipping module is disabled.

## 100.3.0 — 2019-11-27

- Added new feature which automatically adds fraud suspicion records based on order status. This can for example be used to add fraud strikes based on new orders with custom 'payment failed' statuses.

## 100.2.4 — 2019-06-18

- Fixed Magento 2.3.x admin order grid filtering bug

## 100.2.3 — 2019-04-18

- Fixed postcode normalized field error during checkout
- Added better logging functionality and error catching to prevent checkout from breaking

## 100.2.2 — 2019-04-15

- Added missing license configuration to extension

## 100.2.1 — 2019-04-15

- Updated license manager dependency version in composer.json

## 100.2.0 — 2019-04-08

- New field normalization feature which allows advanced parsing and comparing of address fields resulting in a better chance to detect fraudulent orders
- Field normalization removes unnecessary characters from each field and converts special characters which prevents fraudsters from adding new orders with a slightly different address, for example with a space or comma added to a street line
- Added feature to only compare last xx digits of phone and fax, which helps matching fraudulent phone numbers with and without country / region prefix
- Added excluded e-mail domains field in system configuration, which allows the admin to exclude specific e-mail domains from being used by blacklisting rules. This prevents generic e-mail domains like for example gmail.com to be blacklisted accidentally
- Fixed compatibility with some infinite scrolling and other AJAX content extensions.
- Customer / Guest logout functionality AJAX content fix
- Fixed issue where whitelisting order didn't revert to previous order status correctly
- Other minor bug fixes and code improvements

## 100.0.6 — 2018-05-03

- Removed duplicate dependency injection for Magento 2.1.x

## 100.0.5 — 2018-03-07

- Fixed customer IP rule issue
- Removed some legacy code

## 100.0.4 — 2018-02-23

- Fixed customer grid reindexing issue due to core bug in Magento 2.2.x

## 100.0.3 — 2018-02-19

- Fixed issue which broke the orders grid in admin area customer account

## 100.0.2 — 2018-01-29

- Fixed issue related to order grid column conflict with other extensions
- Updated system.xml

## 100.0.1 — 2018-01-24

- Updated ACL xml file
- Added filter for attributes and customer group columns in rule listing

## 100.0.0 — 2018-01-19

- First release of Magento 2 Fraud Prevention extension by Plugin Company.
