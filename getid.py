from telegram import Update, Bot
from telegram.ext import Updater, CommandHandler, CallbackContext

BOT_TOKEN = '1245678'

# Инициализация бота и диспетчера
updater = Updater(token=BOT_TOKEN, use_context=True)
dispatcher = updater.dispatcher

# Функция для обработки команды /id
def id_command(update: Update, context: CallbackContext):
    chat_id = update.effective_chat.id
    context.bot.send_message(chat_id=chat_id, text=f"Chat ID: {chat_id}")

# Добавляем обработчик команды /id
id_handler = CommandHandler('id', id_command)
dispatcher.add_handler(id_handler)

# Запуск бота
updater.start_polling()
updater.idle()
