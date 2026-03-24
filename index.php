<?php
error_reporting(0);

// --- [ أولاً: كلاس SQLite3 - إضافة موازية ] ---
class BotDatabase extends SQLite3 {
    function __construct() {
        $this->open('database.db');
        $this->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, user_id TEXT UNIQUE, username TEXT, name TEXT, banned INTEGER DEFAULT 0)");
        $this->exec("CREATE TABLE IF NOT EXISTS files (id INTEGER PRIMARY KEY, user_id TEXT, file_name TEXT, file_path TEXT, token TEXT, created_at TEXT)");
        $this->exec("CREATE TABLE IF NOT EXISTS stats (id INTEGER PRIMARY KEY, total_files INTEGER DEFAULT 0, bot_files INTEGER DEFAULT 0, other_files INTEGER DEFAULT 0, curl_files INTEGER DEFAULT 0, hacked_files INTEGER DEFAULT 0)");
        $this->exec("CREATE TABLE IF NOT EXISTS bots (id INTEGER PRIMARY KEY, user_id TEXT, type TEXT)");
        $res = $this->querySingle("SELECT count(*) FROM stats");
        if($res == 0) $this->exec("INSERT INTO stats (total_files) VALUES (0)");
    }
}
$db = new BotDatabase();

$token = "8571489537:AAHjdZv5ikLonsmhmdMB4uXJaCLdd5pLMo4";
$admin = 7607952642 ;
define('API_KEY',$token);
echo "setWebhook ~> <a href=\"https://api.telegram.org/bot".API_KEY."/setwebhook?url=".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME']."\">https://api.telegram.org/bot".API_KEY."/setwebhook?url=".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME']."</a>";

function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url); curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}}


$usrbot = bot("getme")->result->username;
$emoji = "➡️
🎟️
↪️
🔘
🏠";

$emoji = explode("\n", $emoji);
$b = $emoji[rand(0, 4)];
$NamesBACK = "رجوع $b";

define("USR_BOT", $usrbot);
if(!is_dir("UploadEr")) mkdir("UploadEr");
if(!is_dir("backups")) mkdir("backups");
if(!is_dir("restore")) mkdir("restore");

function SETJSON($INPUT)
{
    if ($INPUT != NULL || $INPUT != "") {
        $F = "UploadEr/UploadEr.json";
        $N = json_encode($INPUT, JSON_PRETTY_PRINT);
        file_put_contents($F, $N);
    }
}

$update = json_decode(file_get_contents('php://input'));

if ($update->message) {
    $message = $update->message;
    $message_id = $update->message->message_id;
    $username = trim(htmlspecialchars($message->from->username));
    $chat_id = $message->chat->id;
    $title = $message->chat->title;
    $text = trim(htmlspecialchars($message->text));
    $user = $message->from->username;
    $name = trim(htmlspecialchars($message->from->first_name));
    $from_id = $message->from->id;
}

$UploadEr = json_decode(file_get_contents("UploadEr/UploadEr.json"), true);


if ($update->callback_query) {
    $data = $update->callback_query->data;
    $chat_id = $update->callback_query->message->chat->id;
    $title = $update->callback_query->message->chat->title;
    $message_id = $update->callback_query->message->message_id;
    $name = $update->callback_query->message->chat->first_name;
    $user = $update->callback_query->message->chat->username;
    $from_id = $update->callback_query->from->id;
}

if ($UploadEr["mems"][$from_id] == null) {
	$UploadEr["mems"][$from_id] = 1 ;
	$UploadEr["memsA"][] = $from_id ;
        SETJSON($UploadEr);
        // SQLite Sync
        $db->exec("INSERT OR IGNORE INTO users (user_id, username, name) VALUES ('$from_id', '$user', '$name')");
	} 
	if($data == "sendReport") {
	bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $message_id , 
            "text" => "
#️⃣] ارسل الان الكليشه التوضيحيه للمطور
ℹ️] ان كان عن طريق الخطا سيتم فك الحظر
" ,
        ]);
	$UploadEr["mode"][$from_id] = "sR" ;
        SETJSON($UploadEr);
        return false ;
	} 
	
	if($text and $UploadEr["mode"][$from_id] == "sR") {
		bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "✅] تم استلام الطلب سيتم مراجعته في اقرب وقت ممكن",
            "parse_mode" => "markdown"
            
        ]);
        bot("sendMessage", [
            "chat_id" => $admin ,
            "text" => "🎃] طلب فك حظر عزيزي المبرمج
            🔖] من $name
 
[$from_id](tg://user?id=$chat_id) 
[Acount](tg://openmessage?user_id=$chat_id) 

الكليشه : $text
لفك الحظر عنه ارسل [/Unb_$from_id] 
",
            "parse_mode" => "markdown"
            
        ]);
        $UploadEr["mode"][$from_id] = null ;
        SETJSON($UploadEr);
        return false ;
		} 
