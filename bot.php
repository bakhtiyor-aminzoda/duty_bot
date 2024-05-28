<?php

// Получаем токен и chat ID из переменных окружения
$token = getenv('BOT_TOKEN');
$chatId = getenv('CHAT_ID');

// Расписание дежурств и замен
$duty_schedule = [
    'Monday' => ['db_duty' => '@MitolKing', 'db_substitute' => '@AnbozSultonov', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@muhammad0002', 'substitute' => '@UmedjonAliev'],
    'Tuesday' => ['db_duty' => '@AnbozSultonov', 'db_substitute' => '@MitolKing', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@golibemomov', 'substitute' => '@eldor_juraev'],
    'Wednesday' => ['db_duty' => '@MitolKing', 'db_substitute' => '@AnbozSultonov', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@UmedjonAliev', 'substitute' => '@hafizovabdullo'],
    'Thursday' => ['db_duty' => '@AnbozSultonov', 'db_substitute' => '@MitolKing', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@hafizovabdullo', 'substitute' => '@muhammad0002'],
    'Friday' => ['db_duty' => '@MitolKing', 'db_substitute' => '@AnbozSultonov', 'frontend_tasks' => '@komyobraufzoda', 'support_fixes' => '@eldor_juraev', 'substitute' => '@AnbozSultonov']
];

// Функция для форматирования сообщения
function format_duty_message($schedule_for_today)
{
    return implode("\n", [
        "Сегодняшние дежурные:",
        "- Дежурство по БД: {$schedule_for_today['db_duty']}, замена {$schedule_for_today['db_substitute']}",
        "- Фронтенд задачи: {$schedule_for_today['frontend_tasks']}",
        "- Саппорт/багфиксы: {$schedule_for_today['support_fixes']}",
        "Замена задач фронтенда или саппорта при необходимости: {$schedule_for_today['substitute']}"
    ]);
}

// Функция для отправки сообщения в Telegram
function send_message_to_telegram($token, $chatId, $message)
{
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = ['chat_id' => $chatId, 'text' => $message];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    return json_decode($response, true);
}

// Основная функция для отправки сообщения о дежурных
function send_duty_message($token, $chatId, $duty_schedule)
{
    // Проверяем текущее время в Душанбе
    $now = new DateTime('now', new DateTimeZone('Asia/Dushanbe'));
    $current_hour = (int)$now->format('H');
    $current_minute = (int)$now->format('i');

    // Если текущее время 09:00, то отправляем сообщение
    if ($current_hour === 11 && $current_minute === 35) {
        $today = $now->format('l');
        $schedule_for_today = $duty_schedule[$today] ?? null;

        if ($schedule_for_today) {
            $message = format_duty_message($schedule_for_today);
            $response = send_message_to_telegram($token, $chatId, $message);

            if ($response && $response['ok']) {
                echo "Сообщение успешно отправлено в Telegram.";
            } else {
                echo "Ошибка при отправке сообщения в Telegram.";
            }
        } else {
            echo "На сегодня расписание дежурств не найдено.";
        }
    } else {
        echo "Сейчас не время отправки сообщения.";
    }
}

send_duty_message($token, $chatId, $duty_schedule);
