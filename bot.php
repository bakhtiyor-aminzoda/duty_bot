<?php

$token = '492308814:AAGVy3y1t5xb6OroUfSLPlFwOeXArG3K8OI';
$chitId = '-819383773';


// Расписание дежурств и замен
$duty_schedule = [
    'Monday' => ['db_duty' => '@MitolKing', 'db_substitute' => '@AnbozSultonov', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@muhammad0002', 'substitute' => '@UmedjonAliev'],
    'Tuesday' => ['db_duty' => '@AnbozSultonov', 'db_substitute' => '@MitolKing', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@golibemomov', 'substitute' => '@eldor_juraev'],
    'Wednesday' => ['db_duty' => '@MitolKing', 'db_substitute' => '@AnbozSultonov', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@UmedjonAliev', 'substitute' => '@hafizovabdullo'],
    'Thursday' => ['db_duty' => '@AnbozSultonov', 'db_substitute' => '@MitolKing', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@hafizovabdullo', 'substitute' => '@muhammad0002'],
    'Friday' => ['db_duty' => '@MitolKing', 'db_substitute' => '@AnbozSultonov', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@eldor_juraev', 'substitute' => '@AnbozSultonov']
];

function format_duty_message($schedule_for_today)
{
    $message = [
        "Сегодняшние дежурные:",
        "- Дежурство по БД: " . $schedule_for_today['db_duty'] . ", замена " . $schedule_for_today['db_substitute'],
        "- Фронтенд задачи: " . $schedule_for_today['frontend_tasks'],
        "- Саппорт/багфиксы: " . $schedule_for_today['support_fixes'],
        "Замена задач фронтенда или саппорта при необходимости: " . $schedule_for_today['substitute']
    ];
    return implode("\n", $message);
}

function send_message_to_telegram($token, $chatId, $message)
{
    $encoded_message = urlencode($message);
    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chatId&text=$encoded_message";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

function send_duty_message($token, $chitId, $duty_schedule)
{
    $today = date('l');
    $schedule_for_today = $duty_schedule[$today] ?? null;

    if ($schedule_for_today) {
        $message = format_duty_message($schedule_for_today);
        $response = send_message_to_telegram($token, $chitId, $message);

        if ($response && $response['ok']) {
            echo "Сообщение успешно отправлено в Telegram.";
        } else {
            echo "Ошибка при отправке сообщения в Telegram.";
        }
    } else {
        echo "На сегодня расписание дежурств не найдено.";
    }
}

send_duty_message($token, $chitId, $duty_schedule);