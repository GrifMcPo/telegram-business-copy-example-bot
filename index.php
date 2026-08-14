<?php

// Конфигурация
$token = "8864574395:AAHZ0T0sJToQIef_DvKPJl_c4EvXd3zuk6A";
$admin = "8889631346"; // Узнай у @userinfobot

// Получаем обновление от Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Обрабатываем бизнес-сообщения
if (isset($update['business_message'])) {
    $message = $update['business_message'];
    $chat_id = $message['chat']['id'];
    $message_id = $message['message_id'];
    $text = $message['text'] ?? '';
    $user_id = $message['from']['id'] ?? 0;
    
    // Проверяем, что сообщение пришло от админа (тебя)
    if ($user_id != $admin) {
        // Если кто-то другой написал — игнорируем
        http_response_code(200);
        exit;
    }
    
    // Проверяем точную команду .test
    if (trim($text) === ".test") {
        // 1. Удаляем сообщение с командой
        $delete_url = "https://api.telegram.org/bot{$token}/deleteMessage";
        $delete_data = [
            'chat_id' => $chat_id,
            'message_id' => $message_id
        ];
        
        $ch = curl_init($delete_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($delete_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        
        // 2. Отправляем ответ "Бот работает"
        $send_url = "https://api.telegram.org/bot{$token}/sendMessage";
        $send_data = [
            'chat_id' => $chat_id,
            'text' => "✅ Бот работает",
            'parse_mode' => 'HTML'
        ];
        
        $ch = curl_init($send_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($send_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}

// Обязательный ответ для Telegram (200 OK)
http_response_code(200);
?>
