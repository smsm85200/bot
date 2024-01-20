<?php
// Set the timezone to Asia/Baghdad
date_default_timezone_set("Asia/Baghdad");

// Check if madeline.php file exists, if not, download it
if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}

// Include necessary files
include 'madeline.php';

// Define constants
define('MADELINE_BRANCH', 'deprecated');

// Function to make API requests to Telegram
function bot($method, $datas = [])
{
    $token = file_get_contents("token");
    $url = "https://api.telegram.org/bot$token/" . $method;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $res = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($res, true);
}

// Configure MadelineProto settings
$settings = (new \danog\MadelineProto\Settings\AppInfo)
    ->setApiId(13167118)
    ->setApiHash('6927e2eb3bfcd393358f0996811441fd');

// Create an instance of MadelineProto
$MadelineProto = new \danog\MadelineProto\API('2.madeline', $settings);

// Start MadelineProto
$MadelineProto->start();

// Main loop
$x = 0;
do {
    // Read info from JSON file
    $info = json_decode(file_get_contents('info.json'), true);
    $info["loop2"] = $x;
    file_put_contents('info.json', json_encode($info));

    // Read users from file
    $users = explode("\n", file_get_contents("users2"));

    // Loop through users
    foreach ($users as $user) {
        $kd = strlen($user);

        if ($user != "") {
            try {
                // Try to get peer dialogs
                $MadelineProto->messages->getPeerDialogs(['peers' => [['_' => 'inputDialogPeer', 'peer' => $user]]]);
                $x++;
            } catch (\danog\MadelineProto\Exception | \danog\MadelineProto\RPCErrorException $e) {
                try {
                    // Update username if exception occurs
                    $MadelineProto->account->updateUsername(['username' => $user]);
                    // Send video and caption on success
                    bot('sendvideo', [
                        'chat_id' => file_get_contents("ID"),
                        'video' => "https://t.me/aaxxxa/158",
                        'caption' => "⌯ 𝚆𝚎 𝙰𝚛𝚎 𝚃𝚑𝚎 𝙱𝚎𝚜𝚝 \n⌯ 𝙳𝚘𝚗𝚎 𝚄𝚜𝚎𝚛 ❲ @$user ❳\n⌯ 𝙲𝚕𝚒𝚌𝚔𝚜  ❲  $x ❳\n⌯ 𝚜𝚊𝚟𝚎  [ 𝙰𝚌𝚌𝚘𝚞𝚗𝚝 ↬ 2] \n⌯ 𝚃𝚑𝚎 𝙺𝚒𝚗𝚐 : @u0uu0 ↬ @i_m_q ."
                    ]);
                    // Remove the processed user from the list
                    $data = str_replace("\n" . $user, "", file_get_contents("users2"));
                    file_put_contents("users2", $data);
                } catch (Exception $e) {
                    // Handle different exceptions
                    echo $e->getMessage();

                    if ($e->getMessage() == "USERNAME_INVALID") {
                        bot('sendMessage', [
                            'chat_id' => file_get_contents("ID"),
                            'text' => "╭ checker ❲ 2 ❳\n | username is Band\n╰ Done Delet on list ↣ @$user",
                        ]);
                        $data = str_replace("\n" . $user, "", file_get_contents("users2"));
                        file_put_contents("users2", $data);
                    } elseif ($e->getMessage() == "This peer is not present in the internal peer database") {
                        $MadelineProto->account->updateUsername(['username' => $user]);
                    } elseif ($e->getMessage() == "USERNAME_OCCUPIED") {
                        bot('sendMessage', [
                            'chat_id' => file_get_contents("ID"),
                            'text' => "╭ checker ❲ 2 ❳ 🛎 \n | username not save\n╰ FLood 2500 ↣ @$user",
                        ]);
                        $data = str_replace("\n" . $user, "", file_get_contents("users2"));
                        file_put_contents("users2", $data);
                    } else {
                        bot('sendMessage', [
                            'chat_id' => file_get_contents("ID"),
                            'text' => "2 • Error @$user " . $e->getMessage()
                        ]);
                    }
                }
            }
        }
    }
} while (1);
?>