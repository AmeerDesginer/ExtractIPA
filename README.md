# ExtractIPA
This is a full native PHP code to get info of the .IPA file

# How to use it

just include the file that contain the function and call it by


$ipa = ExtractIPA("HERE_YOUR_IPA_PATH");

// to get name
$name = $ipa['name'];

// to get bundle id
$bundle_id = $ipa['bundle_id'];

// to get version
$version = $ipa['version'];

// to get img (I convert it to base64 to make it easy to use)
$icon_content = $ipa['icon_content'];

You can easily to decode the base64 by base64_decode($icon_content);
Then you can do what you want with the content!

I hope this help you!
