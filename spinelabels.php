<?php
include('configs/config.php');

//
// Get variables from config, but override with query params if present
//

$print_codes = false;
if (isset($_POST['print_codes']) && $_POST['print_codes'])
  $print_codes = true;

$DONT_PRINT = explode(',', $SKIP_CODES);
if (isset($_POST['skip-codes']))
  $DONT_PRINT = explode(',', $_POST['skip-codes']);

$font = $FONT;
if (isset($_POST['font']))
  $font = $_POST['font'];

$fontsize = $FONT_SIZE;
if (isset($_POST['fontsize']))
  $fontsize = $_POST['fontsize'];
// fonts are specified in twice the normal number for some reason
$fontsize = (int)$fontsize * 2;

$bold = "false";
if (isset($_POST['bold']) && $_POST['bold'])
  $bold = "true";

$rows = $ROWS;
if (isset($_POST['rows']))
  $rows = $_POST['rows'];

$columns = $COLUMNS;
if (isset($_POST['columns']))
  $columns = $_POST['columns'];

$label_h = $LABEL_HEIGHT;
if (isset($_POST['height']))
  $label_h = $_POST['height'];

$label_w = $LABEL_WIDTH;
if (isset($_POST['width']))
  $label_w = $_POST['width'];

$top_mar = $TOP_MARGIN;
if (isset($_POST['top-margin']))
  $top_mar = $_POST['top-margin'];

$left_mar = $LEFT_MARGIN;
if (isset($_POST['left-margin']))
  $left_mar = $_POST['left-margin'];

$right_mar = $RIGHT_MARGIN;
if (isset($_POST['right-margin']))
  $right_mar = $_POST['right-margin'];

$bottom_mar = $BOTTOM_MARGIN;
if (isset($_POST['bottom-margin']))
  $bottom_mar = $_POST['bottom-margin'];

$callUrl = $LIBRARIES[$_POST['library']]["api_url"] . 
           '?apikey=' . $LIBRARIES[$_POST['library']]["api_key"];

//
// Perform curl call to Primo and return xml.
//

function performCall($url) {
  return @simplexml_load_file($url);
}

$labels = array();

$barCodes = $_POST['barcode'];
ksort($barCodes);

foreach ($barCodes as $key => $bc) {
  $call1 = $callUrl . '&item_barcode=' . $bc;
  $xml1 = performCall($call1);

  // add error message if there is one
  if ($xml1 === false) {
    $labels[] = ["Barcode not found: " . $bc];
    continue;
  }

  $callNo = htmlentities(trim($xml1->holding_data->call_number));
  $vol = htmlentities(trim($xml1->item_data->enumeration_a));
  if (is_numeric($vol))
    $vol = 'v.' . $vol;
  $no = htmlentities(trim($xml1->item_data->enumeration_b));
  if (is_numeric($no))
    $no = 'no.' . $no;
  $year = substr(htmlentities(trim($xml1->item_data->chronology_i)), -4);
  $location = htmlentities(trim($xml1->item_data->location));
  $copy = htmlentities(trim($xml1->holding_data->copy_id));

  $callArr = explode(" ", $callNo);

  // Split the first part of call number into two parts, letters and digits.
  // Add it as the first line of the label
  $labelArr = preg_split('#(?<=[A-Z])(?=[0-9])#i', $callArr[0]);
  unset($callArr[0]);

  // Add the rest of the call number to the label
  $labelArr = array_merge($labelArr, $callArr);
  
  // If there is a location add it to the first line of the label
  if ($print_codes && $location != '' && !in_array($location, $DONT_PRINT))
    array_unshift($labelArr, $location);
    
  // add the volume and number to the label
  $labelArr[] = $vol;
  $labelArr[] = $no;
  $labelArr[] = $year;

  // and copy if available
  if ($copy != '' && $copy > 1)
    $labelArr[] = 'c.' . $copy;

  $finalArr = array();

  // Some final formatting and splitting up long lines
  foreach ($labelArr as $l) {
    $line = str_replace("_", "-", $l);

    if (strlen($line) > 8) {
      $punc = array('-', '/', '.', ':');
      $noPunc = true;
      foreach ($punc as $p) {
        $pos = strpos($line, $p);
        if ($pos !== false) {
          $first = substr($line, 0, $pos + 1);
          $sec   = substr($line, $pos + 1, strlen($line));
          $finalArr[] = $first;
          $finalArr[] = $sec;
          $noPunc = false; 
          break;
        }
      }
      if ($noPunc) {
       $finalArr[] = $line; 
      }
    }
    else {
      $pos = strpos($line, ':');
      if ($pos !== false) {
        $first = substr($line, 0, $pos + 1);
        $sec   = substr($line, $pos + 1, strlen($line));
        $finalArr[] = $first;
        $finalArr[] = $sec;
      } else {
        $finalArr[] = $line;
      }
    }
  }

  // add error message if there is one
  if ($xml1 === false)
    $finalArr[] = "Barcode not found: " . $bc;
  
  // Add the fully formatted label to the whole list of labels
  $labels[] = array_filter($finalArr);

}

