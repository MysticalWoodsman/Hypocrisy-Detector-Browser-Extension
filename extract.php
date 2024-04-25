<?php
// Set the appropriate Content-Type header for JSON responses
header('Content-Type: application/json');
// Allow requests from a specific origin (your Chrome extension's origin)
//header('Access-Control-Allow-Origin: chrome-extension://dkjgmllhdlpkidoadjgjeekdlegiaklo');
header('Access-Control-Allow-Origin: *');




// Report all PHP errors (see changelog)
error_reporting(E_ALL);

// Display errors on the screen
#ini_set('display_errors', 1);

// Start a new session or resume the existing one
session_start();

// Your database connection details 

	

// Create a connection
$conn = new mysqli($servername, $db_username, $pword, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user_email from cookies
$user_email = isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : null;

if ($user_email == "" or $user_email == "new_trial_user"){
echo "<center><br><br><big><big>Oops!</big><br><br>Please <a href=\"https://www.hypocrisy-detector.com/discussions/index.html\" target=\"_blank\">Click Here</a> to login in, or register for a Free Trial Account.<center><br><br>";

exit();
}


# echo ": $user_email<br>";

include_once('extractor3/simple_html_dom.php');

$input_text = $_POST['input_text'];

// Check if the input URL is from YouTube
if (strpos($input_text, 'youtube.com') !== false) {
    // If the URL is from YouTube, send "Plan 2" back to the extension
    echo "<big><big><big><big><center>Analyzing YouTube is only available in the Premium Version. <a href=\"https://hypocrisy-detector.com/index.php\" target=\"_blank\"><br><br><b>Click HERE</a> to upgrade.</b></center></big></big></big></big><br><br>";
    exit();

    #print("Plan 3");
    
} else {
    // If the URL is not from YouTube, perform the extraction process
    $output_text = file_get_html($input_text)->plaintext;
    // Assign the cleaned-up text to $output_text_value
    $output_text_value = array('output_text' => $output_text);
    // Encode the array to JSON
    $output_json = json_encode($output_text_value);

#    $ch = curl_init();
#    curl_setopt($ch, CURLOPT_URL, $url);
#    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
#    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
#    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
#    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // generally for development purposes
#    $output_json = curl_exec($ch);
#    curl_close($ch);

    // Display the JSON-encoded value with readable text
    $clean_data = str_replace(["<\/script> ", "\n"], "", $output_json);
    $clean_data = str_replace(["{\"output_text\":\"", "\"}"], "", $output_json);

    // Print the cleaned data
#    print($clean_data);
}

// Set the API key provided by OpenAI
$apiKey = "";

// Set the API endpoint URL
$apiUrl = "https://api.openai.com/v1/engines/";

// Set the HTTP headers
$headers = array(
    "Content-Type: application/json",
    "Authorization: Bearer " . $apiKey
);

// Initialize cURL session
$curl = curl_init();

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $apiUrl);
curl_setopt($curl, CURLOPT_POST, true);

// Set the prompt
// Set the prompt and max_tokens parameter
$prompt = "[redacted] $clean_data";
$maxTokens = 1000; // Example value, adjust as needed
curl_setopt($curl, CURLOPT_POSTFIELDS, '{ "prompt": "' . $prompt . '", "max_tokens": ' . $maxTokens . ' }');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

// Execute the cURL request
$response = curl_exec($curl);

// Check for cURL errors
if (curl_errno($curl)) {
    $error = curl_error($curl);
    curl_close($curl);
    echo "Curl Error: " . $error;
} else {
    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Check if response is valid
    if ($responseData && isset($responseData['choices'][0]['text'])) {
        // Output the text of the first choice
        echo $responseData['choices'][0]['text'];
			#echo ("<br><br><u><b>[ Tip this source (crypto) ]</b></u><hr>");
        // Extract the filename from the URL
        $url_parts = parse_url($input_text);
        $filename = basename($url_parts['path']);

        
    } else {
        // Write error information to a file in the "errors" directory
        $error_directory = 'errors/';
        if (!file_exists($error_directory)) {
            mkdir($error_directory, 0777, true);
        }

       // Get the email address from cookies
        $email = isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : 'Unknown';

        $error_content = "API Error: No valid response from the API.\nURL: $input_text\nUser Email: $email\n";
        file_put_contents($error_directory . "error_" . time() . ".txt", $error_content);
        
         echo '<big><big><b>Woops!</big><br><br>An error was encountered when looking at this web page.<br><br>A team of Highly Trained Monkeys and their Robot Overlords<br>have been notified.<br><br>Don\'t worry, we\'ll get the problem worked out...<br><br> (Sorry)<hr>';
         exit();
    }
}

