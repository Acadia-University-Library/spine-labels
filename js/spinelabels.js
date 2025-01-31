$(document).ready(function() {
  // Setup default variables
  var bcCount = 0;
  var labelID = 0;
  $('#sbc').focus();
  
  // Override the forms submit to add the barcode to the form that will be submitted.
  $(document).on('submit', '#enterbc', function(e) {
    e.preventDefault();
    // Format the barcode. Remove all characters that aren't alphanumeric
    var bc = $('#sbc').val().trim();
    bc = bc.replace(/\W/g, '');
    // Make sure there is anything left after the formatting.
    if (bc.length > 0) {
      var $la = $('#label-area tbody');
      bcCount++;
      labelID++;
      // Add the row to the top of the table.
      $la.prepend('<tr id="row'+ labelID +'" ><td><input type="hidden" name="barcode['+ labelID +']" value="'+ bc +'">'+ bc +'</td><td></td><td><span class="delete" data-id="'+ labelID +'">[x]</span></td></tr>');
      
      $('#sbc').val('').focus();
    }
    checkCount();
  });
  
  // Bind a click on the button to delete a barcode that has been already entered..
  $(document).on('click', '.delete', function(e) {
    e.preventDefault();
    var result = confirm('Are you sure you want to delete this barcode?');
    if (result) {
      var rid = $(this).data('id');
      $('#row' + rid).remove();
      bcCount--;
      checkCount();
    }
    $('#sbc').focus();
  });
  
  // Clear all barcodes from the list.
  $('#delete-all').on('click', function(e) {
    e.preventDefault();
    var result = confirm('Are you sure you want to delete all the barcodes?');
    if (result) {
      location.reload();
    }
  });
  
  // Mark all barcodes as new items.
  $('#check-all').on('click', function(e) {
    e.preventDefault();
    if ($('.isNew').length > 0 && $('.isNew:first').prop('checked')) {
      $('.isNew').prop('checked', false);
      $(this).html('Check All');
    }
    else {
      if ($('.isNew').length > 0) {
        $('.isNew').prop('checked', true);
        $(this).html('Uncheck All');
      }
    }
    $('#sbc').focus();
  });
  
  // Checks the count of entered barcodes to make sure we are under our limit
  // Also if we get more than 0 we add another submit button to get the labels for easy of use.
  function checkCount() {
    var label = $("#barcode_num");
    if (bcCount > maxCodes) {
      label.addClass("alert");
      label.html('You have ' + (bcCount - maxCodes) + ' too many barcodes!');
    }
    else {
      label.removeClass("alert");
      label.html('Currently ' + bcCount + ' barcodes');
    }
    if (bcCount > 0)
      $('#sendlabels-top').show();
    else
      $('#sendlabels-top').hide();
  }
  
  // As long as there is at least one barcode entered, submit the form.
  $('#sendlabels, #sendlabels-top').on('click', function(e) {
    e.preventDefault();
    if (bcCount > 0) {
      $('#label-form').submit();
    }
  });

  checkCount();
});
 
