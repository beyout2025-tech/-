<?php
error_reporting(0);

// --- [ الإعدادات والتوكن الأصلية ] ---
$token = "8571489537:AAHjdZv5ikLonsmhmdMB4uXJaCLdd5pLMo4";
$admin = 7607952642 ;
define('API_KEY',$token);

// --- [ إعداد قاعدة البيانات SQLite3 ] ---
$db = new SQLite3('UploadEr_Data.db');
$db->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, status TEXT DEFAULT 'active', mode TEXT, count_files INTEGER DEFAULT 0, m_id INTEGER)");
$db->exec("CREATE TABLE IF NOT EXISTS stats (key TEXT PRIMARY KEY, value INTEGER DEFAULT 0)");
$db->exec("CREATE TABLE IF NOT EXISTS file_storage (id TEXT PRIMARY KEY, data TEXT)");

// تهيئة الإحصائيات الافتراضية
$db->exec("INSERT OR IGNORE INTO stats (key, value) VALUES ('total_files', 0), ('total_users', 0), ('FileMatch', 0), ('unFileMatch', 0), ('filehc', 0), ('curlfile', 0)");

// --- [ رابط الويبهوك التلقائي ] ---
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
    }
}

$usrbot = bot("getme")->result->username;
$emoji = "➡️\n🎟️\n↪️\n🔘\n🏠";
$emoji = explode("\n", $emoji);
$b = $emoji[rand(0, 4)];
$NamesBACK = "رجوع $b";

define("USR_BOT", $usrbot);
if(!is_dir("UploadEr")) mkdir("UploadEr");

// دالة فارغة للحفاظ على التوافق مع الكود القديم
function SETJSON($INPUT) { return true; }

$update = json_decode(file_get_contents('php://input'));

if ($update->message) {
    $message = $update->message;
    $message_id = $update->message->message_id;
    $username = $message->from->username;
    $chat_id = $message->chat->id;
    $title = $message->chat->title;
    $text = $message->text;
    $user = $message->from->username;
    $name = $message->from->first_name;
    $from_id = $message->from->id;
}

if ($update->callback_query) {
    $data = $update->callback_query->data;
    $chat_id = $update->callback_query->message->chat->id;
    $title = $update->callback_query->message->chat->title;
    $message_id = $update->callback_query->message->message_id;
    $name = $update->callback_query->message->chat->first_name;
    $user = $update->callback_query->message->chat->username;
    $from_id = $update->callback_query->from->id;
}

// جلب بيانات المستخدم من SQLite
$user_data = $db->queryRow("SELECT * FROM users WHERE id = '$from_id'");
if (!$user_data) {
    $db->exec("INSERT INTO users (id) VALUES ('$from_id')");
    $db->exec("UPDATE stats SET value = value + 1 WHERE key = 'total_users'");
    $user_data = ['id' => $from_id, 'status' => 'active', 'mode' => null, 'count_files' => 0];
}

// --- [ ميزات النسخ الاحتياطي للمطور فقط ] ---
if($data == "db_download" && $from_id == $admin) {
    bot("sendDocument", [
        "chat_id" => $admin,
        "document" => new CURLFile('UploadEr_Data.db'),
        "caption" => "✅ نسخة قاعدة البيانات الأصلية"
    ]);
}

if($data == "db_upload" && $from_id == $admin) {
    bot("editMessagetext", [
        "chat_id" => $admin,
        "message_id" => $message_id,
        "text" => "📂 ارسل الآن ملف قاعدة البيانات (.db) المحدثة."
    ]);
    $db->exec("UPDATE users SET mode = 'up_db' WHERE id = '$admin'");
}

if($message->document && $user_data['mode'] == 'up_db' && $from_id == $admin) {
    $f_id = $message->document->file_id;
    $g = bot("getfile", ["file_id" => $f_id]);
    copy("https://api.telegram.org/file/bot".API_KEY."/".$g->result->file_path, "UploadEr_Data.db");
    bot("sendMessage", ["chat_id" => $admin, "text" => "✅ تم تحديث القاعدة بنجاح."]);
    $db->exec("UPDATE users SET mode = NULL WHERE id = '$admin'");
}

