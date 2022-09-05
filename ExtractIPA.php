<?php

////////////////// IPA Reader //////////////////

function ExtractIPA($IPA_PATH) {
  $PATH = pathinfo(realpath($IPA_PATH), PATHINFO_DIRNAME);
  $IPA_PATH = __DIR__ . "/" . $IPA_PATH;
  // lets get info from ipa
  if (file_exists($IPA_PATH)) {
      $RANDOM = rand(14565,654000);
      if (mkdir("$PATH/tmp/$RANDOM", 0777, true)) {
          // ok
          $zip = new ZipArchive;
          $res = $zip->open($IPA_PATH);
          if ($res === TRUE) {
              $zip->extractTo("$PATH/tmp/$RANDOM");
              $zip->close();
              if (file_exists("$PATH/tmp/$RANDOM/Payload")) {
                  // ok the payload is in
                  $DOT_APP_FOLDER = glob("$PATH/tmp/$RANDOM/Payload/*.app")[0];
                  $PLIST_FILE = "$DOT_APP_FOLDER/Info.plist";
                  if (file_exists($PLIST_FILE)) {
                      // ok
                      $AppName     = PlistBuddy($PLIST_FILE, "CFBundleDisplayName")[0] ?? PlistBuddy($PLIST_FILE, "CFBundleName")[0];
                      $AppBundleID = PlistBuddy($PLIST_FILE, "CFBundleIdentifier")[0];
                      $AppVersion  = PlistBuddy($PLIST_FILE, "CFBundleShortVersionString")[0];
                      $AppIcon     = basename(array_reverse(glob("$DOT_APP_FOLDER/AppIcon*.png"))[0]);
                      
                      if (empty($AppIcon)) {
                          $AppIcon = array_pop(array_reverse(PlistBuddy($PLIST_FILE, "CFBundleIcons~ipad")['dict']['array']['string']));
                          if (strpos($AppIcon, '.png') !== false) {
                              $AppIcon = $AppIcon;
                          } else {
                              $AppIcon = basename(array_reverse(glob("$DOT_APP_FOLDER/$AppIcon*.png"))[0]);
                          }
                      } else {
                          $AppIcon = $AppIcon;
                      }

                      if (file_exists("$DOT_APP_FOLDER/$AppIcon")) {
                          $IMG_CONTENT = base64_encode(file_get_contents("$DOT_APP_FOLDER/$AppIcon"));
                          exec("rm -rf $PATH/tmp/$RANDOM");
                          return [
                              "name"         => $AppName,
                              "bundle_id"    => $AppBundleID,
                              "version"      => $AppVersion,
                              "icon_content" => $IMG_CONTENT
                          ];
                      } else {
                          return ["status" => 0, "msg" => "Can't get image file of IPA file."];
                      }

                  } else {
                      return ["status" => 0, "msg" => "Can't find plist file of IPA file."];
                  }
              } else {
                  return ["status" => 0, "msg" => "Can't find payload of IPA file."];
              }
          } else {
              return ["status" => 0, "msg" => "Can't extract the IPA file."];
          }
      } else {
          return ["status" => 0, "msg" => "Can't create extracting folder"];
      }
  } else {
      return ["status" => 0, "msg" => "Can't find the IPA file."];
  }
}

function PlistBuddy($string, $key, $type = "text") {
  $plist = simplexml_load_file($string);
  $query = '/plist/dict/key['.$type.'()="'.$key.'"]/following-sibling::*[1]';       
  return $results = json_decode(json_encode((array)$plist->xpath($query)), TRUE)[0];
}

////////////////// IPA Reader end //////////////////
