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
      return $this;
      
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
     *
     */
    
    public function upload()
    
    {
      
      /*
      * We need to prepare the sheetname, range, ID and credentials
      */
      
      $this -> applicationName();
      $this -> sheetName();
      $this -> updateRange();
      $this -> spreadsheetID();
      
      /*
      * Google auth by default requires GOOGLE_APPLICATION_CREDENTIALS as env variable
      */
      $this -> authConfig();
      
      /*
      * We need to get a Google_Client object first to handle auth and api calls, etc.
      */
      
      $client = new \Google_Client();
      $client -> setApplicationName('AUDIT2Spreadsheet');
      $client -> setScopes(Google_Service_Sheets::SPREADSHEETS);
      $client -> setAccessType('offline');
      $client -> setAuthConfig($this -> authConfig);
      
      $updateBody = new \Google_Service_Sheets_ValueRange([
        'values' => $this -> body,
      ]);
      /*
      * With the Google_Client we can get a Google_Service_Sheets service object to interact with sheets
      */
      $sheets = new \Google_Service_Sheets($client);
      $sheets -> spreadsheets_values -> update($this -> spreadsheetID, $this -> updateRange, $updateBody, ['valueInputOption' => 'RAW']);
      $spid = $this -> spreadsheetID;
      return $sheets;
    }
    
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
    protected function updateRange()
    {
      
      $this -> startRow();
      $this -> startCol();
      
      if (!$this -> updateRange)
      {
        
        $alphabet = range('A', 'Z');
        
        
        $end_row = count($this -> body) + $this -> startRow - 1;
        $end_col = $alphabet[count($this -> body['0'])];
        
        $start_range = $this -> startCol . $this -> startRow . ':';
        $end_range = $end_col . $end_row;
        
        $this -> updateRange = $this -> sheetName . '!' . $start_range . $end_range; //Spreadsheet range
        
      }
      
    }
    
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
    
    /**
     * @return string
     */
    function startRow()
    
    {
      if (!$this -> startRow)
      {
        $this -> startRow = '1';
      }
      
      return $this -> startRow;
    }
    
    /**
     *
     */
    protected /**
     *
     */
    function startCol()
    
    {
      if (!$this -> startCol)
      {
        $this -> startCol = 'A';
      }
      
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
    
    public $startCol;
    
    public $ApplicationName;
  }
