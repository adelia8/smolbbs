<?php
include "config.php";
date_default_timezone_set($setting_time_zone);
if (isset($_POST['adminpass'])){
$password = $_POST['adminpass'];
} else {
	die("<div class=post><h1>No password was set.</h1></div>");
}

If ($password != $setting_admin_pass) {
	die("<div class=post><h1>Wrong password. Go away.</h1></div>");
}

if (isset($_POST['action'])){
	$action = $_POST['action'];
	if (isset($_POST['text'])){
		$text = $_POST['text'];
	} else {
		die("<div class=post><h1>Text isn't set.</h1></div>");
	}
} else {
	$action = 'view';
	print "<div class=post><h1>Warning:</h1>No action was chosen previously, or you've only opened this page.</div>";
}
if ($action == 'delpost'){
	$board_json = file_get_contents("el.json");
	$board_list = json_decode($board_json, true);
	$board_id_list = array_column($board_list['posts'], 'id');
	$post_place = array_search($text, $board_id_list); 
	$board_list['posts'][$post_place]['text'] = $setting_deleted_msg;
	$new_board_json = json_encode($board_list, true);
	file_put_contents("el.json", $new_board_json);
	die("<div class=post><h1>Deleted post #$text</h1>");
}

if ($action == 'reveal'){
	$decrypt_ip = openssl_decrypt($text, $crypt_cipher, $setting_admin_key, $crypt_options, $crypt_iv);
	die("<div class=post><h1>IP reveal</h1>User ID <b>$text</b> has IP <b>$decrypt_ip</b></div>");
}
print "<form action='admin.php' method='post'>"; 
print "<div class='post'><b>Delete post</b><br><input type='radio' name='action' value='delpost'><br> Input Post ID below</div>"; 
print "<div class='post'><b>Input: </b><input type='text' name='text' maxlength='64'><br><b>Admin pass:</b><br><input required type='password' name='adminpass' maxlength='32'><br><input type='submit' value='Submit'></div>";

?>
</html>
