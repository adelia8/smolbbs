<?php
include "config.php";
date_default_timezone_set($setting_time_zone);
 print '<h1><a href="./"><<< </a></h1>';
 if (isset($_POST['mode'])){
	$mode = $_POST['mode'];
} else {
	$mode = 'view';
}
$math1 = rand(1,2);
$math2 = rand(1,2);
$mtype = rand(0,1);
if ($mtype == 0) {
	$mtype = '+';
} elseif ($mtype == 1) {
	$mtype = '-';
}	
if ($mode == 'post'){
	$text = $_POST['text'];
	$name = $_POST['name'];
	$text = strip_tags($text);

    $user_json = file_get_contents("ad.json");
	$user_list = json_decode($user_json, true);
	$user_id_list = array_column($user_list['users'], 'id');
	$user_id = '8888888888' ; 
    if (in_array($user_id, $user_id_list)){
		$user_place = array_search($user_id, $user_id_list); 
		if ($user_list['users'][$user_place]['blacklist'] == "1"){
			die("<div class='post'><h1>You're banned!</h1>You're not allowed to create new posts!</div>");
			}}
    if (preg_match('/^(.)\1*$/u ', $text)) {
		die ("<div class='post'><h1>Spam detected!</h1>Try posting something that's not spam.</div>");
	} $post_length = strlen($text);
	if ($post_length < $setting_minchar) {
		die ("<div class='post'><h1>Post too short!</h1>You wrote <b>$post_length characters</b>. The minimum is <b>$setting_minchar characters</b>.</div>");
	} if ($post_length > $setting_maxchar) {
		die ("<div class='post'><h1>Post too long!</h1>You wrote <b>$post_length characters</b>. The minimum is <b>$setting_maxchar characters</b>.</div>");
	}
if (substr_count($text, ' ') === strlen($text)) {
		die ("<div class='post'><h1>Spam detected!</h1>Post contained only spaces!</div>");
	}	
$spam_time = time() + $setting_post_wait;
if (in_array($user_id, $user_id_list)){
$user_wait = $user_list['users'][$user_place]['lastpost'] - time();
if ($user_list['users'][$user_place]['lastpost'] > time()){
die("<div class='post'><h1>Spam timer</h1>Wait $user_wait seconds before posting!</div>");
		}
	}
if ($setting_usercodes == 1){
		if (isset($_POST['trip']) && $_POST['trip'] == 1){
			$name_verify = substr(sha1($user_id), 0, 8);
		}
	}

	if (!isset($name_verify)){
		$name_verify = 0;
	}
	$math1 = $_POST['math1'];
	$math2 = $_POST['math2'];
	$mtype = $_POST['mtype'];
	$manswer = $_POST['manswer'];
    if ($mtype == '+') {
		$answer = $math1 + $math2;
	} elseif ($mtype == '-') {
		$answer = $math1 - $math2;
	}

	if ($manswer <> $answer) {
	 $spam_time = time() + $setting_wrong_penalty;
		
	}
        if (in_array($user_id, $user_id_list)){
		$user_list['users'][$user_place]['lastpost'] = $spam_time; 
		$new_user_json = json_encode($user_list, true);
		file_put_contents("ad.json", $new_user_json);
	} else {
		$new_user = array("id" => "$user_id", "lastpost" => $spam_time, "blacklist" => 0);
		array_push($user_list['users'], $new_user);
		$new_user_json = json_encode($user_list, true);
		file_put_contents("ad.json", $new_user_json);
	}

	if ($manswer <> $answer) {
		die("<div class='post'><h1>Spam timer</h1><div class='post'>Your answer to the math question was incorrect!<br>Because of this... you must wait $setting_wrong_penalty seconds.</h1></div>");
		
	}
	
	if(isset($_POST['name']) && isset($_POST['text'])) {
		$board_json = file_get_contents("el.json");
		$board_list = json_decode($board_json, true);
		$board_list['id'] = $board_list['id'] + 1;
		$new_post = array("id" => $board_list['id'], "user" => "$user_id", "name" => "$name", "verified" => "$name_verify", "time" => time(), "text" => "$text");
		array_push($board_list['posts'], $new_post);
		$new_board_json = json_encode($board_list, true);
		file_put_contents("el.json", $new_board_json);
		echo "<div class='post'><h1>Post completed!</h1><a href='index.php'>Back</a></div>";
	}
}

If ($mode == 'view'){print "<title>$setting_board_name</title><center><div class=main>";
	$mode = 0;
	print "<center><br><br><form action='index.php' method='post'><table class='newpost'>
	<tr><td>Title</td><td><input required type='text' name='name' maxlength='64' value='Anonymous'></td></tr>
	<tr><td><input type='submit' value='Send'></td><td><textarea rows='4' cols='44' name='text' maxlength='$setting_maxchar' required placeholder='Message'></textarea></td></tr>";
	print "<tr><td>Math</td><td><input required type='hidden' name='math1' maxlength='32' value='$math1'><input required  type='hidden' name='math2' maxlength='32' value='$math2'><input required type='hidden' name='mtype' maxlength='32' value='$mtype'><input required type='hidden' name='mode' maxlength='4' value='post'>";
    print "$math1 $mtype $math2" . ' <input type="text" required  name="manswer" maxlength=4 placeholder="Answer"></td></form></table></center>' . "\n \n";
     print "$setting_board_title";
	 print "<br>";
	 print "<hr>";
	 print "<hr>";
	 print "<br>";
	$board_json = file_get_contents("el.json");
	$board_posts = json_decode($board_json, true);
	if ($setting_board_flow == '1'){
		$board_posts['posts'] = array_reverse($board_posts['posts']);
	}
	$total_posts = count($board_posts['posts']);
$post_limit = 10;
if(isset($_GET['m'])) {
		$post_limit = $_GET['m'];
		if ($post_limit > 500) {
			die("<div class='post'><h1>Notice!</h1><div class='post'>Posts not shown are still archived!</div>");
		}
	}

$display_posts = array_slice($board_posts['posts'], 0, $post_limit);	
foreach ($display_posts as $display_post){
$post_id = $display_post['id'];
		$post_name = $display_post['name'];
		$post_text = $display_post['text'];
		$post_verify = $display_post['verified'];
		if($post_verify == "0"){
			$name_splash = '';
		} else {
			$verify_bg_color = substr($post_verify, 0, 6);
			$name_splash = "<span class=poster style='background-color:#$verify_bg_color;'>$post_verify</span>";
		}
		echo "<div class='post'><span class=info>#$post_id <b>$post_name</b> $name_splash : </span><br>$post_text</div> \n \n";
		echo "<br>";
	}
    print "<div class=$setting_static_div>$total_posts posts total<br><br><a href='index.php'>10</a> - <a href='index.php?m=25'>25</a> - <a href='index.php?m=50'>50</a> - <a href='index.php?m=100'>100</a> - <a href='index.php?m=250'>250</a> - <a href='index.php?m=500'>500</a></div>";
	print "</div><footer>$setting_footer_content";
}

?>
</footer>
</html>
