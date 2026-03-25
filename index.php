<?php
error_reporting(0);

$token = "8571489537:AAHjdZv5ikLonsmhmdMB4uXJaCLdd5pLMo4";
$admin = 7607952642 ;
$domin = "bjbpip0esg.onrender.com"; 

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
mkdir("UploadEr");

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
    $username = $message->from->username;
    $chat_id = $message->chat->id;
    $title = $message->chat->title;
    $text = $message->text;
    $user = $message->from->username;
    $name = $message->from->first_name;
    $from_id = $message->from->id;
}

$UploadEr = json_decode(file_get_contents("UploadEr/UploadEr.json"), true);

if (!isset($UploadEr["banned"])) {
    $UploadEr["banned"] = [];
}

if (!isset($UploadEr["last_backup"])) {
    $UploadEr["last_backup"] = time(); // تعيين التوقيت الحالي لأول مرة
    SETJSON($UploadEr);
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

if ($UploadEr["mems"][$from_id] == null) {
    $UploadEr["mems"][$from_id] = 1;
    $UploadEr["memsA"][] = $from_id;
    SETJSON($UploadEr);

    // --- كود إشعار المطور بدخول عضو جديد ---
    $total_users = count($UploadEr["memsA"]); // حساب إجمالي المشتركين
    $username_display = ($user) ? "@" . $user : "لا يوجد"; // التحقق من وجود معرف
    
    bot("sendMessage", [
        "chat_id" => $admin,
        "text" => "👾 شخص جديد دخل البوت

👤 معلومات العضو الجديد:
• الاسم: $name
• المعرف: $username_display
• الآيدي: `$from_id`

📊 إجمالي المستخدمين: $total_users",
        "parse_mode" => "markdown"
    ]);
    // --------------------------------------
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
		
		
		// --- ميزة النسخ الاحتياطي الأسبوعي التلقائي ---
// 604800 ثانية = 7 أيام
if (time() - $UploadEr["last_backup"] > 604800) {
    bot("sendDocument", [
        "chat_id" => $admin,
        "document" => new CURLFile("UploadEr/UploadEr.json"),
        "caption" => "🤖 **إشعار أسبوعي تلقائي**\n📦 تم إنشاء نسخة احتياطية لقاعدة البيانات بنجاح.\n📅 التاريخ: " . date("Y-m-d")
    ]);
    
    // تحديث وقت آخر نسخة لكي يبدأ العد التنازلي للأسبوع القادم
    $UploadEr["last_backup"] = time();
    SETJSON($UploadEr);
}

		
		
		
		
		
		
		
		
		// استبدل السطور من 104 إلى 125 بهذا الكود المطور:
// --- بداية التعديل ---
$not = array("$admin"); // اترك الآدمن فقط هنا لضمان الحماية

// نتحقق أولاً هل المستخدم موجود في قائمة المحظورين التي سننشئها
if (isset($from_id) && isset($UploadEr["banned"]) && in_array($from_id, $UploadEr["banned"])) {
    // إذا كان المحظور ليس هو الآدمن، قم بمنعه
    if (!in_array($from_id, $not)) {
        // حذف الرسالة الأخيرة للمحظور لكي لا يزعج البوت
        if(isset($UploadEr["m_id"][$from_id])){
            bot("deleteMessage", [
                "chat_id" => $chat_id,
                "message_id" => $UploadEr["m_id"][$from_id]
            ]);
        }
        
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
        
        // حفظ آيدي الرسالة لحذفها لاحقاً
        $UploadEr["m_id"][$from_id] = $n->result->message_id;
        SETJSON($UploadEr);
        return false; // توقف هنا ولا تنفذ أي أمر آخر للمحظور
    }
}
// --- نهاية التعديل ---

		// --- كود فك الحظر (Unban) المصحح ---
if(preg_match("/^\/?Unb_(\d+)/", $text, $unb_match)) {
    if($from_id == $admin) {
        $id_to_unban = $unb_match[1]; // استخراج الآيدي بشكل أدق باستخدام regex
        
        // التأكد من وجود مصفوفة المحظورين قبل البدء بالبحث
        if (!isset($UploadEr["banned"])) { 
            $UploadEr["banned"] = []; 
        }
        
        $key = array_search($id_to_unban, $UploadEr["banned"]);
        
        if ($key !== false) {
            unset($UploadEr["banned"][$key]);
            $UploadEr["banned"] = array_values($UploadEr["banned"]); // إعادة ترتيب المصفوفة لضمان صحة ملف JSON
            SETJSON($UploadEr);
            
            bot("sendMessage", [
                "chat_id" => $admin,
                "text" => "✅ تم فك الحظر عن الآيدي: `$id_to_unban` بنجاح.",
                "parse_mode" => "markdown"
            ]);
            
            bot("sendMessage", [
                "chat_id" => $id_to_unban,
                "text" => "⚠️] تم فك الحظر عن حسابك\n🤔] الرجاء الالتزام بقوانين البوت",
                "parse_mode" => "markdown"
            ]);
        } else {
            bot("sendMessage", [
                "chat_id" => $admin, 
                "text" => "⚠️ هذا المستخدم ليس محظوراً أصلاً."
            ]);
        }
        return false; // إضافة توقف هنا لمنع البوت من معالجة الأوامر الأخرى بعد التنفيذ
    }
}

// --- كود الحظر (Ban) المصحح والمستقل ---

if (preg_match("/^حظر (\d+)/", $text, $match) && $from_id == $admin) {
    $id_to_ban = $match[1];
    
    if (!isset($UploadEr["banned"])) { 
        $UploadEr["banned"] = []; 
    } 
    
    if (!in_array($id_to_ban, $UploadEr["banned"])) {
        $UploadEr["banned"][] = $id_to_ban;
        SETJSON($UploadEr);
        bot("sendMessage", [
            "chat_id" => $admin,
            "text" => "🚫 تم حظر المستخدم ($id_to_ban) بنجاح."
        ]);
    } else {
        bot("sendMessage", [
            "chat_id" => $admin, 
            "text" => "⚠️ هذا المستخدم محظور بالفعل."
        ]);
    }
    return false;
}

		
		


		
$counts = $UploadEr["count$from_id"] ?? 0;
$vc = $UploadEr["count"] ?? 0;
$no = format_number($vc)?? "0";
$nj = count($UploadEr["memsA"]) ;


if ($text == "/admin" && $from_id == $admin) {
    bot("sendMessage", [
        "chat_id" => $admin,
        "text" => "👑 لوحة تحكم المطور:",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "📊 الإحصائيات", 'callback_data' => "nas"], ['text' => "🚫 المحظورين", 'callback_data' => "show_banned"]],
                [['text' => "📢 إذاعة للكل", 'callback_data' => "broadcast"]],
                [['text' => "📥 تحميل نسخة احتياطية", 'callback_data' => "down_db"]], // زر التحميل
                [['text' => "📤 رفع نسخة احتياطية", 'callback_data' => "up_db"]],    // زر الرفع
                [['text' => "🔄 تحديث", 'callback_data' => "refr"], ['text' => "🔙 رجوع", 'callback_data' => "back"]],
            ]
        ])
    ]);
    return false;
}





   if( $text == "/start") {
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
	bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $message_id , 
            "text" => "*
🆙] احصائيات الرفع في البوت @".bot("getme")->result->username. "
✔️] جميع الملفات : $vc | $no
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
	
	

