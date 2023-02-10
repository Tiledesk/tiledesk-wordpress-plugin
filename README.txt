=== Tiledesk — Live Chat and Chatbots Integration ===
Contributors: tiledesk
Tags: free live chat, chatbot, bot, ai, artificial, intelligence, ml, machine, learning, nlp, facebook, live chat, chat, livechat, tiledesk, widget, twitter, zendesk, mailchimp
Requires at least: 5.0
Tested up to: 6.1.1
Stable tag: trunk
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Free live chat for your website with integrated chat bots. It integrates into your site allowing you to build complex conversational bots in just a few clicks.

== Description ==

[Tiledesk](https://tiledesk.com/) is all-in-one customer engagement platform with intelligent chat bots ready to use in minutes. From lead generation to post-sales, from WhatsApp to your website. With omnichannel live chat and chatbots.

**KEY FEATURES**

* **Free** Built-In Chatbots: Design your first customized chatbot without writing a single line of code.
* **Conversational** Ticketing Platform: Provide a seamless experience to your customers.
* **Omnichannel** Messaging: Experience a unified inbox across all channels and for the entire team.

**Haven’t found the chatbot template you need for your business? Let’s build it together!**

Treat yourself to this game-changer feature: discover our [No-code Chatbot Design Studio](https://tiledesk.com/chatbot-design-studio/).

[youtube https://www.youtube.com/watch?v=USVKeiiFZ7o]

We created Tiledesk as an open-source stack to provide a conversational platform that prioritizes privacy and lets companies keep their own data.

This plugin allows you to connect your website to our hosted API platform. To learn more about the API and hosted services visit our [Developer Hub](https://developer.tiledesk.com/).

== Installation ==
1. Go to WordPress Control Panel
1. Click "Plugins", then "Add New"
1. Type in "Tiledesk Live Chat" and click "Search Plugins"
1. Download and install the Plugin
1. Click the "Activate Plugin" link
1. Move to the "Live Chat" section in the menu
1. Done! Now you can start talk with your users

== Frequently Asked Questions ==

= How much does Tiledesk cost? =

You can sign up for free and enjoy a 30-day free trial of Tiledesk Pro. You can decide to remain on the free plan after your trial ends. Learn more about Tiledesk plans on our [Pricing page](https://tiledesk.com/pricing-live-chat/).

= What does Omnichannel include? =

Our Omnichannel offer includes a Unified Messaging platform that allows you to build your bots targeted for Website, WhatsApp and other platforms use.

Learn more about Tiledesk integrations on our [Integrations page](https://tiledesk.com/integrations-live-chat/).

= Do I need a Tiledesk account to use this WordPress live chat plugin? =

Yes. Install the plugin, activate it, and create your free Tiledesk account from your WP admin panel or alternatively, you can sign up first, install the plugin, then link your Tiledesk account from your WP admin panel.

= Does Tiledesk offer a free trial? =

Yes. Sign up for free and enjoy a 30-day free trial of Tiledesk Pro. Learn more about Tiledesk plans on our [Pricing page](https://tiledesk.com/pricing-live-chat/).

= Can I use Tiledesk in languages other than English? =

Currently, Tiledesk for WordPress is available in English. Other languages are in development. However, Tiledesk Console and Widget are available in English, Italian, German, Spanish, Portuguese, French, and many others.

= Is it a cloud service or self-hosted? =

This plugin exclusively works with our cloud platform, however our components are [open sourced on Github](https://github.com/Tiledesk) so once your self-hosted instance is properly set up, you can apply the following filters targeting them to your publicly-accessible instance:

```php
// Console override
add_filter( 'tiledesk_console_url', function ( $url ) {
    return 'https://your-console.example.com/v2/dashboard/#/project/';
}, 10 );

// JS SDK override
add_filter( 'tiledesk_jssdk_url', function ( $url ) {
    return 'https://your-widget.example.com/v4/launch.js';
}, 10 );
```

= Are there any other resources I can use to learn more about Tiledesk? =

Yes. Most of the information you'll need is available on our website, but you can also have a look at out Developer documentation, Github repositories and Discord community:

- [Website](https://tiledesk.com/)
- [Knowledge Base](https://gethelp.tiledesk.com/)
- [Developer Hub](https://developer.tiledesk.com/)
- [Github Open Source](https://github.com/Tiledesk/)
- [Discord Community](https://discord.gg/nERZEZ7SmG)

== Changelog ==

= 1.0.2 =
* Bug fixing and finalizing plugin for publishing

= 1.0.1 =
* Bug fixing

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.2 =
* This version improves plugin security. Upgrade is strongly advised.

= 1.0.1 =
* This version fixes a security related bug. Upgrade immediately.

= 1.0.0 =
* Initial release.
