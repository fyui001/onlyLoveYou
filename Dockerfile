FROM fyui001/php-mecab-alpine

WORKDIR /code/bot

RUN apk add --update --no-cache composer php-pdo php-pdo_mysql

CMD ["php", "bot.php"]