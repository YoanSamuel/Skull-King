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
                            }
                        }

                        return player;
                    })
                })
                setGamePhaseState(data.gamePhase)
            }

            if (data.status === 'player_play_card') {
                setSkullState((actualSkull) => {
                    return actualSkull.map((skull) => {
                        if (skull.id === data.id) {
                            return {
                                ...cards,
                                fold: data.fold
                            }
                        }
                        return skull;

                    })
                })
                setSkullState(data.gamePhase)
            }
        }
    }, [])


    function displayPlayerAnnounce(player) {
        if (!player.announce && player.announce !== 0) {

            return 'En attente...';
        }

        if (player.userId === playerId) {
            return player.announce;
        }

        if (gamePhaseState !== 'ANNOUNCE') {
            return player.announce;
        }

        return 'A voté, en attente des autres joueurs.';
    }


    return <div>
        {console.log(skull[0].fold[0])}
        {console.log(skull[0].id)}
        {
            playersState.map((player) => {
                return <div key={player.id}>
                    <p>{player.name}</p>
                    <p>annonce: {displayPlayerAnnounce(player)}</p>
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
        {cards.map((card) => {
            return (gamePhaseState === 'PLAYCARD') ?
                <form key={card.id} action={`/game/${gameId}/player/${playerId}/playcard/${card.id}`} method="POST">
                    <button type="submit"> {card.cardType}</button>
                </form>
                : <span key={card.id}>{card.id} {card.cardType}</span>
        })
        }

        <h2>LA FOLD DE SES MORTS </h2>


    </div>

}