// Close cURL session
curl_close($curl);

// Construct the Reddit content

// Encode the content for the URL
$encoded_content = urlencode($reddit_content);

// Extract the filename from the URL for use in the Reddit title
$url_parts = parse_url($input_text);
$filename = basename($url_parts['path']);


$title = str_replace(['-', '_'], ' ', $filename); // Replace all dashes and underscores with spaces
$title = ucwords($title); // Capitalize the first letter of each word
// Use urlencode to ensure it is safe to include in a URL
$encoded_title = urlencode($title);

// Generate the Reddit sharing link
#echo ("<center><big><big><b><a href=\"https://www.reddit.com/r/$subreddit/submit?selftext=true&text=$encoded_content&title=$title\" target=\"_blank\">Share on Reddit</center></a></b></big></big>");
#echo ("<center><br>If you aren't already logged in to Reddit, <b><a href=\"https://www.reddit.com\" target=\"_blank\"><br><big>Click Here!</big></a></b></center>");
$reddit_link = "<center><big><big><b><a href=\"https://www.reddit.com/r/$subreddit/submit?selftext=true&text=$encoded_content&title=$title\" target=\"_blank\">Share on Reddit</center></a></b></big></big>";


#$reddit_content = "this is stuff\n\n and more stuff";
#echo ("<a href=\"https://www.reddit.com/submit?url=[$reddit_content]&title=$filename\" target=\"_blank\">Share on Reddit</a>");

// Set the directory where you want to save the file
        $directory = 'discussions/scanned/';

        // Check if the directory exists, create it if not
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Create the HTML link
        
        $url_link = '<a href="' . $input_text . '" target="_blank">' . $input_text . '</a>';

        // Combine the URL link and the content data with HTML line breaks
        $content = $url_link . '<br><big><b>[ Join the Discussion ]</b></big><br>' . nl2br($responseData['choices'][0]['text']);

        // Save the data to the file in the specified directory
        file_put_contents($directory . "$filename", $content);

// Create a connection to the database
$conn = new mysqli($servername, $db_username, $pword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the user's email is retrieved from a session or passed via POST/GET
$email = $user_email; // Example email, replace or modify based on your actual use case

// SQL to decrement the scans_remaining for a specific user
$sql = "UPDATE [REDACTED] SET scans_remaining = scans_remaining - 1 WHERE email = ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind parameters to the prepared statement
$stmt->bind_param("s", $email);

// Execute the statement
if ($stmt->execute()) {
 #   echo "Scan count updated successfully.<br>";

    // Check the new scan count
  #  echo "Affected Rows: " . $stmt->affected_rows . "<br>";
    if ($stmt->affected_rows > 0) {
        // Query to fetch the current number of remaining scans
        $fetchSql = "SELECT scans_remaining FROM [REDACTED] WHERE email = ?";
        $fetchStmt = $conn->prepare($fetchSql);
        $fetchStmt->bind_param("s", $email);
        $fetchStmt->execute();
        $result = $fetchStmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo "<br><br><b>Scans Remaining: " . $row['scans_remaining'] . "</b><br>";
        }
        $fetchStmt->close();
    } else {
 #       echo "<br><br>No scans were deducted. This might be due to no user found with the given email.<br>";
    }
} else {
#    echo "Error updating record: " . $stmt->error . "<br>";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<table style="width: 100%; border-collapse: collapse;">
    <tr>
      <th style="width: 25%; border: 1px solid black; background-color: lightgreen; text-align: center;"><b><big><big><a href="[REDACTED]" target="_blank">Search<br>Archive</big></big></b></a></th>
      <th style="width: 50%; border: 0px solid black; background-color: yellow; text-align: center;"><center><br>If you aren't already logged in to Reddit,<b><a href="https://www.reddit.com" target="_blank"><br><big>Click Here! </big> (optional)</a></center></th>
      <th style="width: 25%; border: 1px solid black; background-color: lightgreen; text-align: center;"><?echo "$reddit_link";?></th>
    </tr>
  </table>

