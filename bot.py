from telegram import Bot, ParseMode
from telegram.error import TelegramError
from datetime import datetime
import schedule
import time
import os
from dotenv import load_dotenv

<<<<<<< HEAD
# Загружаем переменные окружения из файла .env
load_dotenv()

BOT_TOKEN = os.getenv('BOT_TOKEN')
CHAT_ID = os.getenv('CHAT_ID')
=======
BOT_TOKEN = ''
CHAT_ID = ''
>>>>>>> 1b25c823de35aeed521093e969f54a267ff0aac1

if not BOT_TOKEN or not CHAT_ID:
    print('Please provide both BOT_TOKEN and CHAT_ID')
    exit(1)

# Расписание дежурств и замен
duty_schedule = {
    'Monday': {'db_duty': '@MitolKing', 'db_substitute': '@AnbozSultonov', 'frontend_tasks': '@komyobraufzoda', 'support_fixes': '@muhammad0002', 'substitute': '@UmedjonAliev'},
    'Tuesday': {'db_duty': '@AnbozSultonov', 'db_substitute': '@MitolKing', 'frontend_tasks': '@komyobraufzoda', 'support_fixes': '@golibemomov', 'substitute': '@eldor_juraev'},
    'Wednesday': {'db_duty': '@MitolKing', 'db_substitute': '@AnbozSultonov', 'frontend_tasks': '@komyobraufzoda', 'support_fixes': '@UmedjonAliev', 'substitute': '@hafizovabdullo'},
    'Thursday': {'db_duty': '@AnbozSultonov', 'db_substitute': '@MitolKing', 'frontend_tasks': '@komyobraufzoda', 'support_fixes': '@hafizovabdullo', 'substitute': '@muhammad0002'},
    'Friday': {'db_duty': '@MitolKing', 'db_substitute': '@AnbozSultonov', 'frontend_tasks': '@komyobraufzoda', 'support_fixes': '@eldor_juraev', 'substitute': '@AnbozSultonov'}
}

# Инициализация бота
bot = Bot(token=BOT_TOKEN)

# Функция для отправки сообщения о дежурном и замене
def send_duty_message():
    today = datetime.now().strftime('%A')
    schedule_for_today = duty_schedule.get(today)
    
    if schedule_for_today:
        message = (
            f"Сегодняшние дежурные:\n"
            f"— Дежурство по БД: {schedule_for_today['db_duty']}, замена {schedule_for_today['db_substitute']}\n"
            f"— Фронтенд задачи: {schedule_for_today['frontend_tasks']}\n"
            f"— Саппорт/багфиксы: {schedule_for_today['support_fixes']}\n"
            f"Замена задач фронтенда или саппорта при необходимости: {schedule_for_today['substitute']}"
        )
        try:
            sent_message = bot.send_message(chat_id=CHAT_ID, text=message, parse_mode=ParseMode.HTML)
            bot.pin_chat_message(chat_id=CHAT_ID, message_id=sent_message.message_id, disable_notification=True)
            print('Duty message sent and pinned successfully.')
        except TelegramError as e:
            print(f'Error sending duty message: {e}')
    else:
        print(f'No duty schedule for {today}.')

# Планирование задачи на каждый рабочий день в 09:05
for day in ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']:
    getattr(schedule.every(), day).at("16:18").do(send_duty_message)

# Бесконечный цикл для выполнения запланированных задач
print('Bot is running...')
while True:
    schedule.run_pending()
    time.sleep(1)