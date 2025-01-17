<p align="center">
  <img src="https://raw.githubusercontent.com/eValue8bv/creditable-paywall/main/logo.png"/>
</p>

# Creditable PayWall PHP Package

[![Latest Stable Version](https://poser.pugx.org/evalue8bv/creditable-paywall/v)](https://packagist.org/packages/evalue8bv/creditable-paywall)
[![Total Downloads](https://poser.pugx.org/evalue8bv/creditable-paywall/downloads)](https://packagist.org/packages/evalue8bv/creditable-paywall)
[![License](https://poser.pugx.org/evalue8bv/creditable-paywall/license)](https://packagist.org/packages/evalue8bv/creditable-paywall)

The Creditable-Paywall PHP package is a simple and easy-to-use wrapper for the Creditable API. Its purpose is to enable the integration of a Creditable button into an existing paywall system, thus enabling customers to pay for articles on a pay-per-article basis with credits.

## Requirements

To use the Creditable pay per article button, the following things are required:

- Get yourself a free Creditable Partner Account at https://partner.creditable.news. No signup costs are applicable.
- Login to your dashboard at https://partner.creditable.news and add your media title(s).
- An apikey will be generated for each media title you add.

## Installation

To install the package, use [Composer](https://getcomposer.org/) by running the following command:

```sh
composer require evalue8bv/creditable-paywall
```

## Usage

The javascript code of Creditable requires you to have the following HTML elements on your page:

```html
<div id="creditable-container" class="creditable-container">
  <!-- the button -->
  <div id="creditable-button"></div>
  <!-- popup window -->
  <div id="creditable-window"></div>
</div>
```

Include the following stylesheet in your head element:

```php
<link rel="stylesheet" type="text/css" href="<?= $creditable->getCssDependency(); ?>;" />
```

### Setting environment to dev

```php
require_once 'vendor/autoload.php';

use Creditable\CreditablePayWall;

$apiKey = 'your-api-key-here';
$options = [
    'environment' => 'dev'
];
$creditable = new CreditablePayWall($apiKey, $options);

// Rest of the code
```

### Checking for article access

```php
<?php

require_once 'vendor/autoload.php';

use Creditable\CreditablePayWall;

$apiKey = 'your-api-key-here';
$creditable = new CreditablePayWall($apiKey);

$creditable_article_id = "<<ARTICLE ID>>"; // Alphanumeric (required)
$creditable_article_title = "<<ARTICLE TITLE>>"; // Alphanumeric (required)
$creditable_topic_id = "<<TOPIC ID>>"; // Alphanumeric (required)
$creditable_topic_name = "<<TOPIC NAME>>"; // Alphanumeric (required)
$creditable_topic_desc = "<<TOPIC DESC>>"; // Alphanumeric (optional)
$creditable_topic_url = "<<TOPIC URL>>"; // Alphanumeric (optional)
$creditable_article_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // Alphanumeric (required)
$creditable_article_lang = "en-GB"; // ISO (required)
$creditable_article_authors  =   "<<ARTICLE AUTHORS>>";   // Alphanumeric (comma separated) String (optional)
$creditable_article_desc = "<<ARTICLE DESC>>"; // Alphanumeric (optional) teaser, used to tease recommended articles to users)
$creditable_article_tags = "<<TAGS>>"; // Alphanumeric (optional) comma delimited list or json (optional keywords, used to find recommended articles for users)
$creditable_article_img = "<<ARTICLE IMG URL>>"; // Alphanumeric (optional) URL for article image

// GET LOCAL CREDITABLE JWT COOKIE
$creditable_cookie = $_COOKIE['cjwt'] ?? "";

// Example usage
$data = [
    'jwt' => $creditable_cookie,
    'article_id' => $creditable_article_id,
    'article_name' => $creditable_article_title,
    'topic_id' => $creditable_topic_id,
    'topic_name' => $creditable_topic_name,
    'topic_desc' => $creditable_topic_desc,
    'topic_url' => $creditable_topic_url,
    'article_url' => $creditable_article_url,
    'article_lang' => $creditable_article_lang,
    'article_authors' => $creditable_article_authors,
    'article_desc' => $creditable_article_desc,
    'article_tags' => $creditable_article_tags,
    'article_img' => $creditable_article_img
];

try {
    $result = $creditable->check($data);
    if ($result->isPaid()) {
        // Access granted
    } else {
        // Access denied
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```

### JavaScript footer script:

```php
<!-- creditable scripts -->
<?php if (!$result->isPaid()){ ?>
<script type="text/javascript">
    <!--//
    const cUid = <?= $result->getUid(); ?>;
    //-->
</script>
<script src="<?= $creditable->getJsDependency(); ?>"></script>
<?php } ?>
```

## API Documentation

For more information about the Creditable API, please refer to the [official documentation](https://www.creditable.news/en/integration-manual).

## Contributing

If you would like to contribute, please feel free to submit a pull request on our [GitHub repository](https://github.com/eValue8bv/creditable-api).

## License

This package is released under the [Apache license](https://www.apache.org/licenses/LICENSE-2.0).

## Support

Contact: [www.creditable.news](https://www.creditable.news) — info@creditable.news — +31 (0)24 350 54 00
