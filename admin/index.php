<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//phpinfo();

// Get token from cookie
$token = $_COOKIE['token'];

// Prepare the GraphQL query for token validation
$validationQuery = <<<'GRAPHQL'
query ValidateToken {
  validateToken {
    isValid
    user {
      id
      email
    }
  }
}
GRAPHQL;

// Set up the cURL request for token validation
$validationCh = curl_init('http://laravel.test/graphql');
curl_setopt($validationCh, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
]);
curl_setopt($validationCh, CURLOPT_POST, true);
curl_setopt($validationCh, CURLOPT_POSTFIELDS, json_encode(['query' => $validationQuery]));
curl_setopt($validationCh, CURLOPT_RETURNTRANSFER, true);

// Execute the request and decode the response
$validationResponse = curl_exec($validationCh);
$validationHttpCode = curl_getinfo($validationCh, CURLINFO_HTTP_CODE);
curl_close($validationCh);

// Convert the JSON response to an array
$validationData = json_decode($validationResponse, true);

// Check for errors or invalid token in the GraphQL response
if ($validationHttpCode != 200 || !isset($validationData['data']['validateToken']) || !$validationData['data']['validateToken']['isValid']) {
    // If the token is not valid or there was an error, redirect to login
    header('Location: http://localhost/login');
    exit;
}

// Token is valid, continue with your GraphQL query to fetch user data


// The GraphQL endpoint from your Laravel application
$graphqlEndpoint = 'http://laravel.test/graphql';

// The GraphQL query
$query = <<<'GRAPHQL'
query GetUser($id: ID!) {
  user(id: $id) {
    id
    email
    name
    website{
        name
        url
        
    }
  }
}
GRAPHQL;

// The variables for your query
$variables = [
    'id' => 1,
];

// Set up the cURL request
$ch = curl_init($graphqlEndpoint);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query, 'variables' => $variables]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request and decode the response
$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    // If cURL returned an error
    echo 'cURL error: ' . curl_error($ch);
    exit; // Stop the script
}

curl_close($ch);

// Check the response status code
if ($http_status != 200) {
    echo "HTTP error code: $http_status";
    exit; // Stop the script
}

// Convert the JSON response to an array
$data = json_decode($response, true);

// Check for JSON decoding error
if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'JSON decoding error: ' . json_last_error_msg();
    exit; // Stop the script
}

// Check for errors in the GraphQL response
if (isset($data['errors'])) {
    // Handle GraphQL errors
    print_r($data['errors']);
    exit; // Stop the script
}

// Check if the 'data' key is set and not null
if (!isset($data['data']) || $data['data'] === null) {
    echo "'data' key is not set or is null";
    exit; // Stop the script
}

// Use the user data
if (isset($data['data']['user'])) {
    print_r($data['data']['user']);
} else {
    echo "'user' key is not set in the data";
}

?>

<html>
    <head>
        <title>GraphQL Client</title>
    </head>
    <body>
        <h1>GraphQL Client</h1>
        <p>Response:</p>
        <pre><?php echo $response; ?></pre>
    </body>
</html>
