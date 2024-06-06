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

// Обновление статуса пользователя
function update_user_status($username, $status, $days)
{
    $absences = load_absences();
    $absences[$username] = [
        'status' => $status,
        'end_date' => date('Y-m-d', strtotime("+$days days"))
    ];
    save_absences($absences);
}

// Загрузка информации об отсутствии из файла
function load_absences()
{
    if (file_exists('absences.json')) {
        $json = file_get_contents('absences.json');
        return json_decode($json, true);
    }
    return [];
}

// Сохранение информации об отсутствии в файл
function save_absences($absences)
{
    file_put_contents('absences.json', json_encode($absences));
}

// Проверка статуса пользователя
function is_user_absent($username)
{
    $absences = load_absences();
    if (isset($absences[$username])) {
        $end_date = $absences[$username]['end_date'];
        if (strtotime($end_date) >= strtotime(date('Y-m-d'))) {
            return true;
        }
    }
    return false;
}

// Обработка входящих сообщений
function handle_incoming_message($message, $duty_schedule)
{
    global $token, $chatId;
    
    if (strpos($message['text'], '/change') === 0) {
        $parts = explode(' ', $message['text']);
        if (count($parts) >= 4) {
            $username = $parts[1];
            $status = $parts[2];
            $days = intval($parts[3]);
            
            // Обновить информацию о статусе пользователя
            update_user_status($username, $status, $days);
            
            $response_text = "Информация о пользователе {$username} обновлена: {$status} на {$days} дней.";
            send_message_to_telegram($token, $chatId, $response_text);
        } else {
            $response_text = "Неверный формат команды. Используйте: /change @username статус дни";
            send_message_to_telegram($token, $chatId, $response_text);
        }
    }
}

// Основная функция для отправки сообщения о дежурных
function send_duty_message($token, $chatId, $duty_schedule)
{
    $today = date('l');
    $schedule_for_today = $duty_schedule[$today] ?? null;

    if ($schedule_for_today) {
        foreach ($schedule_for_today as $role => $user) {
            if (is_user_absent($user)) {
                $schedule_for_today[$role] = $schedule_for_today[$role . '_substitute'] ?? $user;
            }
        }

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
}

// Загружаем входящие сообщения
function get_updates($token, $offset)
{
    $url = "https://api.telegram.org/bot$token/getUpdates?offset=$offset";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Основная функция
function main()
{
    global $token, $chatId, $duty_schedule;

    $offset = 0;
    while (true) {
        $updates = get_updates($token, $offset);
        if ($updates['ok'] && count($updates['result']) > 0) {
            foreach ($updates['result'] as $update) {
                $offset = $update['update_id'] + 1;
                if (isset($update['message'])) {
                    handle_incoming_message($update['message'], $duty_schedule);
                }
            }
        }
        sleep(1); // чтобы не перегружать сервер Telegram
    }
}

main();