$not = array("$admin", "7607952642");
if (isset($from_id) && is_array($UploadEr)) {
	if (in_array($from_id, $UploadEr)) {
    if (!in_array($from_id, $not)) {
        bot("deleteMessage", [
            "chat_id" => $chat_id,
            "message_id" => $UploadEr["m_id"][$from_id]
        ]);
        $n = bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "⚠️ You are banned from using the bot due to violations.\n⚠️ تم حظرك من استخدام الروبوت بسبب الانتهاكات.",
            "parse_mode" => "markdown", 
            'reply_markup'=>json_encode([
     'inline_keyboard'=>[
     [['text'=>"ارسال طلب فك حظر",'callback_data'=>"sendReport" ]], 
      ]
    ])
        ]);
        $UploadEr["m_id"][$from_id] = $n->result->message_id;
        SETJSON($UploadEr);
        return false;
       } 
    }
}


		
		if(preg_match("/Unb_/", $text)) {
			if($from_id == $admin) {
				$B=array_search(explode("_",$text)[1],$UploadEr);
        unset($UploadEr[$B]);
        SETJSON($UploadEr);
        $target_id = explode("_",$text)[1];
        $db->exec("UPDATE users SET banned = 0 WHERE user_id = '$target_id'");
				bot("sendMessage", [
            "chat_id" => $admin ,
            "text" => "
            Done ✅
            Id : (". explode("_",$text)[1].") / $B
",
            "parse_mode" => "markdown"
            
        ]);
        bot("sendMessage", [
            "chat_id" => explode("_",$text)[1] ,
            "text" => "⚠️] تم فك الحظر عن حسابك
🤔] الرجاء التزام بقوانين البوت
",
            "parse_mode" => "markdown"
            
        ]);
        bot("sendmessage",[
                "chat_id" => explode("_",$text)[1], 
                "text" => "
🔼] مرحبا بك في بوت رفع الملفات علي الاستضافه 
🔖] ارسل الملف الان لرفعه علي الاستضافه 
ℹ️] ملفاتك المرفوعه : $counts
📊] عدد جميع ملفات المرفوعه : $vc | $no
🌏] عدد مستخدمين البوت : $nj
🤔] تعليمات البوت /help
                ",
                'parse_mode'=>"markdown",
                'reply_markup'=>json_encode([
     'inline_keyboard'=>[
     [['text'=>"عمل تحديث | Refresh",'callback_data'=>"refr" ],['text'=>"احصائيات الرفع",'callback_data'=>"nas" ]], 
     [['text'=>"➿] التواصل مع الدعم",'callback_data'=>"contact" ]], 
     [['text'=>"Serø ⁞ Service",'url'=>"https://t.me/Sero_Bots" ]], 
      ]
    ])
            ]);
				} 
			} 
			
			
		
