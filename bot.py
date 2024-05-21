from telegram import Bot, ParseMode
from telegram.error import TelegramError
from datetime import datetime
import schedule
import time

BOT_TOKEN = '492308814:AAGVy3y1t5xb6OroUfSLPlFwOeXArG3K8OI'
CHAT_ID = '-1001416481750'

if not BOT_TOKEN or not CHAT_ID:
    print('Please provide both BOT_TOKEN and CHAT_ID')
    exit(1)

# Расписание дежурств и замен

   
duty_schedule = {
    'Monday': {
        'db_duty': '@MitolKing',
        'db_substitute': '@AnbozSultonov',
        'frontend_tasks': '@komyobraufzoda',
        'support_fixes': '@muhammad0002',
        'substitute': '@UmedjonAliev'
    },
    'Tuesday': {
        'db_duty': '@AnbozSultonov',
        'db_substitute': '@MitolKing',
        'frontend_tasks': '@komyobraufzoda',
        'support_fixes': '@golibemomov',
        'substitute': '@eldor_juraev'
    },
    'Wednesday': {
        'db_duty': '@MitolKing',
        'db_substitute': '@AnbozSultonov',
        'frontend_tasks': '@komyobraufzoda',
        'support_fixes': '@UmedjonAliev',
        'substitute': '@hafizovabdullo'
    },
    'Thursday': {
        'db_duty': '@AnbozSultonov',
        'db_substitute': '@MitolKing',
        'frontend_tasks': '@komyobraufzoda',
        'support_fixes': '@hafizovabdullo',
        'substitute': '@muhammad0002'
    },
    'Friday': {
        'db_duty': '@MitolKing',
        'db_substitute': '@AnbozSultonov',
        'frontend_tasks': '@komyobraufzodaб',
        'support_fixes': '@eldor_juraev',
        'substitute': '@AnbozSultonov'
    }
}

# Функция для определения дежурного и замены на сегодняшний день
def get_duty_schedule():
    today = datetime.now().strftime('%A')  # Определяем текущий день недели
    schedule_for_today = duty_schedule.get(today)
    if schedule_for_today:
        return schedule_for_today
    else:
        print(f'No duty schedule for {today}.')
        return None

# Инициализация бота
bot = Bot(token=BOT_TOKEN)

# Функция для отправки сообщения о дежурном и замене
def send_duty_message():
    schedule = get_duty_schedule()
    if schedule:
        message = (
            f"Сегодняшние дежурные:\n"
            f"— Дежурство по БД: {schedule['db_duty']}, замена {schedule['db_substitute']}\n"
            f"— Фронтенд задачи: {schedule['frontend_tasks']}\n"
            f"— Саппорт/багфиксы: {schedule['support_fixes']}\n"
            f"Замена задач фронтенда или саппорта при необходимости: {schedule['substitute']}"
        )
        try:
            sent_message = bot.send_message(chat_id=CHAT_ID, text=message, parse_mode=ParseMode.HTML)
            bot.pin_chat_message(chat_id=CHAT_ID, message_id=sent_message.message_id, disable_notification=True)
            print('Duty message sent and pinned successfully.')
        except TelegramError as e:
            print(f'Error sending duty message: {e}')
    else:
        print('No duty schedule for today.')

# Планирование задачи на каждый день в 09:05
schedule.every().monday.at("09:05").do(send_duty_message)
schedule.every().tuesday.at("09:05").do(send_duty_message)
schedule.every().wednesday.at("09:05").do(send_duty_message)
schedule.every().thursday.at("09:05").do(send_duty_message)
schedule.every().friday.at("09:05").do(send_duty_message)

# Бесконечный цикл для выполнения запланированных задач
print('Bot is running...')
while True:
    schedule.run_pending()
    time.sleep(1)
