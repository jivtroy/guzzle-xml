# Guzzle XML

Guzzle XML request and response.

### Contents

1. [Compatibility](#compatibility)
2. [Installation](#installation)
   1. [Composer](#composer)
3. [Usage](#usage)
   1. [Request options](#request-options)
   2. [Response](#response)
4. [Author](#author)
5. [License](#license)

## Compatibility

Library | Version
------- | -------
PHP | >= 8.2
Guzzle | >= 7.0
Symfony Serializer | >= 7.0
Laravel | >= 12.0

## Installation

### Composer

```bash
composer require jivtroy/guzzle-xml
```

## Usage

### Request options

Following example creates POST request with XML body. Option `xml` accepts an array that is converted to XML document. About array format and how converting works you can read in detail [Symfony XmlEncoder](https://symfony.com/doc/current/components/serializer.html#the-xmlencoder).

```php
<?php
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleXml\XmlMiddleware;

$stack = HandlerStack::create();
$stack->push(XmlMiddleware::xml(), 'xml');
$client = new Client(['handler' => $stack]);
$response = $client->post('https://example.com', [
  'xml' => [
    'package' => [
        '@language' => 'PHP',
        'name'      => 'Guzzle XML',
        'author'    => [
          '@role' => 'developer',
          '#'     => 'Jiv Troy',
        ],
        'support'   => [
            'issues' => 'https://github.com/jivtroy/guzzle-xml/issues',
            'source' => 'https://github.com/jivtroy/guzzle-xml',
        ],
    ],
  ],
]);
```

As a result, an xml request will be sent with the header `Content-type: text/xml` and data with the following content:

```xml
<?xml version="1.0"?>
<package language="PHP">
   <name>Guzzle XML</name>
   <author role="developer">Jiv Troy</author>
   <support>
      <issues>https://github.com/jivtroy/guzzle-xml/issues</issues>
      <source>https://github.com/jivtroy/guzzle-xml</source>
   </support>
</package>
```

### Response

Automatically convert your JSON response to XML using middleware.

```php
<?php
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleXml\XmlMiddleware;

$stack = HandlerStack::create();
$stack->push(XmlMiddleware::jsonToXml());
$client = new Client(['handler' => $stack]);
$response = $client->post('https://example.com');
$xml = $response->getBody();
echo $xml;
```

If your json response is:

```json
{
   "package": {
      "@language":"PHP",
      "name":"Guzzle XML",
      "author": {
         "@role":"developer",
         "#":"Jiv Troy"
      },
      "support": {
         "issues":"https:\/\/github.com\/jivtroy\/guzzle-xml\/issues",
         "source":"https:\/\/github.com\/jivtroy\/guzzle-xml"
      }
   }
}
```

This will automatically convert to XML like this:

```xml
<?xml version="1.0"?>
<package language="PHP">
   <name>Guzzle XML</name>
   <author role="developer">Jiv Troy</author>
   <support>
      <issues>https://github.com/jivtroy/guzzle-xml/issues</issues>
      <source>https://github.com/jivtroy/guzzle-xml</source>
   </support>
</package>
```

## Author

* [Dev](https://dev.to/jivtroy)
* [Medium](https://medium.com/@jivtroy)
* [YouTube](https://www.youtube.com/@jivtroy)

## License

This project is licensed under the **MIT License** - see the `LICENSE` file for details.