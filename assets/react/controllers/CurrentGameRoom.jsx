import React, {useEffect, useState} from 'react';

export default function ({users, skullkingid, pathEnterGameRoom, pathCurrentGame, eventSourceUrl}) {
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

    return <div>
        <ul>

            {usersState.map((user) => {
                return <li key={user.userId}>
                    {user.userName}
                </li>
            })}


        </ul>
        {
            usersState.length > 1
                ? (!skullkingid
                    ? <form action={pathEnterGameRoom} method="POST">
                        <button type="submit"> Démarrer la partie</button>
                    </form>
                    : <a href={pathCurrentGame}>Rejoindre la partie en cours</a>)
                : undefined
        }

    </div>;
}