// --- الإضافة هنا ---
if ($data == "show_banned" && $from_id == $admin) {
    $list = "";
    foreach ($UploadEr["banned"] as $id) {
        $list .= "🚫 ID: `$id`\n";
    }
    $text_banned = empty($list) ? "لا يوجد محظورين حالياً." : "قائمة المحظورين:\n\n" . $list;
    bot("editMessagetext", [
        "chat_id" => $chat_id,
        'message_id' => $message_id,
        "text" => $text_banned,
        "parse_mode" => "markdown",
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "🔙 رجوع", 'callback_data' => "back"]]]])
    ]);
}
// ------------------

// --- كود تفعيل وضع الإذاعة ---
if($data == "broadcast" && $from_id == $admin) {
    bot("editMessagetext",[
        "chat_id" => $chat_id,
        'message_id' => $message_id , 
        "text" => "📢 أرسل الآن الرسالة التي تريد توجيهها لجميع المستخدمين (نص فقط):\n\nإرسال /cancel للإلغاء." ,
    ]);
    $UploadEr["mode"][$from_id] = "broadcasting";
    SETJSON($UploadEr);
    return false;
}

// --- معالجة الإذاعة عند إرسال النص ---
if($text && $UploadEr["mode"][$from_id] == "broadcasting" && $from_id == $admin) {
    if($text == "/cancel") {
        $UploadEr["mode"][$from_id] = null;
        SETJSON($UploadEr);
        bot("sendMessage", ["chat_id" => $admin, "text" => "❌ تم إلغاء الإذاعة."]);
        return false;
    }
    
    $users = $UploadEr["memsA"]; // قائمة جميع مستخدمي البوت
    $count = count($users);
    bot("sendMessage", ["chat_id" => $admin, "text" => "⏳ جاري بدء الإذاعة لـ $count مستخدم..."]);
    
    $success = 0;
    foreach($users as $userId) {
        $res = bot("sendMessage", [
            "chat_id" => $userId,
            "text" => $text,
            "parse_mode" => "markdown"
        ]);
        if($res->ok) $success++;
    }
    
    bot("sendMessage", [
        "chat_id" => $admin, 
        "text" => "✅ تمت الإذاعة بنجاح!\n\nوصلت إلى: $success من أصل $count"
    ]);
    
    $UploadEr["mode"][$from_id] = null;
    SETJSON($UploadEr);
    return false;
}



