<?php
/*
Uploader, A Simple PHP Uploader class

Author:  Stephen Nugent
Email:   stephen@thehigherentity.com
Website: http://www.thehigherentity.com
Version: v1.0 ( 2015/01/20 )

The MIT License (MIT)

Copyright (c) 2015 thehigherentity

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

class Uploader {
  protected $dir;	// upload directory
  protected $exts;	// allowed extensions
  protected $name;	// upload field name
  protected $error;	// Error code value
  protected $limit;	// Size limit in MB
  protected $access;	// does dir exist and is it writable
  protected $unique; 	// unique name true/false
  protected $complete;  // true|false shows if upload completed

  /**
   * Class constructor
   *
   * $name,  The upload filed name
   * $dir    The dir to upload files into
   * $exts   An array of allowed extensions
   * $limit  The upload limit in MB
   */
  public function __construct($name, $dir, $exts, $limit) {
    $this->dir = $dir;
    $this->name = $name;
    $this->exts = $exts;
    $this->error = '0';
    $this->unique = false;
    $this->limit = (int)((1024*1000)*(int)$limit);
    $this->complete = false; // assume upload failed

    // If dir does not exist or is unwritable, try to create it.
    $this->access = (is_writable($dir))?true:(@mkdir($dir, 0755, true));
  }	

  /**
   * $bool, true|false if true a unique name .
   * will be given to uploaded file.
   */
  public function unique_name($bool) {
    $this->unique = $bool;
  }
  
  /**
   * Call this function to start the upload.
   */
  public function upload() {
    if ($this->checks()) {
      if (move_uploaded_file($_FILES[$this->name]["tmp_name"], $this->dir.'/'.$_FILES[$this->name]["name"])) {
	$this->complete = true;
	return true;
      } else { $this->error = '6'; }
    }
    return false;
  }

  /**
   * Protected function.
   * Called when upload function starts.
   * Checks to make sure file and directory meet requirements.
   */
  protected function checks() {
    if ($this->access) {
      if (isset($_FILES[$this->name]['tmp_name']) && is_uploaded_file($_FILES[$this->name]['tmp_name']) ) {
	$ext = $this->getExt($_FILES[$this->name]['name']);
	if (in_array($ext, $this->exts)) {
	  if ($this->mimeType($ext) == $_FILES[$this->name]['type']) {
	    $_FILES[$this->name]['name'] = ($this->unique)?(uniqid().'.'.$ext):strtolower($_FILES[$this->name]['name']);
	    if (!file_exists($this->dir.'/'.$_FILES[$this->name]["name"])) {
	      return true;
	    } else { $this->error = '5'; }
	  } else { $this->error = '4'; }
	} else { $this->error = '3'; }
      } else { $this->error = '2'; }
    } else { $this->error = '1'; }
    return false;
  }

  /**
   * Protected function
   * Used by getResults, to show readable error.
   */
  protected function errorCode($code){
    $error = array(
      '0' => '0: No errors reported.',
      '1' => '1: Please check your file permisions.',
      '2' => '2: No file was uploaded.',
      '3' => '3: This file type is not permitted.',
      '4' => '4: Mime type mismatch, File type does not match its extension.',
      '5' => '5: A file with this name already exists in the upload directory ('.$this->dir.').',
      '6' => '6: The file could not be created in the destination directory.'
    );
    return (isset($error[$code]))?$error[$code]:$code.'_: Unknown error.';
  }

  /**
   * Returns the uploaded files name.
   */
  public function getName(){
    return $_FILES[$this->name]['name'];
  }

  /**
   * This function returns information about the upload
   * Returns information in array format.
   */ 
  public function getResults(){
    $name  = $this->getName();
    $ext   = $this->getExt($name);
    $mime  = $this->mimeType($ext);
    $error = $this->errorCode($this->error);
    
    return array(
      'dir'      => $this->dir,
      'name'     => $name,
      'ext'      => $ext,
      'mime'     => $mime,
      'error'    => $error,
      'complete' => $this->complete,
    );
  }

  /**
   * Protected function
   * $name, takes the file name and returns its extension.
   * return file extention.
   */
  protected function getExt($name) {
    $name = (strpos($name, '.') === false)?$name.'.':$name;
    $ext = explode('.', $name);
    return strtolower(end($ext));
  }

  /**
   * Protected function
   * $ext, Takes a file extension and returns its mimetype.
   * return mimetype for the extention.
   */
  protected function mimeType($ext) {
    $mime_types = array(
      'txt'  => 'text/plain',
      'htm'  => 'text/html',
      'html' => 'text/html',
      'php'  => 'text/html',
      'css'  => 'text/css',
      'js'   => 'application/javascript',
      'json' => 'application/json',
      'xml'  => 'application/xml',
      'swf'  => 'application/x-shockwave-flash',
      'flv'  => 'video/x-flv',

      // images
      'png'  => 'image/png',
      'jpe'  => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'jpg'  => 'image/jpeg',
      'gif'  => 'image/gif',
      'bmp'  => 'image/bmp',
      'ico'  => 'image/vnd.microsoft.icon',
      'tiff' => 'image/tiff',
      'tif'  => 'image/tiff',
      'svg'  => 'image/svg+xml',
      'svgz' => 'image/svg+xml',

      // archives
      'zip' => 'application/zip',
      'rar' => 'application/x-rar-compressed',
      'exe' => 'application/x-msdownload',
      'msi' => 'application/x-msdownload',
      'cab' => 'application/vnd.ms-cab-compressed',

      // audio/video
      'mp3' => 'audio/mpeg',
      'wav' => 'audio/x-wav',
      'qt'  => 'video/quicktime',
      'mov' => 'video/quicktime',

      // adobe
      'pdf' => 'application/pdf',
      'psd' => 'image/vnd.adobe.photoshop',
      'ai'  => 'application/postscript',
      'eps' => 'application/postscript',
      'ps'  => 'application/postscript',

      // ms office
      'doc' => 'application/msword',
      'rtf' => 'application/rtf',
      'xls' => 'application/vnd.ms-excel',
      'ppt' => 'application/vnd.ms-powerpoint',

      // open office
      'odt' => 'application/vnd.oasis.opendocument.text',
      'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );
    return (isset($mime_types[$ext]))?$mime_types[$ext]:false;
  }
}
?>