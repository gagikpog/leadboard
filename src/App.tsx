import React, { useEffect, useState } from 'react';
// import logo from './logo.svg';
// {/* <img src={logo} className="App-logo" alt="logo" /> */}
import './App.css';
import { IItem, IResponse, TGameType } from './types';
import { Calendar } from './components/calendar';

const components = {
    calendar: Calendar
};

export default function App() {

    const [gameType, setGameType] = useState<TGameType>('calendar');
    const [data, setData] = useState<IItem[]>([]);


    useEffect(() => {
        fetch('https://gagikpog.ru/leadboard/getTop.php', {
            method: 'POST',
            body: JSON.stringify({
                game: gameType,
                limit: 100
            })
        }).then((res) => {
            return res.json();
        }).then((res: IResponse<IItem[]>) => {
            setData(res.data);
        });
        
    }, [gameType]);

    const Child = components[gameType];

    return (
        <div className="App bg-default">
            <header className='p-8 flex bg-header'>
                <div className='text-upper '>LeadBoard</div>
                <select
                    className='ml-8'
                    aria-label='Игра'
                    value={gameType}
                    onChange={(event) => setGameType(event.target.value as TGameType)}
                >
                    {(Object.keys(components) as TGameType[]).map((game) => (
                        <option key={game} value={game}>{game}</option>
                    ))}
                </select>
            </header>
            <main className='grid grid-gap-8 grid-columns-3 m-8'>
                {
                    data.map((item, index) => {
                        return (
                            <div key={item.identifier} className='contents'>
                                <Child item={item} index={index}/>
                            </div>
                        );
                    })
                }
            </main>
        </div>
    );
}