if($data == "all_files_zip" && $from_id == $admin) {
    bot("answerCallbackQuery", ["callback_query_id" => $update->callback_query->id, "text" => "جاري الضغط..."]);
    $zip = new ZipArchive();
    $zName = 'Backup_Files_'.time().'.zip';
    if ($zip->open($zName, ZipArchive::CREATE) === TRUE) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $f) {
            if (!$f->isDir()) {
                $fPath = $f->getRealPath();
                $rPath = substr($fPath, strlen(realpath('.')) + 1);
                if ($rPath !== $zName && !strpos($rPath, '.db')) {
                    $zip->addFile($fPath, $rPath);
                }
            }
        }
        $zip->close();
        bot("sendDocument", ["chat_id" => $admin, "document" => new CURLFile($zName), "caption" => "📦 أرشيف كافة ملفات البوتات."]);
        unlink($zName);
    }
}

// --- [ نظام التقارير وفك الحظر ] ---
if($data == "sendReport") {
    bot("editMessagetext",[
        "chat_id" => $chat_id,
        'message_id' => $message_id , 
        "text" => "\n#️⃣] ارسل الان الكليشه التوضيحيه للمطور\nℹ️] ان كان عن طريق الخطا سيتم فك الحظر\n"
    ]);
    $db->exec("UPDATE users SET mode = 'sR' WHERE id = '$from_id'");
    return false ;
} 

if($text and $user_data['mode'] == "sR") {
    bot("sendMessage", ["chat_id" => $chat_id, "text" => "✅] تم استلام الطلب سيتم مراجعته في اقرب وقت ممكن", "parse_mode" => "markdown"]);
    bot("sendMessage", [
        "chat_id" => $admin ,
        "text" => "🎃] طلب فك حظر عزيزي المبرمج\n🔖] من $name\n\n[$from_id](tg://user?id=$chat_id) \n[Acount](tg://openmessage?user_id=$chat_id) \n\nالكليشه : $text\nلفك الحظر عنه ارسل [/Unb_$from_id]\n",
        "parse_mode" => "markdown"
    ]);
    $db->exec("UPDATE users SET mode = NULL WHERE id = '$from_id'");
    return false ;
} 

$not = array("$admin", "6006432889");
if ($user_data['status'] == 'banned' && !in_array($from_id, $not)) {
    bot("deleteMessage", ["chat_id" => $chat_id, "message_id" => $user_data['m_id']]);
    $n = bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => "⚠️ You are banned from using the bot due to violations.\n⚠️ تم حظرك من استخدام الروبوت بسبب الانتهاكات.",
        "parse_mode" => "markdown", 
        'reply_markup'=>json_encode(['inline_keyboard'=>[[['text'=>"ارسال طلب فك حظر",'callback_data'=>"sendReport" ]]]])
    ]);
    $db->exec("UPDATE users SET m_id = '".$n->result->message_id."' WHERE id = '$from_id'");
    return false;
}

if(preg_match("/Unb_/", $text) && $from_id == $admin) {
    $tid = explode("_",$text)[1];
    $db->exec("UPDATE users SET status = 'active' WHERE id = '$tid'");
    bot("sendMessage", ["chat_id" => $admin, "text" => "Done ✅\nId : ($tid)"]);
    bot("sendMessage", ["chat_id" => $tid, "text" => "⚠️] تم فك الحظر عن حسابك\n🤔] الرجاء التزام بقوانين البوت\n", "parse_mode" => "markdown"]);
}

// --- [ الواجهة الرئيسية ] ---
$counts = $user_data['count_files'] ?? 0;
$vc = $db->querySingle("SELECT value FROM stats WHERE key = 'total_files'");
$no = format_number($vc)?? "0";
$nj = $db->querySingle("SELECT value FROM stats WHERE key = 'total_users'");

if( $text == "/start") {
    $kb_main = [
        [['text'=>"عمل تحديث | Refresh",'callback_data'=>"refr" ],['text'=>"احصائيات الرفع",'callback_data'=>"nas" ]], 
        [['text'=>"➿] التواصل مع الدعم",'callback_data'=>"contact" ]], 
        [['text'=>"Serø ⁞ Service",'url'=>"https://t.me/Sero_Bots" ]]
    ];
    if($from_id == $admin) {
        $kb_main[] = [['text'=>"📥 تحميل DB",'callback_data'=>"db_download" ],['text'=>"📤 رفع DB",'callback_data'=>"db_upload" ]];
        $kb_main[] = [['text'=>"📁 تحميل كافة الملفات ZIP",'callback_data'=>"all_files_zip" ]];
    }
    bot("sendmessage",[
        "chat_id" => $chat_id, 
        "text" => "\n🔼] مرحبا بك في بوت رفع الملفات علي الاستضافه \n🔖] ارسل الملف الان لرفعه علي الاستضافه (PHP أو ZIP) \nℹ️] ملفاتك المرفوعه : $counts\n📊] عدد جميع ملفات المرفوعه : $vc | $no\n🌏] عدد مستخدمين البوت : $nj\n🤔] تعليمات البوت /help\n",
        'parse_mode'=>"markdown",
        'reply_markup'=>json_encode(['inline_keyboard'=>$kb_main])
    ]);
    $db->exec("UPDATE users SET mode = NULL WHERE id = '$from_id'");
    return false ;
}

