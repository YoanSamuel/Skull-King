import React, {useEffect, useState} from 'react';

export default function ({
                             announceValues,
                             gamePhase,
                             cards,
                             players,
                             gameId,
                             eventSourceUrl,
                             currentUserId,
                             error,
                             fold
                         }) {

    const [playersState, setPlayersState] = useState(players);
    const [gamePhaseState, setGamePhaseState] = useState(gamePhase);

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
        }
    }, [])


    function displayPlayerAnnounce(player) {
        if (!player.announce && player.announce !== 0) {

            return 'En attente...';
        }

        if (player.userId === currentUserId) {
            return player.announce;
        }

        if (gamePhaseState !== 'ANNOUNCE') {
            return player.announce;
        }

        return 'A voté, en attente des autres joueurs.';
    }

    function displayError(error) {
        if (error === true) {

            return ' Réessayes de proposer une annonce';
        }

    }

    console.log(error)

    return <div>


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
                <form key={card.id} action={`/game/${gameId}/play/${card.id}`} method="POST">
                    <button type="submit"> {card.cardType}</button>
                </form>
                : <span key={card.id}>{card.id} {card.cardType}</span>
        })
        }
    </div>

}

