<?php
////////////////////////////////////////////////////////////////////////////////
//
// Captured PHP Upload Script
//
// This is a simple script that can provide a target for Captured App to upload
// to any webserver that supports PHP. For more details or to get Captured
// visit:
//
// http://www.capturedapp.com/
//
////////////////////////////////////////////////////////////////////////////////
//
// Configuration Options
//
// The folling options can either be set with the corresponding envioment
// variables or by manually changing the options in the script below.
//
// To set the envioment variables in a `.htaccess` file you can do the following:
//
// SetEnv CAPTURED_TOKEN change_this_token
// SetEnv CAPTURED_UPLOAD_DIR /home/user/website.com/upload-dir/
//
////////////////////////////////////////////////////////////////////////////////
//
// API Token used to authenticate requests. Do not use the default.
//
$API_TOKEN=$_ENV["CAPTURED_API_TOKEN"] ?: "change_this_token";
//
// Upload Directory. Needs to be writable by PHP on this server. Does not have
// to be publically served, since this script will proxy images back.
//
$UPLOAD_DIR = $_ENV["CAPTURED_UPLOAD_DIR"] ?: "./uploads/";
//
////////////////////////////////////////////////////////////////////////////////

function jecho($arr) {
  echo json_encode($arr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function join_paths() {
  $paths = array();
  foreach (func_get_args() as $arg) {
    if ($arg !== '') { $paths[] = $arg; }
  }
  return preg_replace('#/+#','/',join('/', $paths));
}

function randomish($length = 8) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $string = '';

  for ($i = 0; $i < $length; $i++) {
    $string .= $characters[mt_rand(0, strlen($characters) - 1)];
  }
  return $string;
}

function enforce_token() {
  global $API_TOKEN;
  if($_POST['token'] != $API_TOKEN) {
    http_response_code(401);
    jecho(['status' => "401 Invalid Token"]);
    exit();
  }
}

function render_file() {
  global $UPLOAD_DIR;
  $file = join_paths($UPLOAD_DIR, basename($_GET['i']));
  if (file_exists($file)) {
    header("Content-Type: image/" . pathinfo($file, PATHINFO_EXTENSION));
    readfile($file);
    exit();
  } else {
    http_response_code(404);
    jecho(['status' => "404 File Not Found"]);
  }
}

function process_test_connection() {
  if($_POST['test'] == "true") {
    http_response_code(202);
    jecho(['status' => "202 Accepted"]);
    exit();
  }
}

function process_file_upload() {
  global $UPLOAD_DIR;

  if(!is_dir($UPLOAD_DIR)) { mkdir($UPLOAD_DIR); }

  header('Content-Type: application/json');

  $protocol= (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
  $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
  $upload_name = randomish() . '.' . $ext;
  $upload_file = join_paths($UPLOAD_DIR, $upload_name);
  $public_url = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "/?i=" . $upload_name;


  if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_file)) {
    jecho(["public_url" => $public_url]);
  } else {
    jecho(['error' => "Unable to process file upload"]);
  }

}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  enforce_token();
  process_test_connection();
  process_file_upload();
} else {
  render_file();
}

?>