// --- تحميل الملف ---
if($data == "down_db" && $from_id == $admin) {
    bot("sendDocument", [
        "chat_id" => $admin,
        "document" => new CURLFile("UploadEr/UploadEr.json"),
        "caption" => "📦 نسخة احتياطية لقاعدة البيانات\n📅 التاريخ: " . date("Y-m-d H:i")
    ]);
}

// --- طلب رفع الملف ---
if($data == "up_db" && $from_id == $admin) {
    bot("editMessagetext", [
        "chat_id" => $chat_id,
        "message_id" => $message_id,
        "text" => "📤 ارسل الآن ملف `UploadEr.json` الخاص بالنسخة الاحتياطية.\n⚠️ تنبيه: سيتم استبدال كافة البيانات الحالية!",
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "❌ إلغاء", 'callback_data' => "back"]]]])
    ]);
    $UploadEr["mode"][$from_id] = "waiting_db";
    SETJSON($UploadEr);
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
 

 if($update->message->document){
    // جلب رابط الملف من خوادم تليجرام
    $file_id = "https://api.telegram.org/file/bot".API_KEY."/".bot("getfile",["file_id"=>$update->message->document->file_id])->result->file_path;
    $file_name = $update->message->document->file_name;
    
    // --- الجزء الأول: معالجة رفع قاعدة البيانات (JSON) ---
    if($UploadEr["mode"][$from_id] == "waiting_db" && $from_id == $admin && $file_name == "UploadEr.json") {
        $file_path = bot("getfile",["file_id"=>$update->message->document->file_id])->result->file_path;
        $content = file_get_contents("https://api.telegram.org/file/bot".API_KEY."/".$file_path);
        
        // التأكد أن الملف ليس فارغاً وأنه بصيغة JSON صحيحة
        if(json_decode($content)) {
            file_put_contents("UploadEr/UploadEr.json", $content); // استبدال الملف
            bot("sendMessage", [
                "chat_id" => $chat_id,
                "text" => "✅ تمت استعادة النسخة الاحتياطية بنجاح!\n🔄 تم تحديث جميع البيانات."
            ]);
            // إعادة تصفير المود لكي لا يرفع ملفات أخرى بالخطأ
            $UploadEr = json_decode($content, true);
            $UploadEr["mode"][$from_id] = null;
            SETJSON($UploadEr);
        } else {
            bot("sendMessage", ["chat_id" => $chat_id, "text" => "❌ خطأ في الملف، يرجى التأكد من رفع ملف JSON سليم."]);
        }
        return false;
    }
    
    // --- الجزء الثاني: معالجة رفع ملفات البوت (PHP) ---
    if(pathinfo($file_id, PATHINFO_EXTENSION) == "php"){
        
        // تعريف المتغيرات الضرورية بناءً على المسارات الصحيحة
        $folder_name = str_replace(".php", "", $update->message->document->file_name);
        $current_path = dirname($_SERVER['SCRIPT_NAME']);
        if ($current_path == DIRECTORY_SEPARATOR || $current_path == '.') { $current_path = ""; }
        $ur = "https://" . $domin . $current_path . "/" . $folder_name . "/bot.php";
        $ur = preg_replace('#(?<!https:)/+#', '/', $ur);

    	$b=bot("sendmessage",[
            "chat_id" => $chat_id,
            "text" => "
            *
📊] يتم التحليل انتضر قليلا..
            *
" ,
            "parse_mode" => "marKdown",
            
        ]);

        // جلب محتوى الملف لكي تعمل شروط الفحص الأمني
        $text = file_get_contents($file_id);

    if (strip_tags($text) && preg_match("/H3K/", $text) && preg_match("/public function create/", $text) && preg_match('/(.*)ZipArchive(.*)/i', $text) && preg_match('/(.*)zip(.*)/i', $text) || preg_match('/(.*)eval(.*)/i', $text)) {
        bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $b->result->message_id, 
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
        SETJSON($UploadEr) ;
        return false;
    }

    bot("editMessagetext",[
            "chat_id" => $chat_id,
            'message_id' => $b->result->message_id, 
            "text" => "
<s>📊] يتم التحليل انتظر قليلاً..</s>
🆙] تم الرفع بنجاح
✳️] اسم الملف الخاص بك (". $update->message->document->file_name. ")
" ,
            "parse_mode" => "html",
        ]);

    if(!is_dir($folder_name)){
        mkdir($folder_name, 0755, true);
    }
    file_put_contents($folder_name . "/bot.php", $text);

    $pattern = '/(\d+:[\w-]+)/';

    if(preg_match("/api.telegram.org/", $text)) {
	    $UploadEr["FileMatch"] += 1;
	} else {
		$UploadEr["unFileMatch"] += 1;
	} 
		
	if (strpos($text, 'curl_') !== false) {
	    $UploadEr["curlfile"] += 1;
	} 

    if (preg_match($pattern, $text, $matches)) {
        $token = "ℹ️] توكن البوت : [". $matches[0]. "]" ;
        $n = $matches[0];
        $sethock = ["🔛] عمل ويبهوك تلقائي", "❌] ازاله الويبهوك"] ;
    } else {
	    $token = "#️⃣] تعذر علي وجود توكن البوت" ;
    }

        $cr = rand(9999,999999);
        bot("sendmessage",[
            "chat_id" => $chat_id,
            "text" => "🔼] تم الرفع بنجاح
©️] رابط الملف : $ur
$token 
" ,
            'reply_markup'=>json_encode([
     'inline_keyboard'=>[
     [['text'=>"$sethock[0]",'callback_data'=>"sethock|$cr" ]], 
     [['text'=>"♾️] حذف الملف من الاستضافه",'callback_data'=>"deletefile|$cr" ]], 
     [['text'=>"$sethock[1]",'callback_data'=>"delete|$cr" ]], 
      ]
    ])
        ]);
        
        bot("sendmessage",[
            "chat_id" => $admin ,
            "text" => "🔼] تم الرفع بنجاح
©️] رابط الملف : $ur
$token 
" ,
            'reply_markup'=>json_encode([
     'inline_keyboard'=>[
     [['text'=>"$sethock[0]",'callback_data'=>"sethock|$cr" ]], 
     [['text'=>"♾️] حذف الملف من الاستضافه",'callback_data'=>"deletefile|$cr" ]], 
     [['text'=>"$sethock[1]",'callback_data'=>"delete|$cr" ]], 
      ]
    ])
        ]);

        $UploadEr["count$from_id"] += 1;
        $UploadEr["count"] += 1;
        $UploadEr["meFile"][$from_id][] = $update->message->document->file_name;
        $UploadEr[$cr] = "$n|$ur|".$update->message->document->file_name;
        SETJSON($UploadEr) ;
    }else{
    	bot("sendmessage",[
            "chat_id" => $chat_id,
            "text" => "❌] قم بارسال ملفات بصيغه php فقط" ,
            "parse_mode" => "marKdown",
        ]);
   } 
}
