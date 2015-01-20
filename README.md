Simple JQuery Multi Image Uploader.
====================================

Simple jQuery plugin allows user to select multiple files and upload them all.
A php uploader class is provided for demonstration and testing.

### ***Quick start***

Include within the html <head> tags
--------------------------------------

<script src="js/uploader.js"></script>
<link href="css/style.css" rel="stylesheet">
<script>
$( document ).ready(function() {
  $('#upload').upload('upload.php', {
      classid:'.progress',  // container class or id
      thumb_show:true,		// if true thumbnails will be created as images are uploaded
      data:{'ajax':'1'}     // add form data you wish to include
  });
});
</script>

Include within the html <body> tags
--------------------------------------

<div class="progress">
  <input type="file" id="upload" name="files[]" multiple />
</div>

Notes
-------

Please see comments within the code to understand what is going on.
The uploader.js file will most likly need to be changed for different situations.

License
---------

Copyright (c) 2015 thehigherentity, released under the MIT License
