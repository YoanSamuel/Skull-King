import React, {useEffect, useState} from 'react';

export default function ({
                             announceValues,
                             gamePhase,
                             cards,
                             players,
                             gameId,
                             eventSourceUrl,
                             playerId,
                             skull
                         }) {

    const [playersState, setPlayersState] = useState(players);
    const [gamePhaseState, setGamePhaseState] = useState(gamePhase);
    const [skullState, setSkullState] = useState(skull);
    const [foldData, setFoldData] = useState([]);
    const [playersPlayed, setPlayersPlayed] = useState([]);

    useEffect(() => {
        const eventSource = new EventSource(eventSourceUrl);
        eventSource.onmessage = event => {
            // Will be called every time an update is published by the server
            console.log(JSON.parse(event.data));
            const data = JSON.parse(event.data);
            if (data.status === 'player_announced') {
                setPlayersState((actualPlayers) => {
                    console.log(actualPlayers)
                    return actualPlayers.map((player) => {
                        if (player.userId === data.userId) {
                            return {
                                ...player,
                                announce: parseInt(data.announce),
                                score: data.score,
                            }
                        }

                        return player;
                    })
                })
                setGamePhaseState(data.gamePhase)

            }

            if (data.status === 'player_play_card') {
                setSkullState(data.skull);
                setPlayersPlayed((prevPlayersPlayed) => {
                    if (!prevPlayersPlayed.includes(data.userId)) {
                        return [...prevPlayersPlayed, data.userId];
                    }
                    return prevPlayersPlayed;
                });

                setFoldData(data.fold); // Update fold data

            }
        };
    }, [])


    function displayPlayerAnnounce(player) {
        if (!player.announce && player.announce !== 0) {
            return 'En attente...';
        }

        if (player.userId === playerId) {
            return `Annonce : ${player.announce}, Score : ${player.score}`;
        }

        if (gamePhaseState !== 'ANNOUNCE') {
            return `Annonce : ${player.announce}, Score : ${player.score}`;
        }

        return 'A voté, en attente des autres joueurs.';
    }


    return <div>

        {
            playersState.map((player) => {
                return <div key={player.id}>
                    <p>{player.name}</p>
                    <p>{player.id}</p>
                    <p>Score : {player.score}</p>
                    <p>{displayPlayerAnnounce(player)}</p>
                </div>
            })
        }
        {
            (gamePhaseState === 'ANNOUNCE') && announceValues.map((announce) => {
                return <form key={announce} action={`/game/${gameId}/announce/${announce}`} method="POST">
                    <button type="submit"> {announce} </button>
                </form>
            })

        }
        <p> Votre main : </p>
        <div id="player_hand">
            {cards.map((card, index) => {
                return (gamePhaseState === 'PLAYCARD') ?
                    <form key={`${card.id}_${index}`} action={`/game/${gameId}/player/${playerId}/playcard/${card.id}`}
                          method="POST">
                        <button type="submit"> {card.id}</button>
                    </form>
                    : <span key={`${card.id}_${index}`}>{card.id} </span>
            })
            }
        </div>
        <div>
            <h2>LA FOLD DE SES MORTS</h2>
            <ul>
                {foldData.map((card) => {
                    return (
                        <li key={card.card_id}>
                            {card.player_name}: {card.card_id}
                        </li>
                    );
                })}
            </ul>
        </div>

    </div>

}

