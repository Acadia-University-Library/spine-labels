<?php

/* 
   Update this to contain info for your library or multiple libraries
   Each library added will be selectable in the dropdown, and the code can be used as a query param to auto select it
   Each library has a code that will be used as an identifier, a human readable name, and an API key
   Replace the API key with your own. It needs to have read permissions for "Bibs"
*/
$LIBRARIES = array(
  "sandbox" => array(
    "name" => "Alma Sandbox",
    "api_key" => "l7xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  )
);

// default font to use. Can also be overridden in the form
$FONT = "Arial";
$FONT_SIZE = 16;
$BOLD = true;

// labels per sheet
$ROWS = 7;
$COLUMNS = 8;
$MAX_LABELS = $ROWS * $COLUMNS; // don't edit this

// Sizes should be in DXA. In DXA 1440 is one inch, 720 is 1/2 inch, etc.
$LABEL_HEIGHT = 2160;
$LABEL_WIDTH = 1440;

// margins around the labels
$TOP_MARGIN = 360; // 1/4"
$LEFT_MARGIN = 405; // 9/32"
$RIGHT_MARGIN = 405;
$BOTTOM_MARGIN = 360;

// size of label sheet
$PAPER_HEIGHT = 15840; // 11"
$PAPER_WIDTH = 12240; // 8.5"

// whether to print collection codes at the top of the label
$PRINT_CODES = true;

// collection codes to skip printing
// this is used if you are printing collection codes, but still 
// want to skip certain ones
$SKIP_CODES = "XXXX1,XXXX2,XXXX3";

?>