// Start of the ooxml to create the word document template

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=spinelabels.doc");
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

echo("<" . "?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"?" . ">");
echo("<?mso-application progid=\"Word.Document\"?>");

?>
<w:wordDocument xmlns:aml="http://schemas.microsoft.com/aml/2001/core" xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml" xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint" xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml" xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" w:macrosPresent="no" w:embeddedObjPresent="no" w:ocxPresent="no" xml:space="preserve">
  <w:ignoreSubtree w:val="http://schemas.microsoft.com/office/word/2003/wordml/sp2"/>
  <o:DocumentProperties>
    <o:Title>Spine Labels</o:Title>
  </o:DocumentProperties>
  <w:fonts>
    <w:defaultFonts w:ascii="<?=$font?>" w:fareast="<?=$font?>" w:h-ansi="<?=$font?>" w:cs="<?=$font?>"/>
    <w:font w:name="<?=$font?>">
      <w:panose-1 w:val="02070309020205020404"/>
      <w:charset w:val="00"/>
      <w:family w:val="Modern"/>
      <w:pitch w:val="fixed"/>
      <w:sig w:usb-0="20002A87" w:usb-1="00000000" w:usb-2="00000000" w:usb-3="00000000" w:csb-0="000001FF" w:csb-1="00000000"/>
    </w:font>
  </w:fonts>
  <w:styles>
    <w:versionOfBuiltInStylenames w:val="7"/>
    <w:latentStyles w:defLockedState="off" w:latentStyleCount="267">
      <w:lsdException w:name="Normal"/>
      <w:lsdException w:name="heading 1"/>
      <w:lsdException w:name="heading 2"/>
      <w:lsdException w:name="heading 3"/>
      <w:lsdException w:name="heading 4"/>
      <w:lsdException w:name="heading 5"/>
      <w:lsdException w:name="heading 6"/>
      <w:lsdException w:name="heading 7"/>
      <w:lsdException w:name="heading 8"/>
      <w:lsdException w:name="heading 9"/>
      <w:lsdException w:name="caption"/>
      <w:lsdException w:name="Title"/>
      <w:lsdException w:name="Subtitle"/>
      <w:lsdException w:name="Strong"/>
      <w:lsdException w:name="Emphasis"/>
      <w:lsdException w:name="No Spacing"/>
      <w:lsdException w:name="List Paragraph"/>
      <w:lsdException w:name="Quote"/>
      <w:lsdException w:name="Intense Quote"/>
      <w:lsdException w:name="Subtle Emphasis"/>
      <w:lsdException w:name="Intense Emphasis"/>
      <w:lsdException w:name="Subtle Reference"/>
      <w:lsdException w:name="Intense Reference"/>
      <w:lsdException w:name="Book Title"/>
      <w:lsdException w:name="TOC Heading"/>
    </w:latentStyles>
    <w:style w:type="paragraph" w:default="on" w:styleId="Normal">
      <w:name w:val="Normal"/>
      <w:rsid w:val="001B5590"/>
      <w:rPr>
        <wx:font wx:val="<?=$font?>"/>
        <w:sz w:val="<?=$fontsize?>"/>
        <w:sz-cs w:val="<?=$fontsize?>"/>
        <w:lang w:val="EN-US" w:fareast="EN-US" w:bidi="AR-SA"/>
      </w:rPr>
    </w:style>
    <w:style w:type="character" w:default="on" w:styleId="DefaultParagraphFont">
      <w:name w:val="Default Paragraph Font"/>
    </w:style>
    <w:style w:type="table" w:default="on" w:styleId="TableNormal">
      <w:name w:val="Normal Table"/>
      <wx:uiName wx:val="Table Normal"/>
      <w:rPr>
        <wx:font wx:val="<?=$font?>"/>
        <w:lang w:val="EN-US" w:fareast="EN-US" w:bidi="AR-SA"/>
      </w:rPr>
      <w:tblPr>
        <w:tblInd w:w="0" w:type="dxa"/>
        <w:tblCellMar>
          <w:top w:w="0" w:type="dxa"/>
          <w:left w:w="108" w:type="dxa"/>
          <w:bottom w:w="0" w:type="dxa"/>
          <w:right w:w="108" w:type="dxa"/>
        </w:tblCellMar>
      </w:tblPr>
    </w:style>
    <w:style w:type="list" w:default="on" w:styleId="NoList">
      <w:name w:val="No List"/>
    </w:style>
    <w:style w:type="table" w:styleId="TableGrid">
      <w:name w:val="Table Grid"/>
      <w:basedOn w:val="TableNormal"/>
      <w:rsid w:val="00605154"/>
      <w:rPr>
        <wx:font wx:val="<?=$font?>"/>
      </w:rPr>
      <w:tblPr>
        <w:tblInd w:w="0" w:type="dxa"/>
        <w:tblBorders>
          <w:top w:val="single" w:sz="4" wx:bdrwidth="10" w:space="0" w:color="auto"/>
          <w:left w:val="single" w:sz="4" wx:bdrwidth="10" w:space="0" w:color="auto"/>
          <w:bottom w:val="single" w:sz="4" wx:bdrwidth="10" w:space="0" w:color="auto"/>
          <w:right w:val="single" w:sz="4" wx:bdrwidth="10" w:space="0" w:color="auto"/>
          <w:insideH w:val="single" w:sz="4" wx:bdrwidth="10" w:space="0" w:color="auto"/>
          <w:insideV w:val="single" w:sz="4" wx:bdrwidth="10" w:space="0" w:color="auto"/>
        </w:tblBorders>
        <w:tblCellMar>
          <w:top w:w="0" w:type="dxa"/>
          <w:left w:w="120" w:type="dxa"/>
          <w:bottom w:w="0" w:type="dxa"/>
          <w:right w:w="120" w:type="dxa"/>
        </w:tblCellMar>
      </w:tblPr>
    </w:style>
  </w:styles>
  <w:docPr>
    <w:view w:val="print"/>
    <w:zoom w:percent="100"/>
    <w:doNotEmbedSystemFonts/>
    <w:proofState w:spelling="clean" w:grammar="clean"/>
    <w:stylePaneFormatFilter w:val="3F01"/>
    <w:defaultTabStop w:val="720"/>
    <w:punctuationKerning/>
    <w:characterSpacingControl w:val="DontCompress"/>
    <w:optimizeForBrowser/>
    <w:validateAgainstSchema/>
    <w:saveInvalidXML w:val="off"/>
    <w:ignoreMixedContent w:val="off"/>
    <w:alwaysShowPlaceholderText w:val="off"/>
    <w:compat>
      <w:breakWrappedTables/>
      <w:snapToGridInCell/>
      <w:wrapTextWithPunct/>
      <w:useAsianBreakRules/>
      <w:dontGrowAutofit/>
    </w:compat>
    <wsp:rsids>
      <wsp:rsidRoot wsp:val="001B5590"/>
      <wsp:rsid wsp:val="001B5590"/>
      <wsp:rsid wsp:val="00BE2E45"/>
    </wsp:rsids>
  </w:docPr>
  <w:body>
    <wx:sect>
      <w:tbl>
        <w:tblPr>
          <w:tblW w:w="<?= $columns * $label_w ?>" w:type="dxa"/>
          <w:jc w:val="center"/>
          <w:tblLayout w:type="Fixed"/>
          <w:tblCellMar>
            <w:top w:w="150" w:type="dxa"/>
            <w:left w:w="50" w:type="dxa"/>
            <w:bottom w:w="0" w:type="dxa"/>
            <w:right w:w="0" w:type="dxa"/>
          </w:tblCellMar>
          <w:tblLook w:val="01E0"/>
        </w:tblPr>
        <w:tblGrid>
        <?php
        for ($i = 0; $i < $columns; $i++)
          echo '<w:gridCol w:w="' . $label_w . '"/>'
        ?>
        </w:tblGrid>

<?php

$lclength = count($labels);
$now = 0;

// First loop over each row
for($tablerow = 1; $tablerow <= $rows; $tablerow++) {
  // Loop over each column
  for ($i = 1; $i <= $columns; $i++) {
    if ($i == 1){
?>
        <w:tr wsp:rsidR="001B5590" wsp:rsidRPr="00BE2E45" wsp:rsidTr="00BE2E45">
          <w:trPr>
            <w:cantSplit/>
            <w:trHeight w:h-rule="exact" w:val="<?= $label_h ?>"/>
            <w:jc w:val="center"/>
          </w:trPr>
<?php
		}
?>
          <w:tc>
            <w:tcPr>
              <w:tcW w:w="<?= $label_w ?>" w:type="dxa"/>
            </w:tcPr>
<?php
    // If we are done with the labels but still room on the sheet just add a blank entry
    if ($now > $lclength - 1){
      $LCcur = "";
    }
    else{
      $LCcur = array_values($labels[$now]);
    }

    // Print a blank table cell
    if (empty($LCcur)){
?>
      <w:p>
        <w:pPr>
          <w:spacing w:line="<?=$fontsize?>0" w:line-rule="exact"/>
	  <w:rPr>
	    <w:rFonts w:ascii="<?=$font?>" w:h-ansi="<?=$font?>" w:cs="<?=$font?>"/>
	    <wx:font wx:val="<?=$font?>"/>
	    <w:b w:val="<?=$bold?>"/> 
            <w:sz w:val="<?=$fontsize?>"/>
            <w:sz-cs w:val="<?=$fontsize?>"/>
            <w:lang w:val="EN-CA"/>
          </w:rPr>
				</w:pPr>
			</w:p>
		</w:tc>
<?php
    }
    // A cell that has a label in it.
    else {
?>
            <w:p wsp:rsidR="001B5590" wsp:rsidRPr="00BE2E45" wsp:rsidRDefault="00BE2E45" wsp:rsidP="00BE2E45">
              <w:pPr>
                <w:spacing w:line="<?=$fontsize?>0" w:line-rule="exact"/>
                <w:rPr>
                  <w:rFonts w:ascii="<?=$font?>" w:h-ansi="<?=$font?>" w:cs="<?=$font?>"/>
                  <wx:font wx:val="<?=$font?>"/>
                  <w:b w:val="<?=$bold?>"/> 
                  <w:sz w:val="<?=$fontsize?>"/>
                  <w:sz-cs w:val="<?=$fontsize?>"/>
                  <w:lang w:val="EN-CA"/>
                </w:rPr>
              </w:pPr>

<?php
      // Loop over the label array and print each item on it's own line.
	for ($lcline = 0; $lcline < sizeof($LCcur); $lcline++){
?>
              <w:r wsp:rsidRPr="00BE2E45">
                <w:rPr>
                  <w:rFonts w:ascii="<?=$font?>" w:h-ansi="<?=$font?>" w:cs="<?=$font?>"/>
                  <wx:font wx:val="<?=$font?>"/>
                  <w:sz w:val="<?=$fontsize?>"/>
                  <w:sz-cs w:val="<?=$fontsize?>"/>
                  <w:lang w:val="EN-CA"/>
                  <w:b w:val="<?=$bold?>"/> 
                </w:rPr>
                <?php // If it's the first line of a label don't print a line break otherwise do print it ?>
                <?php echo (($lcline == 0) ? '' : '<w:br/>') . '<w:t>' . $LCcur[$lcline] . '</w:t>'."\n"; ?>
                </w:r>
<?php
			}
?>
      </w:p>
      </w:tc>
<?php
		}
    // If it is the last column close the row
		if($i == $columns){
      echo "	</w:tr>\n";
    }
    // Move on to the next label
		$now++;
	}
?>
<?php
}
?>
</w:tbl>
      <w:p wsp:rsidR="001B5590" wsp:rsidRPr="00BE2E45" wsp:rsidRDefault="001B5590" wsp:rsidP="00BE2E45">
        <w:pPr>
          <w:spacing w:line="<?=$fontSize?>0" w:line-rule="exact"/>
          <w:rPr>
            <w:rFonts w:ascii="<?=$font?>" w:h-ansi="<?=$font?>" w:cs="<?=$font?>"/>
            <wx:font wx:val="<?=$font?>"/>
            <w:sz w:val="<?=$fontsize?>"/>
            <w:sz-cs w:val="<?=$fontsize?>"/>
          </w:rPr>
        </w:pPr>
      </w:p>
      <w:sectPr wsp:rsidR="001B5590" wsp:rsidRPr="00BE2E45" wsp:rsidSect="001B5590">
        <w:pgSz w:w="<?=$PAPER_WIDTH?>" w:h="<?=$PAPER_HEIGHT?>"/>
        <w:pgMar w:top="<?=$top_mar?>" w:right="<?=$right_mar?>" w:bottom="<?=$bottom_mar?>" w:left="<?=$left_mar?>" w:header="0" w:footer="0" w:gutter="0"/>
        <w:cols w:space="0"/>
        <w:docGrid w:line-pitch="360"/>
      </w:sectPr>
    </wx:sect>
  </w:body>
</w:wordDocument>
