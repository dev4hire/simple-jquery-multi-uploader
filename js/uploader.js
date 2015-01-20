/*
SJQIUploader, Simple JQuery Image Uploader

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

  $.fn.upload=function(url,settings) {
   
    // I wouldent change anything besides 'classid' and 'thumb_show'
    var option={
      classid:'.progress', // class or id of container
      method:'POST',	   // for delivery method
      action:url,	   // url to send data
      dataType:'json',	   // datatype returned
      thumb_show:true,     // true to show thumbnail after upload
      data:{},		   // add formdata to submit with file
    };
    
    if(!settings) var settings={}; 	// if no settings create empty
    $.extend(option, settings);		// merge setting with options
   
    /*
     * Upload the given file
     */
    function uploadit(file) {
      // create formdata and add file to upload
      var formData = new FormData( );
      formData.append("image", file);
      
      // Add any other formdata
      $.each( option.data, function( index, value ) {
	formData.append(index, value);
      });

      // submit file with ajax
      $.ajax({
	xhr: function() {
	  addbar(file);   // add progress bar for given file, updating as file transfers
	  var xhr = new window.XMLHttpRequest();
	  xhr.upload.addEventListener("progress", function(evt){
	    if (evt.lengthComputable) {
	      var progressBar = document.getElementById("progress" + file.id); 
	      progressBar.max = evt.total;
	      progressBar.value = evt.loaded;
	      // console.log(Math.round( ( evt.loaded / evt.total ) * 100 ) + "%"); // debug
	    }
	  }, false);
	  return xhr;
	},
	url: option.action,
	type: option.method,
	data: formData,
	dataType:option.dataType,
	processData: false,
	contentType: false,   
	success: function (response) {
	  delbar(file); // delete progress bar on success
	  console.log(response);
	  if ( response.complete && response.dir && response.name ) { // check for thumbnail data and add thumbnail
	     addthumb(response.dir, response.name);
	  }
	  if (response.error && !response.error.match("^0:") ) { // check for error data and add errors
	      adderror(response.error);
	  }
	},
        error: function(jqXHR, textStatus, errorThrown)  {
	  console.log(textStatus + ":" + errorThrown);
	  delbar(file); // delete progress bar on error
        }
      });
    }   
    
    /*
     * Add progress bar for given file
     */
    function addbar(file) {
      $(option.classid).prepend('<div id="bar' + file.id + '"><span>' + file.name + '</span><br>'
			      + '<progress id="progress' + file.id + '" max="100" value="0"></progress>'
			      + '</div>');
    }
    
    /*
     * Remove the progress bar for given file
     */
    function delbar(file) {
      $('#bar' + file.id).remove();
    }
    
    /*
     * Create ul tags and add thumbnails, if option is set     
     */
    function addthumb(dir, file) {
      if ( option.thumb_show ) {
	if ( ! $('#thumbs').length ) {
	  $(option.classid).append('<ul id="thumbs"></ul>');
	}
	$('#thumbs').prepend('<li><img alt="" width="100px" src="' + dir + '/' + file + '" /></li>');
      }
    }
    
    /*
     * create error message container and display message
     */
    function adderror(msg) {
      if ( ! $('#error').length ) {
	$(option.classid).prepend('<div id="error"></div>');
      }
      $('#error').prepend('<div>' + msg + '</div>')
    }
    
    /* 
     * listen for files being selected and 
     * pass them one by one into the uploadit function
     */
    $("#upload").change(function (){
      var elem = document.getElementById($(this).attr('id'));
      for (var i = 0; i < elem.files.length; ++i) {
	
	// check file types
	if (!(/\.(gif|jpg|jpeg|png)$/i).test(elem.files.item(i).name)) {              	    
	  adderror('Invalid file type');
	} else {
	  elem.files.item(i).id = i; // add id to file (used to id progress bars etc)
	  uploadit(elem.files.item(i));
	}
      }
    });
  };
