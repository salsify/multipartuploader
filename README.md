multipart uploader
========================

If you need to POST a multipart upload using PHP to some remote service, this will make your life much easier.


Usage
-----

```php
$uploader = new \MultipartUploader\Uploader('http://example.com/');
$uploader->addPart('key', 'value');
$uploader->addPart('key2', 'value2');
$uploader->addFile('file', '/path/to/some/file.json', 'application/json');
$uploader->addFile('file2', '/path/to/some/sweet_image.jpg', 'image/jpeg');
$response = $uploader->postData();
```

`$response` is an instance of [`HttpMessage`](http://www.php.net/manual/en/class.httpmessage.php).


Limitations
-----------

The file contents are loaded into memory, so this may not be suitable for streaming huge post bodies over the wire.


TODO
----

* Guess the content type from the filename.


License
-------

[MIT License](http://mit-license.org/) (c) Salsify, Inc.
