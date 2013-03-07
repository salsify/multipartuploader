<?php
namespace MultipartUploader;

class Uploader {
  const EOL = "\r\n";

  // POST endpoint
  private $_url;

  // divides sections of a multi-part MIME document
  private $_mime_boundary;

  // the body of the request
  private $_data;


  public function __construct($url) {
    $this->_url = $url;
    $this->_mime_boundary = md5(time());
    $this->_data = '';
  }


  private function _add_part_header() {
    $this->_data .= '--' . $this->_mime_boundary . self::EOL;
  }


  public function addPart($key, $data) {
    $this->_add_part_header();
    $this->_data .= 'Content-Disposition: form-data; name="'.$key.'"' . self::EOL . self::EOL;
    $this->_data .= $data . self::EOL;
  }


  // TODO there has to be a cleaner way to do this...
  private function _guess_encoding($content_type) {
    if (strcasecmp(substr($content_type,0,strlen('text')), 'text') === 0) {
      return 'text';
    } elseif (strcasecmp($content_type, 'application/json')) {
      return 'text';
    } else {
      return 'binary';
    }
  }


  public function addFile($key, $file, $type) {
    $this->_add_part_header();

    // TODO use PECL library to guess MIME type here
    //      http://www.php.net/manual/en/function.finfo-file.php
    
    $encoding = $this->_guess_encoding($type);

    $this->_data .= 'Content-Disposition: form-data; name="' . $key . '"; filename="' . basename($file) .'"'. self::EOL; 
    $this->_data .= 'Content-Type: ' . $type . self::EOL; 
    $this->_data .= 'Content-Transfer-Encoding: ' . $encoding . self::EOL . self::EOL; 
    $this->_data .= file_get_contents($file) . self::EOL; 
  }


  // Performs the actual post now that the data is all set.
  // Returns an instance of \HttpMessage
  public function postData() {
    // definitely need the 2 EOLs at the end here
    $this->_data .= '--' . $this->_mime_boundary . '--' . self::EOL . self::EOL;

    $request = new \HttpRequest($this->_url, \HTTP_METH_POST);
    $content_type = 'multipart/form-data; boundary=' . $this->_mime_boundary;
    $request->addHeaders(array('Content-Type' => $content_type));
    $request->setBody($this->_data);

    return $request->send();
  }
} 