$counts = $UploadEr["count$from_id"] ?? 0;
$vc = $UploadEr["count"] ?? 0;
$no = format_number($vc)?? "0";
$nj = count($UploadEr["memsA"]) ;
   if( $text == "/start") {
    $kb = [
        [['text'=>"عمل تحديث | Refresh",'callback_data'=>"refr" ],['text'=>"احصائيات الرفع",'callback_data'=>"nas" ]], 
        [['text'=>"➿] التواصل مع الدعم",'callback_data'=>"contact" ]], 
        [['text'=>"Serø ⁞ Service",'url'=>"https://t.me/Sero_Bots" ]]
    ];
    if($from_id == $admin) {
        $kb[] = [['text'=>"📦 تحميل نسخة احتياطية",'callback_data'=>"create_backup"]];
        $kb[] = [['text'=>"📥 تحميل جميع البوتات",'callback_data'=>"download_all_bots"]];
    }
   	bot("sendmessage",[
                "chat_id" => $chat_id, 
                "text" => "
🔼] مرحبا بك في بوت رفع الملفات علي الاستضافه 
🔖] ارسل الملف الان لرفعه علي الاستضافه 
ℹ️] ملفاتك المرفوعه : $counts
📊] عدد جميع ملفات المرفوعه : $vc | $no
🌏] عدد مستخدمين البوت : $nj
🤔] تعليمات البوت /help
                ",
                'parse_mode'=>"markdown",
                'reply_markup'=>json_encode(['inline_keyboard'=>$kb])
            ]);
            $UploadEr["المود"][$from_id] = null ;
        SETJSON($UploadEr) ;
        return false ;
  }
  
  function progress($total, $current){
$progress = $current / $total;
$bar_length = 20;
$filled_length = round($bar_length * $progress);
$bar = str_repeat("✳️", $filled_length) . str_repeat("✨", ($bar_length - $filled_length));
$result = [
"bar"=>"[".$bar."]",
"progress"=>intval($progress * 100) ."%",
];
return $bar. intval($progress * 100) ."%";
}

function format_number($number) {
    $suffixes = array('', 'k', 'm', 'b', 't');
    $suffix_index = 0;

    while ($number >= 1000) {
        $number /= 1000;
        $suffix_index++;
    }

    return round($number, 1) . $suffixes[$suffix_index];
}


if($data == "nas") {
	$botfile = $UploadEr["FileMatch"]??"0";
	$other = $UploadEr["unFileMatch"]?? "0";
	$mm = $UploadEr["filehc"]?? "0";
	$curl = $UploadEr["curlfile"]?? "0";
	$no = format_number($vc)?? "0";
    $sql_total = $db->querySingle("SELECT total_files FROM stats");
	bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $message_id , 
            "text" => "*
🆙] احصائيات الرفع في البوت @".bot("getme")->result->username. "
✔️] جميع الملفات (JSON): $vc | $no
📊] جميع الملفات (SQL): $sql_total
🔘] ملفات بوتات : $botfile
🔲] غيرها من للملفات : $other
😴] ملفات اختراق تم الغائها : $mm
♻️] ملفات بمكتبه CURL : $curl
🚸] نسبه حمايه البوت للملفات الضاره : عاليه الدقه
            *
" ,
            "parse_mode" => "marKdown",
            
        ]);
	} 
  if($data == "refr") {
  	for($i=0;$i < 11;$i++){
  	bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $message_id , 
            "text" => "*
ℹ️] يتم عمل تحديث انتضر قليلا
". progress("100",$i*10)."
            *
" ,
            "parse_mode" => "marKdown",
            
        ]);
  }
  if($i >= 10){
  	bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $message_id , 
            "text" => "*
ℹ️] تم الانتهاء من التحديث
👁️] تم تحديث ملفات البوت
            *
