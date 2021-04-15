# WC Pays
Contributors: [Artevio](https://artevio.com), [WPify](https://wpify.io) \
Plugin Name: WC Pays\
Plugin URI: https://github.com/artevio/wc-pays \
Tags: WooCommerce, Czech, WPify, Artevio\
Author: Artevio\
Author URI: https://artevio.com \
Requires at least: 5.3\
Tested up to: 5.7.1\
Requires PHP: 7.3\
Stable tag: 1.0.1\
Version: 1.0.1\
License: GPLv2 or later\
License URI: https://www.gnu.org/licenses/gpl-2.0.html

## Description ##

A free plugin that adds support for payment gateway Pays.cz for WooCommerce.

Based on payment gateway docs: https://www.pays.cz/docs/pays-implementacni-manual-platebni-brany.pdf

For using this plugin please register new Payment Gateway first here: https://www.pays.cz

## Installation ##

1. Upload `wc-pays` folder to the `/wp-content/plugins/` directory or install the plugin from the WordPress plugin repository.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go to the setting via  Plugins > WooCommerce Pays > Settings.
4. Enable and configure modules.

## Frequently Asked Questions ##

### How to view the payment confirmation webhook logs? ###

Go to Plugins > WooCommerce Pays.cz > Logs. Then from the in the dropdown select logs starting with 'woocommerce-pays-' and click View.

### Why did you create this plugin? ###

We needed better implementation for favourite payment gateway Pays.cz 

### Why do you offer this for free? ###

We believe it shouldn't be hard to receive payments on your WooCommerce store.

### Why do you support WordPress 5.x only? ###

We take advantage of the new WP features, and we strive to use modern development practices, which was not possible in the previous versions of WordPress.

### Why do you support PHP 7.3 and higher only? ###

We support only actively supported versions to be sure, that our code is secure from the bottom up. It's also essential to have the PHP version regularly updated, co you can be sure that your e-shop is safe and fast.

### I need feature XYZ, what should I do? ###

We are continually working on adding new modules - we will add some of them to the basic version, some will be available as paid addons. You can also [contact us](https://artevio.com) to write the module for you.

### I found a bug, what should I do? ###

Drop us a message in the support section, or feel free to submit a pull request in the [plugin repository](https://github.com/artevio/wc-pays).

### Who is behind the plugin? ###

This plugin is brought to you by the WordPress and WooCommerce experts at [artevio.com](https://artevio.com) and [wpify.io](https://wpify.io).

## Changelog ##
### 1.0.1 ###
* Small fixes
* Formatting standards
* Coding standards
* Gateway logo
### 1.0.0 ###
* Initial version
