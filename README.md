# OPML Blogroll
A WordPress plugin that exposes the internal Link Manager links as an OPML file at `/.well-known/recommendations.opml`, adds a link element with `rel="blogroll"` to the `<head>` section to aid discovery, and adds a `source:blogroll` to your RSS feed generated for you by WordPress as per the ['source' namespace](https://source.scripting.com/#1710035563000).

Updates to the OPML file are automatic and unlike other years-old abandoned solutions, the user does not need to interact with the plugin in any way to have it update the file based on changes to links in Link Manager.

Please note that the plugin requires some means of enabling the Link Manager as WordPress no longer enables it by default, despite the code for it still being there. I’m using Guido’s [VS Link Manager](https://en-gb.wordpress.org/plugins/very-simple-link-manager/) for this purpose. You also need to add at least one link to your `Links` menu in the WordPress admin sidebar.

# Example
- You can see OPML Blogroll working its magic here: https://hammyhavoc.com/.well-known/recommendations.opml, which exposes https://hammyhavoc.com/following/ as a `.opml` file.

---

Based on the work of [Josh Betz](https://josh.blog/2024/05/blogrolls).