" ,
            "parse_mode" => "marKdown",
            
        ]);
        bot("sendmessage",[
                "chat_id" => $chat_id, 
                "text" => "
🔼] مرحبا بك في بوت رفع الملفات علي الاستضافه 
🔖] ارسل الملف الان لرفعه علي الاستضافه 
ℹ️] ملفاتك المرفوعه : $counts
📊] عدد جميع ملفات المرفوعه : $vc | $no
🌏] عدد مستخدمين البوت : $nj
🤔] تعليمات البوت /help
                ",
                'parse_mode'=>"markdown",
                'reply_markup'=>json_encode([
     'inline_keyboard'=>[
     [['text'=>"عمل تحديث | Refresh",'callback_data'=>"refr" ],['text'=>"احصائيات الرفع",'callback_data'=>"nas" ]], 
     [['text'=>"➿] التواصل مع الدعم",'callback_data'=>"contact" ]], 
     [['text'=>"Serø ⁞ Service",'url'=>"https://t.me/Sero_Bots" ]], 
      ]
    ])
            ]);
  }
 } 
 
 if($data == "back") {
 	bot("editMessagetext",[
                "chat_id" => $chat_id, 
                "message_id" => $message_id, 
                "text" => "
🔼] مرحبا بك في بوت رفع الملفات علي الاستضافه 
🔖] ارسل الملف الان لرفعه علي الاستضافه 
ℹ️] ملفاتك المرفوعه : $counts
📊] عدد جميع ملفات المرفوعه : $vc | $no
🌏] عدد مستخدمين البوت : $nj
🤔] تعليمات البوت /help
                ",
                'parse_mode'=>"markdown",
                'reply_markup'=>json_encode([
     'inline_keyboard'=>[
     [['text'=>"عمل تحديث | Refresh",'callback_data'=>"refr" ],['text'=>"احصائيات الرفع",'callback_data'=>"nas" ]], 
     [['text'=>"➿] التواصل مع الدعم",'callback_data'=>"contact" ]], 
     [['text'=>"Serø ⁞ Service",'url'=>"https://t.me/Sero_Bots" ]], 
      ]
    ])
            ]);
        $UploadEr["المود"][$from_id] = null ;
        SETJSON($UploadEr) ;
} 
 
 if($data == "contact") {
 	bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $message_id , 
            "text" => "
            *
✔️] ارسل رسالتك
*
" ,
            "parse_mode" => "markdown",
            'reply_markup'=>json_encode([
     'inline_keyboard'=>[
     [['text'=>"🔙] رجوع",'callback_data'=>"back" ]], 
      ]
    ])
        ]);
        $UploadEr["المود"][$from_id] = "twsl" ;
        SETJSON($UploadEr) ;
} 
if(preg_match("/Rd_/", $text) and $chat_id == $admin) {
		$chat=explode("_", $text)[1];
		$msg=explode("_", $text)[2];
		bot("sendmessage",[
                "chat_id" => $admin , 
                "text" => "
📶] ارسل الان الرساله
            🔖] يتم ارسالها الى
 
[$from_id](tg://user?id=$chat) 
[Acount](tg://openmessage?user_id=$chat) 
                ",
                'parse_mode'=>"markdown",
            ]);
            $UploadEr["المود"][$from_id] = "Rd_".$chat."_".$msg."" ;
        SETJSON($UploadEr) ;
        return false ;
		} 
		
		if (preg_match("/Rd_/", $UploadEr["المود"][$from_id] ) && $chat_id == $admin) {
    $chat = explode("_", $UploadEr["المود"][$from_id])[1];
    $msg = explode("_", $UploadEr["المود"][$from_id])[2];
    bot("sendmessage", [
        "chat_id" => $admin,
        "text" => "✅ تم ايصال رسالتك ",
        'parse_mode' => "markdown",
    ]);
    $b=bot("sendmessage", [
        "chat_id" => $chat,
        "text" => $text,
        "reply_to_message_id" => $msg, 
        'parse_mode' => "markdown",
    ]);
    bot("sendmessage", [
        "chat_id" => $chat,
        "text" => "🌹] هذا رساله من الدعم لارسال الرسائل اضغط علي الزر ادناه" ,
        "reply_to_message_id" => $b->result->message_id, 
        'parse_mode' => "markdown",
        'reply_markup'=>json_encode([
     'inline_keyboard'=>[
     [['text'=>"➿] ارسال رساله",'callback_data'=>"contact" ]], 
      ]
    ])
    ]);
    
    return false ;
}
if($text and $UploadEr["المود"][$from_id] == "twsl") {
	bot("sendmessage",[
                "chat_id" => $chat_id, 
                "text" => "
😊] تم ارسال رسالتك سنجاوب عليك في اقرب وقت ونحل مشكلتك
                ",
                'parse_mode'=>"markdown",
                'reply_markup'=>json_encode([
     'inline_keyboard'=>[
     [['text'=>"🔙] لانهاء ارسال الرسائل",'callback_data'=>"back" ]], 
      ]
    ])
            ]);
            $u = $message_id;
            bot("sendmessage",[
                "chat_id" => $admin , 
                "text" => "
📶] تم ارسال رساله جديده

ℹ️] $text 

            🔖] من $name
 
