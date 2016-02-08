# Captured PHP Upload Script
This is a simple script that can provide a target for [Captured App](http://www.capturedapp.com/) to upload to any webserver that supports PHP.

### Installation

1. Download and modify `captured.php`'s configuration options, setting the upload directory and api token.
1. Upload the `captured.php` file to your host, to a directory configured to serve PHP scripts.
1. Configure Captured.app with the URL to the script and the API Token you created in step 1.

If you have `mod_env` avaliable on your host, you might want to pass the token to the script via an ENV variable. This might be done via the `.htaccess` file:

```
SetEnv CAPTURED_TOKEN change_this_token
SetEnv CAPTURED_UPLOAD_DIR /home/user/website.com/upload-dir/
```

### Usage

This script is indtended to be used with Captured App's "Captured PHP" uploader.

For testing purposes you can use `curl` to test the script directly:

```
curl -i -X POST \
  -F "token=change_this_token" \
  -F "file=@path/to/test.jpg" \
   http://example.com/captured.php
```
