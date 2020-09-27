FROM fyui001/php-mecab-alpine

WORKDIR /root/bot

ADD bot/ .

RUN apk add --update --no-cache composer php-pdo php-pdo_mysql

CMD ["php", "bot.php"]