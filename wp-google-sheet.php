<?php
/**
 * Plugin Name: WP  Google Sheet
 *
 * @author            Sabbir Sam, devsabbirahmed
 * @copyright         2022- devsabbirahmed
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: WP  Google Sheet
 * Plugin URI: https://github.com/sabbirsam/Admin-Chat-Box/tree/free
 * Description: Add, view, update GS data from PHP
 * Version:           1.0.0
 * Requires at least: 5.9 or higher
 * Requires PHP:      5.4 or higher
 * Author:            SABBIRSAM
 * Author URI:        https://github.com/sabbirsam/
 * Text Domain:       acb
 * Domain Path: /languages/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 */

defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');

if (file_exists(dirname(__FILE__).'/vendor/autoload.php')) {
    require_once dirname(__FILE__).'/vendor/autoload.php';
}

use GSS\Inc\GSS_Activate;
use GSS\Inc\GSS_Deactivate;

/**
 * Main Class
 */
if(!class_exists('WP_Google_Sheet')){
    class WP_Google_Sheet{
        public function __construct(){
            $this->includes();
            add_action( 'admin_menu', array( $this, 'add_sheet_page' ) );
        }
        /**
         * Register
         */
        function register(){
            add_action("plugins_loaded", array( $this, 'gss_load' )); 
        }
        /**
         * Language load
         */
        function gss_load(){
            load_plugin_textdomain('gss', false,dirname(__FILE__)."languages");
        }
        /**
         * Classes 
         */
        public function includes() {

        }
        
        /**
         * Add error page
         */
        public function add_sheet_page() {
            add_menu_page( 'Google Sheet', 'google-sheet', 'manage_options', 'google', array( $this, 'google_sheets' ), 'dashicons-media-spreadsheet', 90 );
            add_submenu_page( 'google', 'Google Data','Google Data', 'manage_options', 'show_data', array( $this, 'display_sheet_data' ) );
        }

        /**
         * Update code if not new and add If new 
         */

        public function google_sheets() {
            $client = new \Google_Client();
            $client->setApplicationName('Google Sheets with Sam');
            $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
            $client->setAccessType('offline');
            $client->setAuthConfig(__DIR__ . '/credentials.json'); 

            $service = new Google_Service_Sheets($client);
            $spreadsheetId = "1OlnkUoR2opURoIym5GnayN4NAYYqxMFIc1w4XDmH_OU";
            //https://docs.google.com/spreadsheets/d/1OlnkUoR2opURoIym5GnayN4NAYYqxMFIc1w4XDmH_OU/edit#gid=0

            $range = "Sheet1!A1:F";

            $existingData = $service->spreadsheets_values->get($spreadsheetId, $range);

            $existingValues = $existingData->getValues();
            if (empty($existingValues)) {
                $headings = [['Date', 'Form Name', 'Name', 'Email', 'Tag', 'Location']];
                $body = new Google_Service_Sheets_ValueRange([
                    'values' => $headings
                ]);
                $params = [
                    'valueInputOption' => 'RAW'
                ];
                $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
                echo "Headings added successfully";
            } else {
                $dataExist = false;
                $existingRow = 0;
                $newValues = [['January 24, 2023', 'form-widget-name', 'sabbir', 'samiubsadfat14@gmail.com', 'EasyTag,Random,test', 'Dhaka Bangladesh']];
                foreach ($existingValues as $i => $row) {
                    if ($row[0] == $newValues[0][0] && $row[1] == $newValues[0][1] && $row[2] == $newValues[0][2] && $row[3] == $newValues[0][3] && $row[4] == $newValues[0][4] && $row[5] == $newValues[0][5]) {
                        $dataExist = true;
                        $existingRow = $i;
                        break;
                    }
                }
                if ($dataExist) {
                    //update the existing data
                    $range = "Sheet1!A" . ($existingRow + 1) . ":F" . ($existingRow + 1);
                    $body = new Google_Service_Sheets_ValueRange([
                        'values' => $newValues
                    ]);
                    $params = [
                        'valueInputOption' => 'RAW'
                    ];
                    $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
                    echo "Data updated successfully";
                } else {
                    //append the new data
                    $range = "Sheet1";
                    $body = new Google_Service_Sheets_ValueRange([
                        'values' => $newValues
                    ]);
                    $params = [
                        'valueInputOption' => 'RAW'
                    ];
                    $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
                    echo "Data inserted successfully";
                }
            }

        }

        /**
         * Data fetch
         */
        public function display_sheet_data() {
            $client = new \Google_Client();
            $client->setApplicationName('Google Sheets with Sam');
            $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
            $client->setAccessType('offline');
            $client->setAuthConfig(__DIR__ . '/credentials.json'); 
        
            $service = new Google_Service_Sheets($client);
            $spreadsheetId = "1OlnkUoR2opURoIym5GnayN4NAYYqxMFIc1w4XDmH_OU";
            $range = "Sheet1!A1:Z";
        
            $result = $service->spreadsheets_values->get($spreadsheetId, $range);
            $data = $result->getValues();
            // return $data; if retun then call using the function $data = get_data_from_google_sheet("1OlnkUoR2opURoIym5GnayN4NAYYqxMFIc1w4XDmH_OU");

            echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
            foreach ($data as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td style="padding: 8px;">' . $cell . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';

        }

        /**
         * Activation Hook
         */
        function gss_activate(){   
            GSS_Activate::gss_activate();
        }
        /**
         * Deactivation Hook
         */
        function gss_deactivate(){ 
            GSS_Deactivate::gss_deactivate(); 
        }
    }
    /**
     * Instantiate an Object Class 
     */
    $err = new WP_Google_Sheet;
    register_activation_hook (__FILE__, array( $err, 'gss_activate' ) );
    register_deactivation_hook (__FILE__, array( $err, 'gss_deactivate' ) );
}