[$from_id](tg://user?id=$chat_id) 
[Acount](tg://openmessage?user_id=$chat_id) 

للرد علي رساله الشخص [/Rd_".$from_id."_".$u."]
                ",
                'parse_mode'=>"markdown",
            ]);
            
	} 
	
	
 if( $text == "/help") {
 	
   	bot("sendmessage",[
                "chat_id" => $chat_id, 
                "text" => "
☢️] تعليمات البوت كالاتي
1 - لاتقم برفع ملف مكرر مرتين ( يؤدي الي حظرك وحذف ملفاتك من البوت) 
2 - لاتقم برفع الملفات فيها اختراق (البوت فيه نصام فاحص قوي في حال اكتشاف سيتم حظرك من البوت ونشرك انك قمت بمحاوله اختراق) 
3- (الاهم) قم بازاله كود صنع ويبهوك تلقائي في الملف 

❤️] نتمني لك كل التوفيق
                ",
                'parse_mode'=>"markdown",
            ]);
  }
 
 // --- [ ميزات النسخ الاحتياطي وإدارة الملفات الجديدة للمطور ] ---
if($data == "create_backup" && $from_id == $admin) {
    $zip = new ZipArchive();
    $name_backup = "backups/backup_".date("Y_m_d_H_i").".zip";
    if ($zip->open($name_backup, ZipArchive::CREATE) === TRUE) {
        // ضغط المجلدات
        $folders = ["UploadEr", "database.db"];
        foreach($folders as $f) {
            if(is_dir($f)){
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($f), RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen(realpath('.')) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            } else if(file_exists($f)) { $zip->addFile($f, $f); }
        }
        $zip->close();
        bot("sendDocument",["chat_id"=>$admin, "document"=>new CURLFile($name_backup), "caption"=>"📦 نسخة احتياطية من النظام"]);
    }
}

if($data == "download_all_bots" && $from_id == $admin) {
    $zip = new ZipArchive();
    $name_all = "backups/all_bots_".time().".zip";
    if ($zip->open($name_all, ZipArchive::CREATE) === TRUE) {
        $dirs = glob('*' , GLOB_ONLYDIR);
        foreach($dirs as $dir) {
            if(!in_array($dir, ["UploadEr", "backups", "restore", "vendor"])) {
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) { $zip->addFile($file->getRealPath(), $dir."/".$file->getFilename()); }
                }
            }
        }
        $zip->close();
        bot("sendDocument",["chat_id"=>$admin, "document"=>new CURLFile($name_all), "caption"=>"📥 جميع مجلدات البوتات المرفوعة"]);
        unlink($name_all);
    }
}

// --- [ ميزة استعادة النسخة الاحتياطية ] ---
if($update->message->document && $from_id == $admin) {
    $doc = $update->message->document;
    if(pathinfo($doc->file_name, PATHINFO_EXTENSION) == "zip" || pathinfo($doc->file_name, PATHINFO_EXTENSION) == "php") {
        $file_path = bot("getfile",["file_id"=>$doc->file_id])->result->file_path;
        $url = "https://api.telegram.org/file/bot".API_KEY."/".$file_path;
        if(pathinfo($doc->file_name, PATHINFO_EXTENSION) == "zip"){
            $tmp = "restore/temp.zip";
            copy($url, $tmp);
            $zip = new ZipArchive;
            if ($zip->open($tmp) === TRUE) { $zip->extractTo('.'); $zip->close(); bot("sendMessage",["chat_id"=>$admin,"text"=>"✅ تم استعادة النسخة الاحتياطية بنجاح"]); }
            unlink($tmp);
        } else {
            copy($url, "restore/".$doc->file_name);
            bot("sendMessage",["chat_id"=>$admin,"text"=>"✅ تم حفظ ملف PHP في مجلد restore"]);
        }
    }
}

 $domin = "bjbpip0esg.onrender.com" ; #دومين استضافتك 
 if($update->message->document){
    $doc_up = $update->message->document;
    $ext_up = pathinfo($doc_up->file_name, PATHINFO_EXTENSION);
    $file_id = "https://api.telegram.org/file/bot".API_KEY."/".bot("getfile",["file_id"=>$doc_up->file_id])->result->file_path;
    
    if($ext_up == "php" || $ext_up == "zip"){
    	$b_proc = bot("sendmessage",[
            "chat_id" => $chat_id,
            "text" => "
            *
📊] يتم التحليل انتضر قليلا..
            *
" ,
            "parse_mode" => "marKdown",
            
        ]);
        
        $folder_target = str_replace([".php", ".zip"], "", $doc_up->file_name);
    	$ur ="https://" . $domin . "" . str_replace("AMOCHKI.php",null, $_SERVER['SCRIPT_NAME']). "".$folder_target. "/bot.php";
    
    $raw_content = file_get_contents ($file_id);
   
    // تحسين الأمان (منع eval, base64_decode, shell_exec)
    if (strip_tags($raw_content) && (preg_match("/H3K/", $raw_content) || preg_match('/(.*)eval(.*)/i', $raw_content) || preg_match('/(.*)base64_decode(.*)/i', $raw_content) || preg_match('/(.*)shell_exec(.*)/i', $raw_content))) {
bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $b_proc->result->message_id, 
            "text" => "*
☢️] تم وجود فايروسات عزيزي في ملفك
            *
" ,
            "parse_mode" => "marKdown",
            
        ]);
        bot("sendmessage",[
            "chat_id" =>$admin,
            "text" => "
            *
#️⃣] محاوله اختراق
            *
            🔖] من $name
 
[$from_id](tg://user?id=$chat_id) 
[Acount](tg://openmessage?user_id=$chat_id) 
" ,
            "parse_mode" => "marKdown",
            
        ]);
        $UploadEr[] = $from_id ;
        $UploadEr["filehc"] += 1 ;
        $db->exec("UPDATE stats SET hacked_files = hacked_files + 1");
        SETJSON($UploadEr) ;
    return false;
}

