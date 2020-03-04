=== WP No External Links ===
Author: nicolly
Tags: seo, link, links, nofollow, external links
Requires at least: 2.6
Tested up to: 5.0
Stable tag: 1.0.2

Convert all external links into internal or nofollow links!

== Description ==

Convert all external links into internal or nofollow links!

This plugin is a fork of the hijacked and vulnarable "WP No External Links" plugin.

The following critical issues have been fixed:
- Removed Cross-Site Scripting vulnerability.
- Removed backdoor injected into original plugin.
- Fixed creation of the new `links_stats` table: added charset collation and table existence check.
- Fixed "Specified key was too long; max key length is 767 bytes" error on creating `mask_links` table.
- Added removing tables on uninstallation.

No External Links plugin is designed for specialists who care about the number of outgoing links that can be found by search engines. Now you can make all external links internal! Plugin does not change anything in website database - it just processes the output. Now you don't need to worry about your page rank dropping because of spam bots. You write any kind of http link and it becomes internal, hidden or nofollow! Of course, all the links will still be usable.

To make the long story short, your links like "http://gmail.com" will be masked into
"http://YourBlog.com/goto/http://gmail.com" - or
"http://YourBlog.com?goto=http://gmail.com"

= Features =

- Masking links in posts, pages, comments or on the whole website including widget, theme footer, etc.
- Outgoing clicks stats.
- Javascript redirect with custom text and timeout.
- Masking links with digital code and base64.
- Completely removing links from your posts.
- Custom redirects.
- Disabling masking for registered users.
- Disabling masking on per-post basis.
- Using nofollow and noindex instead of redirection.
- Excluding URLs from masking.
- Masking links in custom fields.
- Extending plugin with your own functions.

If you need any extra features, just create a ticket via support forum.

= Known Issues =

This plugin may conflict with your caching plugins, including Hyper Cache. Usually adding redirect page to caching plugin exclusions works fine.

If you disabled this plugin and still have links masked - check your chaching plugins.

== Installation ==

1. Upload the complete folder `no-external-links` to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
2. Configure plugin via No External Links link under the Settings.
4. Write posts with any kind of links, watch comments with links.

== Frequently Asked Questions ==

= Is it an evil hack and black SEO?! =

No. Please read [Google's topics on SEO](http://www.google.com/support/webmasters/bin/topic.py?topic=8522)

= How can I exclude my page with links from masking? =

Just put URLs you need to the exclusion list or disable masking for specific post.

= I've removed your plugin but links are still masked! =

* Deactivate other links related plugins.
* Clear cache via chache plugins you are using.
* Deactivate caching plugins.

= How can I mask links in a custom field? =

You will have to add just a line in a theme code where you output custom field data.

To add same preprocessing for data as for comment text, use
    `$metadata=apply\_filters('comment\_text',$metadata);`

For example, if you use some kind of metadata, it should look like this:
    `$metadata = get\_post\_meta($id, 'MetaTagName', true); // get data from wordpress database
    $metadata=apply\_filters('comment\_text', $metadata); // add this line of code for preprocessing field value
    echo $metadata; //output preprocessed field value`

Use "the\_content" instead of "comment\_text" if  you want to use the same masking policy as for post text.

== Changelog ==

= 1.0.2 =
* Add: WordPress 5.0 compatibility.

= 1.0.1 =
* Fix: base64 links encoding

= 1.0.0 =
* Add: Plugin released