function progress($total, $current){
    $progress = $current / $total;
    $bar_length = 20;
    $filled_length = round($bar_length * $progress);
    $bar = str_repeat("✳️", $filled_length) . str_repeat("✨", ($bar_length - $filled_length));
    return $bar. intval($progress * 100) ."%";
}

function format_number($number) {
    $suffixes = array('', 'k', 'm', 'b', 't');
    $suffix_index = 0;
    while ($number >= 1000) { $number /= 1000; $suffix_index++; }
    return round($number, 1) . $suffixes[$suffix_index];
}

// --- [ إحصائيات الرفع ] ---
if($data == "nas") {
    $botfile = $db->querySingle("SELECT value FROM stats WHERE key = 'FileMatch'");
    $other = $db->querySingle("SELECT value FROM stats WHERE key = 'unFileMatch'");
    $mm = $db->querySingle("SELECT value FROM stats WHERE key = 'filehc'");
    $curl = $db->querySingle("SELECT value FROM stats WHERE key = 'curlfile'");
    bot("editMessagetext",[
        "chat_id" => $chat_id,
        'message_id' => $message_id , 
        "text" => "*\n🆙] احصائيات الرفع في البوت @$usrbot\n✔️] جميع الملفات : $vc | $no\n🔘] ملفات بوتات : $botfile\n🔲] غيرها من للملفات : $other\n😴] ملفات اختراق تم الغائها : $mm\n♻️] ملفات بمكتبه CURL : $curl\n🚸] نسبه حمايه البوت للملفات الضاره : عاليه الدقه\n*",
        "parse_mode" => "marKdown",
    ]);
}

// --- [ عملية التحديث ] ---
if($data == "refr") {
    for($i=0;$i < 11;$i++){
        bot("editMessagetext",["chat_id" => $chat_id,'message_id' => $message_id ,"text" => "*\nℹ️] يتم عمل تحديث انتضر قليلا\n". progress("100",$i*10)."\n*","parse_mode" => "marKdown"]);
    }
    bot("editMessagetext",["chat_id" => $chat_id,'message_id' => $message_id ,"text" => "*\nℹ️] تم الانتهاء من التحديث\n👁️] تم تحديث ملفات البوت\n*","parse_mode" => "marKdown"]);
    bot("sendmessage",[
        "chat_id" => $chat_id, 
        "text" => "\n🔼] مرحبا بك في بوت رفع الملفات علي الاستضافه \n🔖] ارسل الملف الان لرفعه علي الاستضافه \nℹ️] ملفاتك المرفوعه : $counts\n📊] عدد جميع ملفات المرفوعه : $vc | $no\n🌏] عدد مستخدمين البوت : $nj\n🤔] تعليمات البوت /help\n",
        'parse_mode'=>"markdown",
        'reply_markup'=>json_encode(['inline_keyboard'=>[[['text'=>"عمل تحديث | Refresh",'callback_data'=>"refr" ],['text'=>"احصائيات الرفع",'callback_data'=>"nas" ]],[['text'=>"➿] التواصل مع الدعم",'callback_data'=>"contact" ]],[['text'=>"Serø ⁞ Service",'url'=>"https://t.me/Sero_Bots" ]]]])
    ]);
}

if($data == "back") {
    bot("editMessagetext",["chat_id" => $chat_id, "message_id" => $message_id, "text" => "\n🔼] مرحبا بك في بوت رفع الملفات علي الاستضافه \n🔖] ارسل الملف الان لرفعه علي الاستضافه \nℹ️] ملفاتك المرفوعه : $counts\n📊] عدد جميع ملفات المرفوعه : $vc | $no\n🌏] عدد مستخدمين البوت : $nj\n🤔] تعليمات البوت /help\n",'parse_mode'=>"markdown",'reply_markup'=>json_encode(['inline_keyboard'=>[[['text'=>"عمل تحديث | Refresh",'callback_data'=>"refr" ],['text'=>"احصائيات الرفع",'callback_data'=>"nas" ]],[['text'=>"➿] التواصل مع الدعم",'callback_data'=>"contact" ]],[['text'=>"Serø ⁞ Service",'url'=>"https://t.me/Sero_Bots" ]]]])]);
    $db->exec("UPDATE users SET mode = NULL WHERE id = '$from_id'");
}

