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

// Функция для отправки сообщения о дежурном и замене
function send_duty_message($token, $chitId, $duty_schedule)
{
    $today = date('l');
    $schedule_for_today = $duty_schedule[$today] ?? null;

    if ($schedule_for_today) {
        $message = "Сегодняшние дежурные:\n";
        $message .= "— Дежурство по БД: " . $schedule_for_today['db_duty'] . ", замена " . $schedule_for_today['db_substitute'] . "\n";
        $message .= "— Фронтенд задачи: " . $schedule_for_today['frontend_tasks'] . "\n";
        $message .= "— Саппорт/багфиксы: " . $schedule_for_today['support_fixes'] . "\n";
        $message .= "Замена задач фронтенда или саппорта при необходимости: " . $schedule_for_today['substitute'];

        $message = "chat_id=$chitId&text=$message";

        file_get_contents("https://api.telegram.org/bot$token/sendMessage?$message");
    }
}

send_duty_message($token, $chitId, $duty_schedule);
