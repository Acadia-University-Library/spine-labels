<?php include('configs/config.php'); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/spinelabels.css" rel="stylesheet">
    <title>Spine Labels</title>
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="js/spinelabels.js?ver=1.1"></script>
    <!-- Copy variables into JavaScript -->
    <script> var maxCodes = <?php echo $MAX_LABELS; ?>; </script>
  </head>
  <body>
    <div id="header">
      <img src="images/logo.png" alt="Site Logo">
      <h1>Barcodes to Spine Labels</h1>
    </div>
    <div id="content">
      <div id="label-controls">
        <p id="barcode_num"></p>
        <p><a class="button" href="#" id="delete-all">Delete All</a> </p>
      </div>
      <div id="enter-barcodes">
        <form name="enterbc" id="enterbc" method="post">
          <p>Scan up to <?= $MAX_LABELS ?> barcodes, or type them and press enter</p>
          <p><label>Barcode: </label></p>
          <p><input type="text" id="sbc" name="sbc" value=""> </p>
        </form>
      </div>
      <div class="clear">&nbsp;</div>
      <form id="label-form" name="labels" method="post" action="spinelabels.php" target="_blank">  
        <p><a href="#" class="button" id="sendlabels-top">Create Labels</a></p>
        <table id="label-area">
          <thead>
            <tr>
              <th>Barcode</th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            
          </tbody>
        </table>
        <label for="library">Library</label>
        <select name="library" id="library">
          <?php 
          // get library param
          $libparam = "";
          if (array_key_exists('library', $_GET))
            $libparam = $_GET['library'];

          // list each library from config file 
          // if specified in the 'library' query param, have it selected
          foreach ($LIBRARIES as $lib => $value) {
            echo '<option value="' . $lib . '"';
            if ($libparam === $lib)
              echo ' selected';
            echo '>' . $value["name"] . '</option>';
          }
          ?>
        </select>
        <br><br>
        <!-- Print collection codes checkbox -->
        <fieldset>
          <legend>Collection Codes</legend>
          <div class="float">
            <label for="print-codes">Print collection codes</label>
            <br>
            <?php 
            $print_checked = $PRINT_CODES ? 'checked' : '';
            if (isset($_GET['print_codes'])) {
              if ($_GET['print_codes'] === 'true')
                $print_checked = 'checked';
              if ($_GET['print_codes'] === 'false')
                $print_checked = '';
            }
            ?>
            <input type="checkbox" id="print-codes" name="print-codes" value="true" <?= $print_checked ?>>
          </div>
          <!-- input box for skipping collection codes -->
          <div class="float">
            <label for="skip-codes">Collection codes to skip</label>
            <br>
            <input type="text" id="skip-codes" name="skip-codes" value="<?= isset($_GET['skip-codes']) ? $_GET['skip-codes'] : $SKIP_CODES ?>">
          </div>
        </fieldset>
        <!-- input box for font -->
        <fieldset>
          <legend>Font</legend>
          <div class="clear float">
            <label for="font">Font</label>
            <br>
            <input type="text" id="font" name="font" value="<?= isset($_GET['font']) ? $_GET['font'] : $FONT ?>">
            <br>
          </div>
          <div class="float">
            <label for="fontsize">Font Size</label>
            <br>
            <input type="number" id="fontsize" name="fontsize" min="1" value="<?= isset($_GET['fontsize']) ? $_GET['fontsize'] : $FONT_SIZE ?>">
          </div>
          <!-- Use bold font checkbox -->
          <div class="float" >
            <label for="bold">Use bold font</label>
            <br>
            <?php 
            $bold_checked = $BOLD ? 'checked' : '';
            if (isset($_GET['bold'])) {
              if ($_GET['bold'] === 'true')
                $bold_checked = 'checked';
              if ($_GET['bold'] === 'false')
                $bold_checked = '';
            }
            ?>
            <input class="clear" type="checkbox" id="bold" name="bold" value="false" <?= $bold_checked ?>>
          </div>
        </fieldset>
        <!-- input boxes for rows & columns-->
        <fieldset>
          <legend>Labels</legend>
          <div class="clear float">
            <label for="rows">Rows</label>
            <br>
            <input type="number" id="rows" name="rows" min="1" value="<?= isset($_GET['rows']) ? $_GET['rows'] : $ROWS ?>">
          </div>
          <div class="float">
            <label for="columns">Columns</label>
            <br>
            <input type="number" id="columns" name="columns" min="1" value="<?= isset($_GET['columns']) ? $_GET['columns'] : $COLUMNS ?>">
          </div>
          <!-- input boxes for label height & width -->
          <div class="float">
            <label for="height">Label Height (DXA)</label>
            <br>
            <input type="number" id="height" name="height" min="1" value="<?= isset($_GET['height']) ? $_GET['height'] : $LABEL_HEIGHT ?>">
          </div>
          <div class="float">
            <label for="width">Label Width (DXA)</label>
            <br>
            <input type="number" id="width" name="width" min="1" value="<?= isset($_GET['width']) ? $_GET['width'] : $LABEL_WIDTH ?>">
          </div>
        </fieldset>
        <!-- margins -->
        <fieldset>
          <legend>Margins</legend>
          <div class="clear float">
            <label for="top-margin">Top Margin (DXA)</label>
            <br>
            <input type="number" id="top-margin" name="top-margin" min="1" value="<?= isset($_GET['top-margin']) ? $_GET['top-margin'] : $TOP_MARGIN ?>">
          </div>
          <div class="float">
            <label for="left-margin">Left Margin (DXA)</label>
            <br>
            <input type="number" id="left-margin" name="left-margin" min="1" value="<?= isset($_GET['left-margin']) ? $_GET['left-margin'] : $LEFT_MARGIN ?>">
          </div>
          <div class="float">
            <label for="right-margin">Right Margin (DXA)</label>
            <br>
            <input type="number" id="right-margin" name="right-margin" min="1" value="<?= isset($_GET['right-margin']) ? $_GET['right-margin'] : $RIGHT_MARGIN ?>">
          </div>
          <div class="float">
            <label for="bottom-margin">Bottom Margin (DXA)</label>
            <br>
            <input type="number" id="bottom-margin" name="bottom-margin" min="1" value="<?= isset($_GET['bottom-margin']) ? $_GET['bottom-margin'] : $BOTTOM_MARGIN ?>">
          </div>
        </fieldset>
        <!-- submit button -->
        <br>
        <div class="clear">
          <a href="#" class="button" id="sendlabels">Create Labels</a>
        </div>
      </form>
      <p>Documentation can be found on <a href="https://github.com/Acadia-University-Library/spine-labels">GitHub</a></p>
    </div>
  </body>
</html>
