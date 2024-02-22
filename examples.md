# Examples


## add new record
``` javascript
fetch('https://gagikpog.ru/leadboard/add.php', {
    method: 'POST',
    body: JSON.stringify({
        // identifier of user
        identifier: '0000-000',
        // game name
        game: 'calendar',
        // scores for save
        score: 50,
        // custom data
        meta: JSON.stringify({'image': 'img_url', name: 'Гагик'})
    })
});
```


## get top users
``` javascript
fetch('https://gagikpog.ru/leadboard/getTop.php', {
    method: 'POST',
    body: JSON.stringify({
        // game name
        game: 'calendar',
        // limit of top (optional, default = 10)
        limit: 10,
        // sorting (optional, default = 'DESC')
        order: 'ASC'
    })
});
```