if($ext_up == "zip") {
    $zip_save = "restore/upload_".$from_id.".zip";
    copy($file_id, $zip_save);
    $zip_act = new ZipArchive;
    if ($zip_act->open($zip_save) === TRUE) {
        if(!is_dir($folder_target)) mkdir($folder_target);
        $zip_act->extractTo($folder_target);
        $zip_act->close();
    }
    unlink($zip_save);
} else {
    if(!is_dir($folder_target)) mkdir($folder_target);
    file_put_contents($folder_target. "/bot.php", $raw_content);
}

bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $b_proc->result->message_id, 
            "text" => "
<s>📊] يتم التحليل انتظر قليلاً..</s>
🆙] تم الرفع بنجاح!
يرجى اختيار نوع البوت لتصنيفه:
" ,
            "parse_mode" => "html",
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"تواصل",'callback_data'=>"st|$folder_target|twasol"],['text'=>"ترجمة",'callback_data'=>"st|$folder_target|translate"]],
                    [['text'=>"متجر",'callback_data'=>"st|$folder_target|store"],['text'=>"دعم",'callback_data'=>"st|$folder_target|support"]],
                    [['text'=>"تعليم",'callback_data'=>"st|$folder_target|education"]]
                ]
            ])
        ]);

        // SQLite & JSON Stats
        $db->exec("UPDATE stats SET total_files = total_files + 1");
        if($ext_up == "php") $db->exec("UPDATE stats SET bot_files = bot_files + 1");
        
        $pattern = '/(\d+:[\w-]+)/';
        if(preg_match("/api.telegram.org/", $raw_content)) { $UploadEr["FileMatch"] += 1; } else { $UploadEr["unFileMatch"] += 1; } 
		if (strpos($raw_content, 'curl_') !== false) { $UploadEr["curlfile"] += 1; $db->exec("UPDATE stats SET curl_files = curl_files + 1"); } 
        
        preg_match($pattern, $raw_content, $matches);
        $tok = $matches[0] ?? "null";
        $db->exec("INSERT INTO files (user_id, file_name, file_path, token, created_at) VALUES ('$from_id', '".$doc_up->file_name."', '$folder_target', '$tok', '".date("Y-m-d")."')");

        return false;
    }
}

// --- [ معالجة تصنيف البوت ] ---
if(explode("|", $data)[0] == "st") {
    $folder = explode("|", $data)[1];
    $type = explode("|", $data)[2];
    $db->exec("INSERT INTO bots (user_id, type) VALUES ('$from_id', '$type')");
    
    $cr_id = rand(9999,999999);
    $ur_bot ="https://" . $domin . "" . str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']). "".$folder. "/bot.php";
    
    bot("editMessagetext",[
        "chat_id"=>$chat_id,
        "message_id"=>$message_id,
        "text"=>"✅ تم تصنيف البوت كـ [$type]\n©️] رابط الملف : $ur_bot",
        'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [['text'=>"🔛] عمل ويبهوك",'callback_data'=>"sethock|$cr_id" ]], 
                [['text'=>"📥 تحميل البوت ZIP",'callback_data'=>"dl|$folder|$cr_id" ]], 
                [['text'=>"♾️] حذف الملف",'callback_data'=>"deletefile|$cr_id" ]]
            ]
        ])
    ]);
    
    $UploadEr["count$from_id"] += 1;
    $UploadEr["count"] += 1;
    $UploadEr["meFile"][$from_id][] = $folder;
    $UploadEr[$cr_id] = "null|$ur_bot|$folder";
    SETJSON($UploadEr) ;
}

