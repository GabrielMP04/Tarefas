<?php
// Database Configuration
define('DB_SERVER', 'localhost'); // Your database server
define('DB_USERNAME', 'root'); // Your database username
define('DB_PASSWORD', ''); // Your database password
define('DB_NAME', 'gerenciador_tarefas'); // Your database name

// Google Sheets API Configuration
define('GOOGLE_APPLICATION_CREDENTIALS', __DIR__ . '/gerenciador-de-tarefas-461403-1476b57e46aa.json');
define('GOOGLE_SHEET_ID', 'YOUR_GOOGLE_SHEET_ID_HERE'); // <<< IMPORTANT: REPLACE WITH YOUR ACTUAL GOOGLE SHEET ID

require __DIR__ . '/vendor/autoload.php'; // Path to Composer's autoload.php

// Establish database connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to append data to Google Sheet
function appendSheetData($sheetName, $data) {
    try {
        $client = new Google_Client();
        $client->setApplicationName('Gerenciador de Tarefas');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(GOOGLE_APPLICATION_CREDENTIALS);

        $service = new Google_Service_Sheets($client);

        $spreadsheetId = GOOGLE_SHEET_ID;
        $range = $sheetName . '!A:H'; // Adjust range based on your actual columns

        // Get header row to ensure correct column order
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $header = $response->getValues()[0] ?? [];

        if (empty($header)) {
            // If no header, or sheet is empty, create a default one
            $header = ['Cliente', 'Tipo de Serviço', 'Descrição', 'Técnico', 'Previsão Término', 'Prioridade', 'Status', 'Data Registro'];
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [$header]
            ]);
            $service->spreadsheets_values->update($spreadsheetId, $range, $body, ['valueInputOption' => 'RAW']);
        }

        // Prepare values in the order of the header
        $values = [];
        foreach ($header as $col) {
            $values[] = $data[$col] ?? ''; // Use ?? '' to avoid errors if a key is missing
        }

        $body = new Google_Service_Sheets_ValueRange([
            'values' => [$values]
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $insert = [
            'insertDataOption' => 'INSERT_ROWS'
        ];

        $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params, $insert);
        return $result;

    } catch (Exception $e) {
        error_log('Google Sheets API Error: ' . $e->getMessage());
        return false;
    }
}

// Function to update data in Google Sheet (Optional, more complex for Sheets)
// This function would require retrieving all data, finding the row, updating, and writing back.
// For simplicity, this example only includes append. Update operations are usually handled by database.
?>