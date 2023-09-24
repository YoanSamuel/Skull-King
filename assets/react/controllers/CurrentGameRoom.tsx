import {FC, useEffect, useState} from 'react';

type User = {
    userId: string;
    userName: string;
}

type Props = {
    users: User[];
    skullkingid: string;
    pathEnterGameRoom: string;
    pathCurrentGame: string;
    eventSourceUrl: string;
}

const CurrentGameRoom: FC<Props> = ({users, skullkingid, pathEnterGameRoom, pathCurrentGame, eventSourceUrl}) => {

    const [usersState, setUsersState] = useState(users);

    useEffect(() => {
        const eventSource = new EventSource(eventSourceUrl);
        eventSource.onmessage = event => {
            // Will be called every time an update is published by the server
            console.log(JSON.parse(event.data));
            const data = JSON.parse(event.data);
            if (data.status === 'player_joined') {
                setUsersState((actualUsers) => {
                    console.log(actualUsers)
                    return actualUsers.concat(data.user)
                })
            }
            if (data.status === 'game_started') {
                window.location.href = data.game_url;
            }
        }
    }, [])

    return <div className="players-list">
        <ul id="player-list">

            {usersState.map((user) => {
                return <li key={user.userId} id="player-names">
                    {user.userName}
                </li>
            })}


        </ul>
        {
            usersState.length > 1
                ? (!skullkingid
                    ? <form action={pathEnterGameRoom} method="POST">
                        <button type="submit" id="launch-game" className="button"> Démarrer la partie</button>
                    </form>
                    : <a href={pathCurrentGame} id="join-game" className="button">
                        Rejoindre la partie en cours
                    </a>)
                : undefined
        }

    </div>;
}

export default CurrentGameRoom;