import React, { useEffect, useState } from 'react';
import './App.css';
import { IGame, IItem, IResponse } from './types';
import { Calendar } from './components/calendar';

const apiRoot = 'https://gagikpog.ru/leadboard/';

export default function App() {

    const [games, setGames] = useState<IGame[]>([]);
    const [gameType, setGameType] = useState('');
    const [mode, setMode] = useState('default');
    const [order, setOrder] = useState('DESC');
    const [loadingGames, setLoadingGames] = useState(true);
    const [loading, setLoading] = useState(false);
    const [gamesError, setGamesError] = useState('');
    const [error, setError] = useState('');
    const [retryGames, setRetryGames] = useState(0);
    const [retryRating, setRetryRating] = useState(0);
    const [data, setData] = useState<IItem[]>([]);


    useEffect(() => {
        const controller = new AbortController();
        setLoadingGames(true);
        setGamesError('');
        fetch(`${apiRoot}getGames.php`, { signal: controller.signal })
            .then(async (response) => {
                const result: IResponse<IGame[]> = await response.json();
                if (!response.ok || result.status !== 'done' || !Array.isArray(result.data)) {
                    throw new Error(result.message || 'Не удалось загрузить игры.');
                }
                if (controller.signal.aborted) return;
                setGames(result.data);
                const initial = result.data.find((game) => game.id === 'calendar') || result.data[0];
                setGameType(initial?.id || '');
                setMode(initial?.id === 'minesweeper' ? 'beginner' : 'default');
                setOrder(initial?.id === 'minesweeper' ? 'ASC' : 'DESC');
            })
            .catch((failure) => {
                if (!controller.signal.aborted) setGamesError(failure.message || 'Не удалось загрузить игры.');
            })
            .finally(() => {
                if (!controller.signal.aborted) setLoadingGames(false);
            });
        return () => controller.abort();
    }, [retryGames]);

    useEffect(() => {
        if (!gameType) return;
        const controller = new AbortController();
        setData([]);
        setLoading(true);
        setError('');
        fetch(`${apiRoot}getTop.php`, {
            method: 'POST',
            signal: controller.signal,
            body: JSON.stringify({ game: gameType, mode, order, limit: 100 })
        }).then(async (response) => {
            const result: IResponse<IItem[]> = await response.json();
            if (!response.ok || result.status !== 'done' || !Array.isArray(result.data)) {
                throw new Error(result.message || 'Не удалось загрузить рейтинг.');
            }
            if (!controller.signal.aborted) setData(result.data);
        }).catch((failure) => {
            if (!controller.signal.aborted) setError(failure.message || 'Не удалось загрузить рейтинг.');
        }).finally(() => {
            if (!controller.signal.aborted) setLoading(false);
        });
        return () => controller.abort();
    }, [gameType, mode, order, retryRating]);

    return (
        <div className="App bg-default">
            <header className='p-8 flex bg-header leaderboard-controls'>
                <div className='text-upper '>LeadBoard</div>
                <select
                    className='ml-8'
                    aria-label='Игра'
                    value={gameType}
                    disabled={loadingGames || games.length === 0}
                    onChange={(event) => {
                        const game = event.target.value;
                        setGameType(game);
                        setMode(game === 'minesweeper' ? 'beginner' : 'default');
                        setOrder(game === 'minesweeper' ? 'ASC' : 'DESC');
                    }}
                >
                    {games.map((game) => (
                        <option key={game.id} value={game.id}>{game.name}</option>
                    ))}
                </select>
                {gameType === 'minesweeper' && (
                    <select aria-label="Сложность" value={mode} onChange={(event) => setMode(event.target.value)}>
                        <option value="beginner">Новичок</option>
                        <option value="intermediate">Любитель</option>
                        <option value="advanced">Профессионал</option>
                    </select>
                )}
                <select aria-label="Порядок рейтинга" value={order} disabled={!gameType}
                    onChange={(event) => setOrder(event.target.value)}>
                    <option value="DESC">Больше — лучше</option>
                    <option value="ASC">Меньше — лучше</option>
                </select>
            </header>
            <main className="m-8">
                {(loadingGames || loading) && <p role="status">Загрузка…</p>}
                {gamesError && <div role="alert"><p>{gamesError}</p>
                    <button onClick={() => setRetryGames((value) => value + 1)}>Повторить</button></div>}
                {error && <div role="alert"><p>{error}</p>
                    <button onClick={() => setRetryRating((value) => value + 1)}>Повторить</button></div>}
                {!loadingGames && !gamesError && games.length === 0 && <p>Игры пока не добавлены.</p>}
                {!loadingGames && !loading && !gamesError && !error && gameType && (
                    data.length === 0 ? <p>В этом режиме пока нет результатов.</p> : (
                    <div className="grid grid-gap-8 grid-columns-3 leaderboard-results">
                        <strong>Место</strong><strong>Игрок</strong>
                        <strong>{gameType === 'minesweeper' ? 'Время, с' : 'Очки'}</strong>
                {
                    data.map((item, index) => {
                        return (
                            <div key={item.identifier} className='contents'>
                                {gameType === 'calendar' ? <Calendar item={item} index={index}/> : (
                                    <><div>{index + 1}</div>
                                        <div className="player-name">{item.identifier}</div>
                                        <div>{item.score}</div></>
                                )}
                            </div>
                        );
                    })
                }
                    </div>
                    )
                )}
            </main>
        </div>
    );
}
