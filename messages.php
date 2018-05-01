<?php
/*
 * Restful API for adding messages to a cache.
 * On acess cache removes files that have been stored
 * for longer than 30 seconds.
 */

//Remove files greater than 30 seconds.
clearOldFiles(time());


//Check sever request method and complete required processes.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST["id"]) || !isset($_POST["message"])) {
        die();
    }

    addMessage($_POST["id"], $_POST["message"]);

} else if ($_SERVER["REQUEST_METHOD"] == "GET") {


    getMessage($_GET["id"]);


} else {
    //Method type not allowed.
    http_response_code(405);
    die();
}


/*
 * Function clears files that are older than 30 seconds.
 *
 */
function clearOldFiles($currentTime)
{

    $files = scandir('messagesCache/');

    foreach ($files as $file) {

        $file = "messagesCache/" . $file;

        //Only complete on files that are .txt extension.
        if (pathinfo($file, PATHINFO_EXTENSION) == 'txt') {

            $timeDifference = $currentTime - filemtime($file);

            if ($timeDifference > 30) {

                unlink($file);

            }

        }

    }

}
/*
 * Creates a new cache file with the ID as file name
 * and message as content of the file.
 *
 */
function addMessage($id, $message)
{

    $fileName = createFileName($id);

    //Write over ID if already exists.  Otherwise new file created.
    $newFile = fopen($fileName, "w");
    fwrite($newFile, $message);

}

/*
 * Returns the message associated with the parameter ID.
 * If no ID match is acquired, an error message is returned.
 * If found message/id returned in JSON format.
 */
function getMessage($id)
{

    //Check required variables exist.
    if (!isset($_GET["id"])) {
        http_response_code(400);
        die();
    } else {
        $fileName = createFileName($id);

        if (!file_exists($fileName)) {
            http_response_code(404);
            echo json_encode(["message" => "Resource not found"]);
            die();
        }


        $file = fopen($fileName, "r");

        //Retrieve first line from txt file.
        $message = fgets($file);

        //Retrun JSON.
        echo json_encode(["id" => $id, "message" => $message]);

        http_response_code(200);
        //Retrieve required document from cache.

    }
}

/*
 * Prefixes the file name with the correct folder.
 */
function createFileName($id)
{
    return $fileName = "messagesCache/" . $id . ".txt";
}