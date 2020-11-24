<?php

  require __DIR__ . '/vendor/autoload.php';

  /**
   * Class GSpreadSheetsWrapper
   */
  class GSpreadSheets
  {

    /**
     * @param $data
     *
     * Google client is accepting the payload as an array without keys. Also the data should have no empty fields.
     * TODO: ATM we only load arrays, add support wrapper  data type eq JSON,CSV,YML and other structured formats.
     *
     *
     * @return $this
     */
    public function load($data)
    {
      $this -> prepareData($data);

    }

    /**
     * @param $data
     */
    protected function prepareData($data)
    {
      if (!isset($this -> body))
      {


        foreach ($data as $entries)

        {
          $column = [];
          foreach ($entries as $val)
          {
            if ($val == "")
            {
              $val = "null";
            }

            $column[] = $val;
          }
          $values[] = $column;
        }

        $this -> body = $values;
      }
    }

    /**
     * @return string
     */
    function applicationName()

    {
      if (!$this -> ApplicationName)
      {
        $this -> ApplicationName = 'Audit2Spreadsheets';
      }

      return $this -> ApplicationName;
    }

    /**
     *
     */
    public function sheetName()
    {
      if (!$this -> sheetName)
      {
        $this -> ahSiteGroup = getenv('AH_SITE_GROUP');
        $this -> ahSiteEnv = getenv('AH_SITE_ENVIRONMENT');
        if (!$this -> ahSiteGroup)
        {
          throw new Error('missing AH_SITE_GROUP');
        }
        if (!$this -> ahSiteEnv)
        {
          throw new Error('missing AH_SITE_ENVIRONMENT');
        }
        $this -> sheetName = $this -> ahSiteGroup . $this -> ahSiteEnv;

      }
      
      return $this -> sheetName;

    }

    /**
     *
     */
    public function Range()
    {
   
      
      $this -> startRow();
      $this -> startCol();

        $alphabet = range('A', 'Z');
  
        if ($this -> body)
        {
        $end_row = count($this -> body) + $this -> startRow - 1;
        $end_col = $alphabet[count($this -> body['0'])];

        $start_range = $this -> startCol . $this -> startRow . ':';
        $end_range = $end_col . $end_row;

        $this -> range = $this -> sheetName . '!' . $start_range . $end_range; //Spreadsheet range
  
         } elseif (!$this -> pullstartRow)
         
        {
          $this -> range = $this -> sheetName . '!A1:Z100';
          $this -> pullstartRow = "100";
        } else
        
        {
          $this -> pullendRow = $this -> pullstartRow + "99";
          $this -> range = $this -> sheetName . '!A' . $this -> pullstartRow . ':Z' . $this -> pullendRow;
  
        }
    
    }
  public $pullendRow;
    /**
     * @return mixed
     */
    public function getPullstartRow()
    {
      return $this -> pullstartRow;
    }
  
    /**
     * @param mixed $pullstartRow
     */
    public function setPullstartRow($pullstartRow)
    {
      $this -> pullstartRow = $pullstartRow;
    }
  
    /**
     * @return mixed
     */
    public function getRange()
    {
      return $this -> range;
    }
  
    /**
     * @param mixed $range
     */
    public function setRange($range)
    {
      $this -> range = $range;
    }
    
    public $pullstartRow;
    /**
     *
     */
    public function spreadsheetID()
    {
      if (!$this -> spreadsheetID)
      {

        $this -> spreadsheetID = getenv('SPREADSHEET_ID');
      }
      if (!$this -> spreadsheetID)
      {
        throw new Error('missing SPREADSHEET_ID');
      }
      return $this -> spreadsheetID;
    }

    /**
     *
     */
    public function authConfig()
    {
      if (!$this -> authConfig)
      {
        $this -> authConfig = getenv('GOOGLE_APPLICATION_CREDENTIALS');
      }
      if (!$this -> authConfig)
      {
        throw new Error('missing GOOGLE_APPLICATION_CREDENTIALS');
      }
      return $this -> authConfig;

    }

    private function loadClient()
    {
      /*
       * Google auth by default requires GOOGLE_APPLICATION_CREDENTIALS as env variable
       */
      $this -> authConfig();
      $this -> spreadsheetID();
      $this -> applicationName();
      $this -> sheetName();
      $this -> Range();
      $this -> client = new \Google_Client();
      $this -> client -> setApplicationName($this -> applicationName());
      $this -> client -> setScopes(Google_Service_Sheets::SPREADSHEETS);
      $this -> client -> setAccessType('offline');
      $this -> client -> setAuthConfig($this -> authConfig);

    }

    /**
     * @return string
     */
    function startRow()

    {
      if (!isset($this -> startRow))
      {
        $this -> startRow = '1';
      }

      return $this -> startRow;
    }

    /**
     *
     */
    protected function startCol()

    {
      if (!$this -> startCol)
      {
        $this -> startCol = 'A';
      }

    }

    /**
     * @return mixed
     */

    public function getAuthConfig()
    {
      return $this -> authConfig;
    }

    /**
     * @param mixed $authConfig
     */

    public function setAuthConfig($authConfig)
    {
      $this -> authConfig = $authConfig;
    }

    /**
     * @return mixed
     */

    public function getahSiteEnv()
    {
      return $this -> ahSiteEnv;
    }

    /**
     * @return mixed
     */

    public function getSheetName()
    {
      return $this -> sheetName;
    }

    /**
     * @param mixed $sheetName
     */

    public function setSheetName($sheetName)
    {
      $this -> sheetName = $sheetName;
    }

    /**
     * @return mixed
     */
    public function getAhSiteGroup()
    {
      return $this -> ahSiteGroup;
    }

    /**
     * @param mixed $ahSiteGroup
     */

    public function setAhSiteGroup($ahSiteGroup)
    {
      $this -> ahSiteGroup = $ahSiteGroup;
    }
    /**
     * upload will be deprecated in favor of push
     */

    public function upload()

    {
      return $this->push();
    }

    public function pull()

    {
      $this -> loadClient();
      $sheets = new \Google_Service_Sheets($this->client);

      /*
       * To read data from a sheet we need the spreadsheet ID and the range of data we want to retrieve.
       * Range is defined using A1 notation, see https://developers.google.com/sheets/api/guides/concepts#a1_notation
       */
      
      $rows = $sheets->spreadsheets_values->get($this -> spreadsheetID, $this ->range, ['majorDimension' => 'ROWS']);
      return $rows;
    }
    /**
     *
     */

    public function push()

    {

      /*
      * We need to get a Google_Client object first to handle auth and api calls, etc.
      */
      $this -> loadClient();

      $updateBody = new \Google_Service_Sheets_ValueRange([
        'values' => $this -> body,
      ]);


      /*
      * With the Google_Client we can get a Google_Service_Sheets service object to interact with sheets
      */
      $sheets = new \Google_Service_Sheets($this->client);
      $sheets -> spreadsheets_values -> update($this -> spreadsheetID, $this -> range, $updateBody, ['valueInputOption' => 'RAW']);
      print_r(array(count($updateBody)=>$this->range));
      return $sheets;
      
    }

    /**
     * @return mixed
     */
    public function getStartrow()
    {
      return $this -> startrow;
    }

    /**
     * @param mixed $startrow
     */
    public function setStartrow($startrow)
    {
      $this -> startrow = $startrow;
    }

    /**
     * @return mixed
     */
    public function getStartCol()
    {
      return $this -> startCol;
    }

    /**
     * @param mixed $startCol
     */
    public function setStartCol($startCol)
    {
      $this -> startCol = $startCol;
    }

    /**
     * @return mixed
     */
    public function getApplicationName()
    {
      return $this -> ApplicationName;
    }

    /**
     * @param mixed $ApplicationName
     *
     * @return GSpreadSheets
     */
    public function setApplicationName($ApplicationName)
    {
      $this -> ApplicationName = $ApplicationName;
      return $this;
    }

    /**
     * @return mixed
     */
    public function getSpreadsheetID()
    {
      return $this -> spreadsheetID;
    }

    /**
     * @param mixed $spreadsheetID
     */
    public function setSpreadsheetID($spreadsheetID)
    {
      $this -> spreadsheetID = $spreadsheetID;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
      return $this -> client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
      $this -> client = $client;
    }

    /**
     * @var
     */
    public $sheetName;

    /**
     * @var
     */
    public $ahSiteGroup;

    /**
     * @var
     */
    public $ahSiteEnv;

    /**
     * @var
     */
    public $authConfig;

    /**
     *
     */
    public $startrow;

    /**
     * @var
     */
    public $startCol;

    /**
     * @var
     */
    public $spreadsheetID;

    /**
     * @var
     */
    public $ApplicationName;
    
    public $range;
    
    protected $client;
    
    public $body;
    
  }
