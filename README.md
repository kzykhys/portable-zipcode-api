Portable ZipCode API
====================

Easy, portable, self-hosting Japan postal code API

Live Example
-------------------------------------------------------------------------------------------------------

[Try the demo!](http://zipapi.pagodabox.com/)

Requirements
-------------------------------------------------------------------------------------------------------

* PHP5.3.3 or later
* SQLite support (maybe installed on default PHP build)
* Latest jQuery

Installation
-------------------------------------------------------------------------------------------------------

* Download [portable-zip-api.zip](http://kzykhys.github.com/portable-zipcode-api/releases/portable-zip-api.zip)
* Extract archive
* Upload zip.phar.php and zip.sqlite.db to same directory on your web server (eg. `http://www.example.com/API/zip.phar.php`)

If you prefer clean URLs, you'll need mod_rewrite and .htaccess files like this:

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    #RewriteBase /path/to/app
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ zip.phar.php [L]
</IfModule>
```

API Reference
-------------------------------------------------------------------------------------------------------

### General notes

* Every string passed to and from the API needs to be UTF-8 encoded.
* You can rename `zip.phar.php` to anything you like.

-------------------------------------------------------------------------------------------------------

### `/version` Get version

#### URL

```
GET /zip.phar.php/version
```

#### Return value

An object with mime type 'application/json'

``` json
{
    "version": "1.0.0"
}
```

-------------------------------------------------------------------------------------------------------

### `/api` Get Javascript API

#### URL

```
GET /zip.phar.php/api
```

#### Return value

The content of Javascript API with mime type 'text/javascript'

-------------------------------------------------------------------------------------------------------

### `/search/{code}.{format}` Find an address by zip-code

#### URL

```
GET  /zip.phar.php/search/{code}
GET  /zip.phar.php/search/{code}.{format}
POST /zip.phar.php/search
```

#### Arguments

* **code** *(string/integer)* The code to search (9999999/999-9999)
* **format** *(string)* The format (json|xml|php) Default:json

#### Return value

##### Format: json

An object with mime type 'application/json'

``` json
{
    "result": true,
    "data": {
        "id":   "38201",
        "code": "1600022",
        "pref": "\u6771\u4eac\u90fd",
        "city": "\u65b0\u5bbf\u533a",
        "town": "\u65b0\u5bbf"
    }
}
```

#### Format: xml

A xml document with mime type 'application/xml'

``` xml
<?xml version="1.0"?>
<response>
    <result>1</result>
    <data>
        <id>38201</id>
        <code>1600022</code>
        <pref>&#x6771;&#x4EAC;&#x90FD;</pref>
        <city>&#x65B0;&#x5BBF;&#x533A;</city>
        <town>&#x65B0;&#x5BBF;</town>
    </data>
</response>
```

##### Format: php

A PHP [serialized](http://www.php.net/manual/en/function.serialize.php) string with mime type 'text/plain'

```
a:2:{s:6:"result";b:1;s:4:"data";a:5:{s:4:"city";s:9:"新宿区";s:4:"code";s:7:"1600022";s:2:"id";s:5:"38201";s:4:"pref";s:9:"東京都";s:4:"town";s:6:"新宿";}}
```

JavaScript API
-------------------------------------------------------------------------------------------------------

### Configure and Paste the code above

``` html
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="/path/to/zip.phar.php/api" type="text/javascript"></script>
```

### Call the API through jQuery

``` javascript
$.zipSearch('950-2014').done(function(json) {
    if (json.result) {
        console.log(json.data.pref);
        console.log(json.data.city);
        console.log(json.data.town);
    } else {
        console.log('Address not found for code: ' + json.data.code);
    }
});
```

Update address database
-------------------------------------------------------------------------------------------------------

### Download full source code from repository

``` sh
$ git clone https://github.com/kzykhys/portable-zipcode-api.git zipapi
$ cd zipapi
```

### Install dependencies via Composer

``` sh
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```

### Download CSV (Lzh archive) from official website

Download lzh archive from [http://www.post.japanpost.jp/zipcode/dl/kogaki.html](http://www.post.japanpost.jp/zipcode/dl/kogaki.html)

or just run

``` sh
$ php app/console.php csv:download
```

### Extract Lzh archive

If you have lha command, just type

``` sh
$ cd ./csv
$ find . -type f -exec lha x {} \;
```

### Setup database

``` sh
$ php app/console.php csv:import ./csv
```

### Rebuild phar archive

``` sh
$ php app/console.php build:phar
```

Author
-------------------------------------------------------------------------------------------------------

Kazuyuki Hayashi (@kzykhys)

LICENSE
-------------------------------------------------------------------------------------------------------

The MIT License