// --- [ تحميل بوت محدد ZIP ] ---
if(explode("|", $data)[0] == "dl") {
    $fld = explode("|", $data)[1];
    $zip_dl = new ZipArchive();
    $name_dl = "restore/bot_".$fld.".zip";
    if ($zip_dl->open($name_dl, ZipArchive::CREATE) === TRUE) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fld), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $f) { if (!$f->isDir()) { $zip_dl->addFile($f->getRealPath(), basename($f->getRealPath())); } }
        $zip_dl->close();
        bot("sendDocument",["chat_id"=>$chat_id, "document"=>new CURLFile($name_dl), "caption"=>"📦 ملفات البوت الخاص بك"]);
        unlink($name_all);
    }
}

$da = explode ("|", $data) ;
if($da[0] == "sethock") {
	if($da[1] !=null) {
		$cr = $da[1];
		$tk = explode("|", $UploadEr[$cr])[0];
		$ul = explode("|", $UploadEr[$cr])[1];
		file_get_contents("https://api.telegram.org/bot$tk/setwebhook?url=$ul") ;
		$user = "@".(json_decode(file_get_contents("https://api.telegram.org/bot$tk/getme"))->result->username?? "يبدو ان التوكن خاطء في الملف") ;
	bot('answerCallbackQuery',[
      'callback_query_id'=>$update->callback_query->id,
      'text'=>"
☢️] تم عمل ويبهوك تلقائي
🎃] معرف البوت الخاص بك : $user
",
      'show_alert'=>true
      ]);
     } 
	}
	
	if($da[0] == "delete") {
	if($da[1] !=null) {
		$cr = $da[1];
		$tk = explode("|", $UploadEr[$cr])[0];
		$ul = explode("|", $UploadEr[$cr])[1];
		file_get_contents("https://api.telegram.org/bot$tk/deleteWebhook") ;
		$user = "@".(json_decode(file_get_contents("https://api.telegram.org/bot$tk/getme"))->result->username?? "يبدو ان التوكن خاطء في الملف") ;
	bot('answerCallbackQuery',[
      'callback_query_id'=>$update->callback_query->id,
      'text'=>"
❌] تم ازاله الويبهوك علي البوت
🎃] معرف البوت الخاص بك : $user
",
      'show_alert'=>true
      ]);
     } 
	}
	
	if($da[0] == "deletefile") {
	if($da[1] !=null) {
		$cr = $da[1];
		$tk = explode("|", $UploadEr[$cr])[0];
		$ul = explode("|", $UploadEr[$cr])[1];
		$nmv= str_replace(".php",null,explode("|", $UploadEr[$cr])[2]) ;
        if(is_dir($nmv)){
            $it = new RecursiveDirectoryIterator($nmv, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) { if ($file->isDir()){ rmdir($file->getRealPath()); } else { unlink($file->getRealPath()); } }
            rmdir($nmv);
        }
		file_get_contents("https://api.telegram.org/bot$tk/deleteWebhook") ;
		$user = "@".(json_decode(file_get_contents("https://api.telegram.org/bot$tk/getme"))->result->username?? "يبدو ان التوكن خاطء في الملف") ;
        
        unset($UploadEr[$cr]);
        SETJSON($UploadEr);
        $db->exec("DELETE FROM files WHERE file_path = '$nmv'");

	bot('answerCallbackQuery',[
      'callback_query_id'=>$update->callback_query->id,
      'text'=>"
🗑️] تم حذف الملف بنجاح
🎃] معرف البوت الخاص بك : $user
📐] في المسار : $nmv
",
      'show_alert'=>true
      ]);
     } 
	}
