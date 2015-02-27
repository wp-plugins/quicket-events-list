# Quicket API Plugin #
Contributors: quijames
Tags: quicket, api, events
Requires at least: 4.1.1
Tested up to: 4.1.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Connect to the Quicket API to display a public feed of events on your WordPress site.

## Description ##

This plugin allows you to embed a widget in your WordPress site to display a feed of events pulled from the Quicket website (www.quicket.co.za).

The plugin allows two modes of operation, either:

1. *Public events*. In this mode, a list of publicly listed Quicket events are pulled to your feed.
2. *Saved events*. In this mode, a list of events that you have saved on the Quicket website will be pulled to your feed.

The plugin is added to your WordPress site as a widget that can be put in your theme, or embedded in posts using shortcodes. Every time someone views the feed it will be refreshed with the latest events.

## Installation ##

To install the plugin:

1. Install and activate the plugin from the Plugins menu in WordPress
2. Navigate to the Widgets menu option in WordPress
3. Drag the Quicket widget into an active widget area (e.g. Main Widget Area)

To configure the widget:

1. Choose whether to show public or saved events
2. In order to use the plugin, you will need an *API key*. To obtain an API key, you need to register at http://developer.quicket.co.za .
3. To use the *saved events* mode, you will also need a Quicket user token. This can be found in your Quicket account. It is used to identify and authenticate you so that the plugin can retrieve events you have saved on Quicket.
4. You can enter the number of events to display at any given time. The higher the number, the longer the feed will take to show, so 5 - 10 would be a good starting point.
5. To limit your feed to events in a certain category, enter a comma separated list of category ID's found on Quicket, e.g. 13,40,2
6. If you are a Quicket affiliate, you can enter your affiliate code in the widget setup. That will automatically add your affiliate code onto all the events in the feed.
7. If you'd only like to pull recent events in your feed, you can specify a last modified date. If specified, only events that were modified on Quicket after that date will be shown on your feed.
8. If you'd like a paged list of events, check the "Show Pagination" check box. This will add a paging control to the bottom of the feed. The number of events you specified previously will then be the number of events shown per page.

## Frequently Asked Questions ##

### How do I get an API key? ###

Please register on https://developer.quicket.co.za to get your key.

### How do I save an event on Quicket so that it shows up on my WordPress site? ###

To save an event on Quicket, simply go the event page and click the "Save this event" button. If you are logged in, it will be saved to your Quicket account. If you have configured the Quicket widget for displaying your saved events, it will instantly show up in your feed.

### Where can I find a category's ID? ###

Currently the only way to find a category's ID is to go to http://www.quicket.co.za/events/ and click on the category in the left menu. On the resulting page, look at the URL in the browser, and it will show something like http://www.quicket.co.za/events/?categories#28 . In this case, 28 is the category ID for the *Alternative* category.

### How can I become a Quicket affiliate ###

Earn money by promoting Quicket events or getting new events listed on Quicket. To find out more about the Quicket affiliate program, please email support@quicket.co.za . 

## Screenshots ##

1. Configuring the widget
2. An example of a feed showing events pulled from Quicket

## Changelog ##

### 1.0.0 ###
* Initial version