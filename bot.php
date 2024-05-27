<?php

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$BOT_TOKEN = getenv('BOT_TOKEN');
$CHAT_ID = getenv('CHAT_ID');

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

        $url = 'https://api.telegram.org/bot' . $BOT_TOKEN . '/sendMessage';
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result !== false) {
            echo 'Duty message sent and pinned successfully.';
        } else {
            echo 'Error sending duty message.';
        }
    } else {
        echo 'No duty schedule for ' . $today;
    }
}

// Отправка сообщения
send_duty_message($CHAT_ID, $duty_schedule);

?>
