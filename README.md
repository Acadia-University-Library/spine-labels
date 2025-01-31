Setup:

Create an API key
https://developers.exlibrisgroup.com/alma/apis/#defining
It needs to have read permissions for "Bibs"

If using Docker, copy sample-docker-compose.yml to docker-compose.yml and edit it as necessary
- update image name
- update prod URL

Copy sample-config.php to config.php and edit it
- add your own API keys for as many libraries as you like

Replace header-bg.jpg and logo.png with your own images if you want to customize the header

Query parameters:
- You can use the 'library' param to auto select a library from the dropdown list. These should use the codes from the config file
  - eg. ?library=acadia
- You can toggle the print collection codes checkbox by setting the 'print-codes' param to true or false
  - eg. ?print-codes=true
  - if printing collection codes you can skip certain ones by providing a comma separated list in the 'skip-codes' param
    - eg. ?print-codes=true&skip-codes=CODE1,CODE2
- you can change the default font by adding the 'font' and 'fontsize' params
  - eg. ?font=arial&fontsize=16
- you can specify your label sizes by adding the 'rows', 'columns', 'height', 'width', and 'x-margin' parameters
  - eg. ?rows=8&columns=6&height=2400&top-margin=1440&left-margin=1440&right-margin=1440&bottom-margin=1440
  - note that sizes are in DXA, where one inch is 1440
- you can toggle the bold checkbox by setting the 'bold' param to true or false
  - eg. ?bold=true