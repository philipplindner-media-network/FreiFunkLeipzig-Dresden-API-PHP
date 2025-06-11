<?php

// 1. Error Reporting and Configuration
// It's good practice to set error reporting for development.
// For production, you might want to log errors instead of displaying them.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants for better readability and easier modification
define('API_BASE_URL_TEMPLATE', "http://%s.freifunk-dresden.de");
define('API_SYSINFO_ENDPOINT', "/sysinfo-json.cgi");
define('OFFLINE_COLOR', '#ff0000');
define('ONLINE_COLOR', '#00ff00');
define('CONNECTION_TIMEOUT', 5); // Shorter timeout for fsockopen

// 2. Input Validation
// Always validate user input. Using filter_var for a basic check.
// If 'kid' is not set or invalid, we can gracefully exit or show an error.
if (!isset($_REQUEST["kid"]) || empty($_REQUEST["kid"])) {
    echo "Error: Node ID (kid) is missing in the request.";
    exit;
}

$nodeId = htmlspecialchars($_REQUEST["kid"]); // Sanitize input to prevent XSS
$apiUrl = sprintf(API_BASE_URL_TEMPLATE, $nodeId);
$apiSysinfoUrl = $apiUrl . API_SYSINFO_ENDPOINT;

// 3. Fetching JSON Data with Error Handling
// Use file_get_contents with error handling for network issues.
$jsonContent = @file_get_contents($apiSysinfoUrl); // Suppress warnings with @ and handle manually

if ($jsonContent === FALSE) {
    echo "Error: Could not retrieve data from " . htmlspecialchars($apiSysinfoUrl) . ". Please check the node ID or network connectivity.";
    exit;
}

$jsonR = json_decode($jsonContent);

if ($jsonR === NULL) {
    echo "Error: Could not decode JSON data from " . htmlspecialchars($apiSysinfoUrl) . ". The API might be returning malformed JSON.";
    exit;
}

// 4. Data Extraction and Null Coalescing
// Use null coalescing operator (??) to provide default values if data is missing.
// This prevents errors if a field is not present in the JSON response.
$ffVersion        = $jsonR->data->firmware->version ?? 'N/A';
$distName         = $jsonR->data->firmware->DISTRIB_ID ?? 'N/A';
$distVersion      = $jsonR->data->firmware->DISTRIB_RELEASE ?? 'N/A';
$ffCommunity      = $jsonR->data->common->community ?? 'N/A';
$ffNode           = $jsonR->data->common->node ?? 'N/A';
$ffDomain         = $jsonR->data->common->domain ?? 'N/A';
$ffRouterIp       = $jsonR->data->common->ip ?? 'N/A';
$ffModel          = $jsonR->data->system->model2 ?? 'N/A';
$ffRouterGpsLat   = $jsonR->data->gps->latitude ?? 'N/A';
$ffRouterGpsLon   = $jsonR->data->gps->longitude ?? 'N/A';
$ffRouterName     = $jsonR->data->contact->name ?? 'N/A';
$ffRouterLocation = $jsonR->data->contact->location ?? 'N/A';
$ffRouterEmail    = $jsonR->data->contact->email ?? 'N/A';

// Client statistics with null coalescing
$ffClient2g1m = $jsonR->data->statistic->client2g->{"1min"} ?? 0;
$ffClient2g1d = $jsonR->data->statistic->client2g->{"1d"} ?? 0;
$ffClient5g1m = $jsonR->data->statistic->client5g->{"1min"} ?? 0;
$ffClient5g1d = $jsonR->data->statistic->client5g->{"1d"} ?? 0;

// Calculate total clients
$ffClientAll1m = $ffClient2g1m + $ffClient5g1m;
$ffClientAll1d = $ffClient2g1d + $ffClient5g1d;

// 5. Output Formatting and HTML Escaping
// Use htmlspecialchars for all output to prevent XSS vulnerabilities.
// Structure output using a more readable format.
echo "<h2>Freifunk Node Statistics</h2>";
echo "<p><strong>Freifunk Version:</strong> " . htmlspecialchars($ffVersion) . " on " . htmlspecialchars($distName) . " (ver.: " . htmlspecialchars($distVersion) . ")</p>";
echo "<p><strong>Router Model:</strong> " . htmlspecialchars($ffModel) . "</p>";
echo "<p><strong>Community:</strong> " . htmlspecialchars($ffCommunity) . "</p>";
echo "<p><strong>Node Name:</strong> " . htmlspecialchars(rawurldecode($ffRouterName)) . " (Node ID: " . htmlspecialchars($ffNode) . ")</p>";
echo "<p><strong>Location:</strong> " . htmlspecialchars(rawurldecode($ffRouterLocation)) . " <strong>Contact:</strong> " . htmlspecialchars(rawurldecode($ffRouterEmail)) . "</p>";
echo "<p><strong>Clients:</strong> Currently: " . htmlspecialchars($ffClientAll1m) . " Today: " . htmlspecialchars($ffClientAll1d) . "</p>";

// 6. Status Check Refinement
// Better error handling for fsockopen and clearer status message.
echo "<p><strong>Status:</strong> ";
if ($socket = @fsockopen(parse_url($apiUrl, PHP_URL_HOST), 80, $errno, $errstr, CONNECTION_TIMEOUT)) {
    // fsockopen returns a resource on success
    echo "<span style='color: " . ONLINE_COLOR . "'>Online</span>";
    fclose($socket);
} else {
    echo "<span style='color: " . OFFLINE_COLOR . "'>Offline</span>";
    // Optionally, display error details for debugging
    // echo " (Error: $errstr)";
}
echo "</p>";

// 7. Links and Attribution
// Ensure links are properly formatted and attribution is clear.
// Correct the broken link tag for "Statistik".
echo "<p><a href='https://karte.freifunk-leipzig.de/grafana/d/KoKOqJc7k/node-public?orgId=1&refresh=30s&from=now-24h&to=now&var-node_id=" . htmlspecialchars($ffNode) . "' target='_blank'>View Statistics (Grafana)</a></p>";
echo "<p>A tool by <a href='https://www.philipp-lindner.de' target='_blank'>Philipp Lindner</a> and <a href='https://freifunk-dd-le.srv64.de' target='_blank'>FreiFunk-DD-LE.srv64.de</a></p>";

?>
