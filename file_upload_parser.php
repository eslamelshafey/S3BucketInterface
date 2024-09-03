<?php

use Aws\S3\Exception\S3Exception;

require "app.php";

if(isset($_SESSION['login']) && $_SESSION['login'] == $config['pass']) {

    if(!isset($_FILES) || empty($_FILES)) return;

    $files = $_FILES['file'];
    // for($i = 0;$i < count($files['name']);$i++) {

        $fileName = $_REQUEST['file']; // The file name
        $fileTmpLoc = $files["tmp_name"]; // File in the PHP tmp folder
        $fileType = $files["type"]; // The type of file it is
        $fileSize = $files["size"]; // File size in bytes
        $fileErrorMsg = $files["error"]; // 0 for false... and 1 for true
        
        if (!$fileTmpLoc) { // if file not chosen
            echo "ERROR: Please browse for a file before clicking the upload button.";
            exit();
        }

        $tmp_file_path = "uploads/$fileName";

        $dir = str_replace($files['name'], '', $tmp_file_path);

        if(!file_exists($dir)) {
            mkdir($dir);
        }

        // $x = false;
        // if((strpos($fileType, 'video') > -1) || (strpos($fileType, 'application') > -1 && in_array(strtolower(explode('.', $fileName)[1]), ['mp4', 'mkv', 'flv', 'wmv', 'mkv', 'avi']))) { // video file types only 
            // $x = explode('.', $fileName)[0].".svg";
            // copy('download.svg', "uploads/$x");
        // }

        if(move_uploaded_file($fileTmpLoc, $tmp_file_path)){
            
            try{
                $s3->putObject([
                    'Bucket'=>$config['s3']['bucket'],
                    'Key'=>"download/$fileName",
                    'Body'=>fopen($tmp_file_path, 'rb'),
                    'ACL'=>'public-read',
                ]);

                // if($x) {
                //     $s3->putObject([
                //         'Bucket'=>$config['s3']['bucket'],
                //         'Key'=>"download/$x",
                //         'Body'=>fopen('uploads/'.$x, 'rb'),
                //         'ACL'=>'public-read',
                //     ]);
                // }

                unlink($tmp_file_path);
            } catch(S3Exception $e) {
                echo $e->getMessage();
            }

        } else {
            echo "move_uploaded_file function failed";
        }

    // }
    // rmdir($dir);

}

?>