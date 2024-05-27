<?php

$BOT_TOKEN = '492308814:AAGVy3y1t5xb6OroUfSLPlFwOeXArG3K8OI';
$CHAT_ID = '-819383773';

if (!$BOT_TOKEN || !$CHAT_ID) {
    echo 'Please provide both BOT_TOKEN and CHAT_ID';
    exit(1);
}

// Расписание дежурств и замен
$duty_schedule = [
    'Monday' => ['db_duty' => '@MitolKing', 'db_substitute' => '@AnbozSultonov', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@muhammad0002', 'substitute' => '@UmedjonAliev'],
    'Tuesday' => ['db_duty' => '@AnbozSultonov', 'db_substitute' => '@MitolKing', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@golibemomov', 'substitute' => '@eldor_juraev'],
    'Wednesday' => ['db_duty' => '@MitolKing', 'db_substitute' => '@AnbozSultonov', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@UmedjonAliev', 'substitute' => '@hafizovabdullo'],
    'Thursday' => ['db_duty' => '@AnbozSultonov', 'db_substitute' => '@MitolKing', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@hafizovabdullo', 'substitute' => '@muhammad0002'],
    'Friday' => ['db_duty' => '@MitolKing', 'db_substitute' => '@AnbozSultonov', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@eldor_juraev', 'substitute' => '@AnbozSultonov']
];

// Функция для отправки сообщения о дежурном и замене
function send_duty_message($chat_id, $duty_schedule)
{
    $today = date('l');
    $schedule_for_today = $duty_schedule[$today] ?? null;

    if ($schedule_for_today) {
        $message = "Сегодняшние дежурные:\n";
        $message .= "— Дежурство по БД: " . $schedule_for_today['db_duty'] . ", замена " . $schedule_for_today['db_substitute'] . "\n";
        $message .= "— Фронтенд задачи: " . $schedule_for_today['frontend_tasks'] . "\n";
        $message .= "— Саппорт/багфиксы: " . $schedule_for_today['support_fixes'] . "\n";
        $message .= "Замена задач фронтенда или саппорта при необходимости: " . $schedule_for_today['substitute'];

        $message = urldecode($message);
        
        $url = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage?chat_id=$CHAT_ID&text=$message";
        
        // Initialize cURL session
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Execute cURL session
        $response = curl_exec($ch);
        
        // Check for errors
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        
        // Close cURL session
        curl_close($ch);
        
        // Output response
        echo $response;
    }
}
