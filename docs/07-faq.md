# Extension FAQ

## I've installed and enabled the extension, but it doesn't appear (correctly) in my front-end store.

After installing the Search Autocomplete extension, first make sure to flush the autocomplete cache by navigating to **System > Cache Management** in the top Admin Panel menu. Click the **Flush Autocomplete Cache** button at the bottom of the page to rebuild the search suggestion cache, which is necessary to show the extension in your front-end store.

Please also remember to flush the autocomplete cache after updating the Search Autocomplete settings or the product attributes used in the search results in order to show the changes on the front-end.

## I get an Internal Server Error when trying the flush the autocomplete cache. What should I do?

If it takes a long time to flush the autocomplete cache, which can occur in stores with a large number of products, you might run into an Internal Server Error.

This can usually be fixed by increasing the following values in the .htaccess or php.ini settings, which can be found in your Magento root directory:

- Increase php memory limit to at least 500mb or 1gb
- Increase php max execution time to at least 1800

If you still encounter the same issue after increasing these amounts and flushing the Autocomplete cache, please [open an issue on GitHub](https://github.com/PluginCompany/magento1-search-autocomplete/issues) for assistance.

## Will Search Autocomplete work with my custom Magento theme?

The Search Autocomplete extension works out of the box with most Magento themes, although some custom themes require a small CSS modification in order to show the plugin correctly on the front-end. Please don't hesitate to [open an issue on GitHub](https://github.com/PluginCompany/magento1-search-autocomplete/issues) if you need help with customizing the CSS!

## Is it possible to translate certain words / sentences of the Search Autocomplete extension?

To translate the plugin, you can use the Plugincompany_Autocomplete.csv file located in the **app > locale** folder of your Magento root directory.

## The images in the search results are not displayed properly

In most cases rebuilding the Search Autocomplete cache in "System > Cache Management" will solve this issue. You can rebuild the cache by clicking the "Flush Autocomplete Cache" button.

If the above doesn't work, then it's recommended to disable the "Pre-render Images" setting in the extension configuration screen. Disabling this will make the extension fetch images on the fly when search results are being displayed. After disabling this option, please rebuild the Search Autocomplete cache for changes to take effect.

---

[Documentation index](./README.md) · [README](../README.md) · [Online version](https://plugin.company/knowledge-base/docs/magento-search-autocomplete/faq/)
