# Google Spreadsheets Helper 

Inspired from https://www.fillup.io/post/read-and-write-google-sheets-from-php/
The above tutorial is mostly for reading spreadsheets, our tool is dedicated to writing spreadsheets. 

It can be used as an example to setup your own scripts or you can use thisone to save your time.

By default Google uses Oauth, we want our automatic ways to write to Spreadsheets, you will find below the way to setup and have it work.

## Prerequisites
You need the following prerequisites:

- PHP 5.4 or greater with the command-line interface (CLI) and JSON extension installed
- [Composer dependency management tool](https://getcomposer.org/download/)
- [Google account](https://myaccount.google.com/) in order to enable Google Spreadsheets API
- [Spreadsheet ID](https://developers.google.com/sheets/api/guides/concepts#sheet_id) of the spreadsheet you intend to write to

## Setup

- Create project on https://console.developers.google.com/apis/dashboard.
- Click Enable APIs and enable the Google Sheets API
- Go to Credentials, then click Create credentials, and select Service account key
- Choose New service account in the drop down. Give the account a name, anything is fine.
- For Role I selected Project -> Service Account Actor
- For Key type, choose JSON (the default) and download the file. This file contains a *private key* so be very careful with it, it is your credentials after all
- Finally, edit the sharing permissions for the spreadsheet you want to access and share *EDIT* access to the 'client_email' address you can find in the JSON file.
- Git clone this class.
- Run "composer update"
- Include *GSpreadSheets.php* in your own PHP script.
- See the below tutorial
- Have FUN!
 
## Usage 

Once you have your data array ready, you can use the above code to upload it:
The array is expected to be 1 level deep, each rown must have the same amount of values. 

#### Example of expected input:

````

    // example of header if you need to write it
     $data[] = [
        'Account',
        'Owner',
        'Domain',
        'Expiry',
      ];

      //example of row 1
      $data[] = [
        'Account-D8SUD80',
        'John Doe',
        'domain.com',
        '02/03/2020',
        ];

      //example of row 2
      $data[] = [
        'Account-DYJSU90',
        'Dan Silvain',
        'domain.co.uk',
        '02/03/2020',
      ];
````

#### Example of code to use this Class with.

````
    //intialise
    $upload = new \GSpreadSheets();

    //your Spreadsheet ID
    $upload -> setSpreadsheetID('your_spreadsheet_id');

    //your Sheet name you are writing too.
    $upload -> setSheetName('your_sheet_name');

    //Path to your Credentials
    $upload -> setAuthConfig('GAcredentials-some-hash.json');

    //if you don't want to write on A1 but starting B2
    $upload ->setStartCol('B');
    $upload ->setStartrow('2');

    //The loader will: 
    //  - Empty your keys if any 
    //  - If any empty fields it will fill in with "null" to avoid API errors.
    $upload -> load($data);

    //upload itself ;)
    $upload -> upload();
````

Please report any bugs that you encounter or help improving this ;)

Many thanks,

Ciprian.





