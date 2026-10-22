# Changelog: Search Autocomplete for Magento 1 / OpenMage

All releases of this extension, newest first. This repository contains the latest release, tagged [`1.14.4`](https://github.com/PluginCompany/magento1-search-autocomplete/releases/tag/1.14.4); earlier versions are listed for reference and have no tag.

## 1.14.4 — 2020-09-09

- Fixed issue causing search result category URLs to be incorrect in Magento EE
- Fixed issue causing search result product URLs to be incorrect when product url_path and url_key are different in Magento EE
- Improved description of some fields in admin configuration

## 1.14.2 — 2020-06-21

- Fixed issue which caused the search field selector to default to 'searchId' instead of '#search'

## 1.14.1 — 2020-06-15

- Minor CSS tweak which solves search result item width issue in some rare cases
- Added workaround for jQuery(document).ready() not being fired sometimes

## 1.14.0 — 2019-11-29

- Added support for custom search input field ID (custom theme support)
- Added support for different search input field ID for mobile view

## 1.13.2 — 2019-11-03

- Fixed issue causing search results to show the same image for all products when pre-prender images is enabled

## 1.13.1 — 2019-10-22

- Fixed headers already sent error when pre-render images setting is disabled
- Disabled native Magento autocomplete functionality for better support with custom themes

## 1.13.0 — 2019-08-23

- Performance optimization for cache rebuilding process
- Improved image loading time when images aren't pre-rendered
- Configurable products with all child products out of stock are now excluded from search when 'Show out of stock products' is set to 'No'
- Added workaround for Magento 1.8.x and lower core code bug related to image processing memory limit
- Added some additional error logging statements

## 1.12.0 — 2019-08-13

- Added advanced query and keyword tokenizer. When enabled the search query and keywords will be tokenized using ngram and other methods. This will improve results when partial or misspelled words are typed.

## 1.11.5 — 2019-08-01

- Updated included jQuery library
- Improved Twitter Typeahead library locating method, which is used to find the correct jQuery object containing the library in case jQuery is loaded multiple times on a single page

## 1.11.4 — 2019-07-27

- Minor advanced product name & SKU parsing improvement for improved search result relevance

## 1.11.3 — 2019-07-09

- Some code format changes for Magento Marketplace submission

## 1.11.2 — 2019-04-11

- Price in search results now uses the price format of the store view locale / language
- Fixed issue where search string highlighting wasn't working correctly on mobile in some custom themes
- Excluded CMS pages and Categories from cache rebuilding process when front-end display is disabled for these items

## 1.11.1 — 2019-02-13

- PHP-FPM cache rebuilding bug workaround
- Fixed extension conflict which was causing invalid search suggestions JSON response
- Improved search results keyword parsing method

## 1.11.0 — 2018-11-28

- Added option to select price tax display type for search results. Including / excluding VAT.

## 1.10.2 — 2018-10-31

- Added error message in admin panel when cache rebuilding fails
- Cache rebuilding script now uses PHP_BINARY executable if available

## 1.10.1 — 2018-10-04

- Fixed headers already sent error when rebuilding cache with exec and shell_exec disabled

## 1.10.0 — 2018-09-27

- Added option to either pre-render images while rebuilding the cache, or render them on the fly when a search is executed. The first option (which is the default) requires more server resources during the cache rebuilding process, but results in better performance on the client side.
- Some CSS tweaks for better compatibility with custom themes

## 1.9.7 — 2018-09-20

- Locale CSV fix for issue which happened when both Contact Forms and Search Autocomplete were installed

## 1.9.6 — 2018-09-18

- Additional HTML5 placeholder shim compatibility fix

## 1.9.5 — 2018-09-18

- Made grid view use 2 columns on mobile when 3 column layout is selected
- Removed description line when viewed on mobile
- CSS tweaks for some screen sizes

## 1.9.4 — 2018-09-17

- Compatibility fix for some themes
- Compatibility fix for old themes with HTML5 placeholder shim

## 1.9.3 — 2018-07-23

- Fixed cache rebuilding ACL permissions issue
- Removed AMD from Typeahead plugin for better compatibility
- Added more error reporting to cache rebuilding logs
- Improved system.xml descriptions

## 1.9.2 — 2018-04-13

- Added parsing support for comma separated list keyword attributes

## 1.9.1 — 2018-03-27

- Added last modified header to autocomplete JSON cache files

## 1.9.0 — 2018-02-27

- Improved search results relevance
- Added option to specify weight per attribute for fine tuning search results
- The relative frequency of keyword occurence is now taken into account
- Added option to sort search results based on price

## 1.8.3 — 2018-01-20

- Fixed issue regarding image urls in some set-ups

## 1.8.2 — 2017-12-29

- Fix for filemtime warning in system.log

## 1.8.1 — 2017-12-14

- Added improved logging functionality
- Removed AJAX loader from cache management screen
- Removed memory limit for cache rebuilding shell script
- Fixed issue arising from using exec / shell_exec in conjunction with PHP FPM / CGI

## 1.8.0 — 2017-12-11

- Improved cache rebuilding process
- Added option to set up periodic cron job from system configuration section

## 1.7.3 — 2017-12-11

- Fix for issue introduced in v1.7.2

## 1.7.2 — 2017-12-07

- Added possibility to enable search result sorting starting from a certain query length
- Fixed issue regarding retrieving correct php executable path during cache rebuilding
- Added some extra logging calls

## 1.7.1 — 2017-11-21

- Added some extra special characters to search query normalization

## 1.7.0 — 2017-11-15

- Added suggestion cache rebuilding shell script
- Rebuilding cache from Magento admin area now also tries to run shell script as background process, which should solve any webserver timeout issues
- Added option to normalize accented characters in search results

## 1.6.7 — 2017-09-12

- Bundled products price display fix

## 1.6.6 — 2017-08-31

- Fixed flat catalog + EAV attributes issue

## 1.6.5 — 2017-07-20

- made search button display fix optional through system config

## 1.6.4 — 2017-06-01

- Added translate CSV

## 1.6.3 — 2017-05-29

- Added workaround for Fooman Speedster bug

## 1.6.2 — 2017-04-10

- Fixed widget code processing issue in search result descriptions

## 1.6.1 — 2017-04-10

- Fixed cache rebuild bug introduced in v1.6.0

## 1.6.0 — 2017-04-08

- Added possibility to exclude certain CMS pages from search results
- Disabled categories are now excluded from search results

## 1.5.2 — 2017-03-08

- Fixed compatibility with automatic currency conversion

## 1.5.1 — 2017-03-07

- fixed issue regarding product details when using flat catalog setting
- CSS improvements
- Improved config page descriptions

## 1.5.0 — 2016-12-21

- Added option to exclude out of stock products from search results
- Added advanced product name parsing option for better product name search result relevance
- Fixed issue with grid layout in Safari browser
- Fixed gzip / zlib conflict

## 1.4.4 — 2016-11-29

- Fixed issue regarding partially SSL secured webshop sections

## 1.4.3 — 2016-11-02

- Fixed a minor bug regarding warning level errors

## 1.4.2 — 2016-10-18

- Fixed jQuery detection logic
- Fixed ACL issue

## 1.4.1 — 2016-08-11

- Fixed bug regarding single store shops search indexing

## 1.4.0 — 2016-07-19

- Added search result grid layouts
- Added search query highlighting option
- Added adjustable search results dropdown width option
- Removed search field width expansion option
- Improved search autocomplete performance
- Fixed several bugs
- Fixed multiple jQuery versions issue

## 1.3.1 — 2016-05-22

- Fixed issue regarding undefined product url_path (related to Magmi import)
- Fixed issue when multiple jQuery versions are loaded
- Fixed issue regarding custom keywords when only one store view is active

## 1.3.0 — 2016-04-22

- Added minimum query length for search results sorting
- Added template variable processing option for search result descriptions

## 1.2.3 — 2016-02-26

- Fixed compatibility with third party jQuery
- Added z-index to Search Autcomplete CSS

## 1.2.2 — 2016-02-24

- Fixed search result issue regarding product prices including VAT

## 1.2.1 — 2016-02-21

- Fixed PHP 5.4 compatibility issue
- Filtered out disabled CMS pages from search results

## 1.2.0 — 2016-02-16

- Added option to display product prices in search results
- Added option to show categories and CMS pages in search results
- Added option to configure the maximum amount of displayed product, category and CMS page suggestions
- Added options to select the attributes used for product suggestion titles, descriptions and keywords
- Added option to set a character limit for search result descriptions
- Added option to enable advanced SKU parsing for search suggestions
- Added configurable search suggestion sort order based on attribute priority
- Added option to select the allowed product visibility values
- Made compatible with latest security patch

## 1.1.0 — 2015-10-26

- Fixed full store search issue for custom themes
- Fixed image loading issue
- Added new option to choose a preferred image type for search results

## 1.0.4 — 2015-09-15

- Fixed special characters in result description bug
- Fixed resize search result bug when search field is initially hidden

## 1.0.3 — 2015-08-10

- Fixed product search result bug

## 1.0.2 — 2015-07-25

- Minor bug fix

## 1.0.1 — 2015-05-11

- Fixed multi-store compatibility
- Local storage now refreshes after cache rebuild
- Minor bugfix

## 1.0.0 — 2015-03-28

- First release of Magento Search Autocomplete extension by Plugin Company.
