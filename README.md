# Easy Search plugin for Craft CMS 3.x

Craft's search functionality, powerful as it may be, is not as end user friendly as you'd like it to be. With Easy Search, your content editors can use predefined search queries ("search for all entries with an image field that's not empty") or build their own search query with a user friendly GUI.

![Easy Search](resources/gif/easy-search.gif)

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require nilsenpaul/craft-easy-search

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Easy Search.

## Configuring Easy Search (optional)

If you want to be able to use predefined search queries with Easy Search, create a file in Craft's `config` folder, named `easy-search.php`. In this file, you'd add the following:

```
<?php

return [
  'queries' => [
    [
      'query' => 'title:*',
      'label' => 'All items with a title',
      'element' => 'craft\\elements\\Entry', // This is optional, and will make Easy Search only show this query on the Entries element index
    ],
    [
      'query' => 'image:*',
      'label' => 'All items with an image',
    ],
    [
      'query' => '-image:*',
      'label' => 'All items without an image',
    ],
  ]
];
```

Brought to you by [nils&paul](https://nilsenpaul.nl)