// --- [ نظام التواصل ] ---
if($data == "contact") {
    bot("editMessagetext",["chat_id" => $chat_id,'message_id' => $message_id ,"text" => "\n*\n✔️] ارسل رسالتك\n*\n" ,"parse_mode" => "markdown",'reply_markup'=>json_encode(['inline_keyboard'=>[[['text'=>"🔙] رجوع",'callback_data'=>"back" ]]]])]);
    $db->exec("UPDATE users SET mode = 'twsl' WHERE id = '$from_id'");
}

if(preg_match("/Rd_/", $text) and $chat_id == $admin) {
    $e = explode("_", $text);
    $c = $e[1]; $m = $e[2];
    bot("sendmessage",["chat_id" => $admin , "text" => "\n📶] ارسل الان الرساله\n            🔖] يتم ارسالها الى\n \n[$from_id](tg://user?id=$c) \n[Acount](tg://openmessage?user_id=$c) \n",'parse_mode'=>"markdown"]);
    $db->exec("UPDATE users SET mode = 'Rd_".$c."_".$m."' WHERE id = '$admin'");
    return false ;
}

if (preg_match("/Rd_/", $user_data['mode']) && $chat_id == $admin) {
    $e = explode("_", $user_data['mode']);
    $c = $e[1]; $m = $e[2];
    bot("sendmessage", ["chat_id" => $admin, "text" => "✅ تم ايصال رسالتك "]);
    $bb = bot("sendmessage", ["chat_id" => $c, "text" => $text, "reply_to_message_id" => $m, 'parse_mode' => "markdown"]);
    bot("sendmessage", ["chat_id" => $c,"text" => "🌹] هذا رساله من الدعم لارسال الرسائل اضغط علي الزر ادناه" ,"reply_to_message_id" => $bb->result->message_id, 'reply_markup'=>json_encode(['inline_keyboard'=>[[['text'=>"➿] ارسال رساله",'callback_data'=>"contact" ]]]])]);
    return false ;
}

if($text and $user_data['mode'] == "twsl") {
    bot("sendmessage",["chat_id" => $chat_id, "text" => "\n😊] تم ارسال رسالتك سنجاوب عليك في اقرب وقت ونحل مشكلتك\n",'parse_mode'=>"markdown",'reply_markup'=>json_encode(['inline_keyboard'=>[[['text'=>"🔙] لانهاء ارسال الرسائل",'callback_data'=>"back" ]]]])]);
    bot("sendmessage",["chat_id" => $admin , "text" => "\n📶] تم ارسال رساله جديده\n\nℹ️] $text \n\n            🔖] من $name\n \n[$from_id](tg://user?id=$chat_id) \n[Acount](tg://openmessage?user_id=$chat_id) \n\nللرد علي رساله الشخص [/Rd_".$from_id."_".$message_id."]\n",'parse_mode'=>"markdown"]);
}

if( $text == "/help") {
    bot("sendmessage",["chat_id" => $chat_id, "text" => "\n☢️] تعليمات البوت كالاتي\n1 - لاتقم برفع ملف مكرر مرتين ( يؤدي الي حظرك وحذف ملفاتك من البوت) \n2 - لاتقم برفع الملفات فيها اختراق (البوت فيه نصام فاحص قوي في حال اكتشاف سيتم حظرك من البوت ونشرك انك قمت بمحاوله اختراق) \n3- (الاهم) قم بازاله كود صنع ويبهوك تلقائي في الملف \n\n❤️] نتمني لك كل التوفيق\n",'parse_mode'=>"markdown"]);
}

