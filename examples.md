# Примеры LeadBoard API

[Описание API и параметров →](README.md#api)

Каждый JavaScript-пример можно отдельно вставить в консоль браузера и увидеть ответ.
Примеры сохранения добавляют настоящие записи в БД.

В POST-запросах JSON передаётся с `Content-Type: text/plain;charset=UTF-8`, чтобы запрос с другого домена не требовал предварительного `OPTIONS`.

## Список игр

```javascript
fetch('https://gagikpog.ru/leadboard/getGames.php')
    .then(response => response.json())
    .then(result => console.log(result))
    .catch(error => console.error(error));
```

В `data` возвращаются игры: `id` для запросов и `name` для отображения.

## Календарь: сохранить результат

```javascript
fetch('https://gagikpog.ru/leadboard/add.php', {
    method: 'POST',
    headers: { 'Content-Type': 'text/plain;charset=UTF-8' },
    body: JSON.stringify({
        identifier: '00000000-0000-4000-8000-000000000001',
        game: 'calendar',
        score: 50,
        meta: JSON.stringify({ name: 'Гагик', image: 'image-id' })
    })
})
    .then(response => response.json())
    .then(result => console.log(result))
    .catch(error => console.error(error));
```

Без `mode` используется `default`. `meta` передаётся JSON-строкой, а не объектом.
Для всех попыток игрока используйте один и тот же `identifier`.

## Новая игра

Сначала зарегистрируйте игру в БД:

```sql
INSERT INTO `games` (`id`, `name`)
VALUES ('new-game', 'Новая игра');
```

Сохранение попытки:

```javascript
fetch('https://gagikpog.ru/leadboard/add.php', {
    method: 'POST',
    headers: { 'Content-Type': 'text/plain;charset=UTF-8' },
    body: JSON.stringify({
        game: 'new-game',
        identifier: 'player-42',
        score: 250,
        mode: 'default',
        meta: JSON.stringify({ name: 'Игрок 42' })
    })
})
    .then(response => response.json())
    .then(result => console.log(result))
    .catch(error => console.error(error));
```

Чтение рейтинга:

```javascript
fetch('https://gagikpog.ru/leadboard/getTop.php', {
    method: 'POST',
    headers: { 'Content-Type': 'text/plain;charset=UTF-8' },
    body: JSON.stringify({
        game: 'new-game',
        mode: 'default',
        order: 'DESC',
        limit: 20
    })
})
    .then(response => response.json())
    .then(result => console.log(result))
    .catch(error => console.error(error));
```

Для игры на минимальное время замените `order` на `ASC`.
Для независимых рейтингов режимов передавайте разные `mode` при сохранении и чтении.

## Как читать ответ

- `status: 'done'` — операция выполнена.
- `status: 'error'` — ошибка, описание в `message`.
- `data` — список игр или результатов; при успешном сохранении этого поля нет.
- `data: []` — список пуст.

Примеры выводят JSON-ответ целиком. `fetch` не отклоняет Promise из-за HTTP-ошибок, а `.catch()` не обрабатывает `status: 'error'` автоматически. При внедрении проверяйте HTTP-статус и `result.status`, как описано в [README](README.md#обработка-ошибок).

Повторное сохранение создаёт новую попытку. В рейтинге остаётся лучший результат каждого игрока для выбранных `game`, `mode` и `order`.