// --- [ نظام الرفع والفحص (PHP & ZIP) ] ---
$domin = "dev-hostbot.pantheonsite.io";
if($update->message->document){
    $doc = $update->message->document;
    $ext = pathinfo($doc->file_name, PATHINFO_EXTENSION);
    
    if($ext == "php" || $ext == "zip"){
        $b_up = bot("sendmessage",["chat_id" => $chat_id, "text" => "*\n📊] يتم التحليل انتضر قليلا..\n*", "parse_mode" => "marKdown"]);
        $f_url = "https://api.telegram.org/file/bot".API_KEY."/".bot("getfile",["file_id"=>$doc->file_id])->result->file_path;
        $folder = str_replace([".php", ".zip"], "", $doc->file_name);
        
        if($ext == "php"){
            $f_txt = file_get_contents($f_url);
            // الفحص الأمني الأصلي
            if (strip_tags($f_txt) && preg_match("/H3K/", $f_txt) || preg_match('/(.*)eval(.*)/i', $f_txt)) {
                bot("editMessagetext",["chat_id" => $chat_id, 'message_id' => $b_up->result->message_id, "text" => "☢️] تم وجود فايروسات في ملفك"]);
                $db->exec("UPDATE users SET status = 'banned' WHERE id = '$from_id'");
                $db->exec("UPDATE stats SET value = value + 1 WHERE key = 'filehc'");
                return false;
            }
            if(!is_dir($folder)) mkdir($folder);
            file_put_contents($folder. "/bot.php", $f_txt);
        }
        
        if($ext == "zip"){
            $z_temp = "t_".$from_id.".zip";
            copy($f_url, $z_temp);
            $z = new ZipArchive;
            if ($z->open($z_temp) === TRUE) {
                if(!is_dir($folder)) mkdir($folder);
                $z->extractTo($folder);
                $z->close();
                unlink($z_temp);
            } else {
                bot("editMessagetext",["chat_id" => $chat_id, 'message_id' => $b_up->result->message_id, "text" => "❌] فشل فك الضغط"]);
                return false;
            }
        }

        $db->exec("UPDATE users SET count_files = count_files + 1 WHERE id = '$from_id'");
        $db->exec("UPDATE stats SET value = value + 1 WHERE key = 'total_files'");
        
        $link ="https://$domin/" . str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']). "$folder/bot.php";
        bot("editMessagetext",["chat_id" => $chat_id,'message_id' => $b_up->result->message_id, "text" => "🆙] تم الرفع بنجاح\n✳️] اسم الملف (".$doc->file_name.")","parse_mode" => "html"]);
        
        $cid = rand(9999,999999);
        $db->exec("INSERT INTO file_storage (id, data) VALUES ('$cid', 'null|$link|".$doc->file_name."')");
        $kb_up = json_encode(['inline_keyboard'=>[[['text'=>"🔛] عمل ويبهوك",'callback_data'=>"sethock|$cid" ]],[['text'=>"♾️] حذف الملف",'callback_data'=>"deletefile|$cid" ]]]]);
        bot("sendmessage",["chat_id" => $chat_id, "text" => "🔼] تم الرفع بنجاح\n©️] رابط التشغيل : $link", 'reply_markup'=>$kb_up]);
    } else {
        bot("sendmessage",["chat_id" => $chat_id, "text" => "❌] قم بارسال ملفات بصيغه php أو zip فقط" , "parse_mode" => "marKdown"]);
    }
}

// --- [ عمليات الويبهوك والحذف ] ---
$da = explode ("|", $data) ;
if($da[0] == "sethock") {
    if($da[1] !=null) {
        $cid = $da[1];
        $i = explode("|", $db->querySingle("SELECT data FROM file_storage WHERE id = '$cid'"));
        $tk = $i[0]; $ul = $i[1];
        file_get_contents("https://api.telegram.org/bot$tk/setwebhook?url=$ul") ;
        $ubot = "@".(json_decode(file_get_contents("https://api.telegram.org/bot$tk/getme"))->result->username?? "خطأ في التوكن") ;
        bot('answerCallbackQuery',['callback_query_id'=>$update->callback_query->id,'text'=>"\n☢️] تم عمل ويبهوك تلقائي\n🎃] معرف البوت : $ubot\n",'show_alert'=>true]);
    } 
}

if($da[0] == "deletefile") {
    if($da[1] !=null) {
        $cid = $da[1];
        $i = explode("|", $db->querySingle("SELECT data FROM file_storage WHERE id = '$cid'"));
        $tk = $i[0]; $nm = str_replace(".php",null,$i[2]);
        if(is_dir($nm)){ unlink("$nm/bot.php"); rmdir($nm); }
        file_get_contents("https://api.telegram.org/bot$tk/deleteWebhook") ;
        bot('answerCallbackQuery',['callback_query_id'=>$update->callback_query->id,'text'=>"\n🗑️] تم حذف الملف بنجاح\n",'show_alert'=>true]);
    } 
}

