import React, {useEffect, useState} from 'react';

/**
 *
 * @param announceValues
 * @param cards
 * @param eventSourceUrl
 * @param playerId
 * @param {{players, gameState, fold, id}} skull
 * @returns {Element}
 */
export default function ({
                             announceValues,
                             cards,
                             eventSourceUrl,
                             playerId,
                             skull,

                         }) {

    const [skullState, setSkullState] = useState(skull);
    const [cardsState, setCardsState] = useState(cards);
    const [blockPlayCard, setBlockPlayCard] = useState(false);
    const onPlayerAnnounced = (data) => {

        setSkullState((oldSkull) => ({
            ...oldSkull,
            gameState: data.gamePhase,
            players: oldSkull.players.map((player) => {
                if (player.userId === data.userId) {
                    return {
                        ...player,
                        announce: parseInt(data.announce),
                        score: data.score,
                    }
                }

                return player;
            })
        }));
    }

    const onCardPlayed = (data) => {

        setSkullState((oldSkull) => {
            const t = ({
                ...data.skull,
                fold: oldSkull.fold.concat({id: data.cardId, playerId: data.playerId})
            });
            console.log('setSkullState', t, oldSkull);
            return t;
        });
        setCardsState((cards) => cards.filter((card) => card.id !== data.cardId || card.playerId !== data.playerId));
        if (data.skull.fold.length === 0) {
            setBlockPlayCard(true);
            window.setTimeout(() => {
                setSkullState(data.skull);
                setBlockPlayCard(false);
            }, 5000)
        }


    }

    useEffect(() => {
        const eventSource = new EventSource(eventSourceUrl);
        eventSource.onmessage = event => {
            // Will be called every time an update is published by the server
            console.log(JSON.parse(event.data));
            const data = JSON.parse(event.data);
            if (data.status === 'player_announced') {
                onPlayerAnnounced(data);

            }

            if (data.status === 'player_play_card') {
                onCardPlayed(data);

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

        if (skullState.gameState !== 'ANNOUNCE') {
            return `Annonce : ${player.announce}, Score : ${player.score}`;
        }

        return 'A voté, en attente des autres joueurs.';
    }


    async function playCard(playerId, card) {

        const url = `/game/${skullState.id}/player/${playerId}/playcard/${card.id}`;
        const response = await fetch(url, {
            method: "POST",
        });

        const body = await response.json();
        if (!response.ok) {
            //todo player display message error
            console.log(body);
            throw new Error('Issue fetching play card');
        }


    }

    return <div>

        {
            skullState.players.map((player) => {
                return <div key={player.id}>
                    <p>{player.name}</p>
                    <p>{player.id}</p>
                    <p>Annonce: {displayPlayerAnnounce(player)}</p>
                </div>
            })
        }
        {
            (skullState.gameState === 'ANNOUNCE') && announceValues.map((announce) => {
                return <form key={announce} action={`/game/${skullState.id}/announce/${announce}`} method="POST">
                    <button type="submit"> {announce} </button>
                </form>
            })

        }
        <p> Votre main : </p>
        <div id="player_hand">
            {cardsState.map((card, index) => {
                return (skullState.gameState === 'PLAYCARD') ?
                    <form key={`${card.id}_${index}`}
                          onSubmit={(event) => {
                              event.preventDefault();
                              playCard(playerId, card);
                          }}>
                        <button type="submit" disabled={blockPlayCard}> {card.id}</button>
                    </form>
                    : <span key={`${card.id}_${index}`}>{card.id} </span>
            })
            }
        </div>
        <div>
            <h2>LA FOLD DE SES MORTS</h2>
            <ul>
                {skullState.fold.map((card) =>
                    (
                        <li key={card.id}>
                            {card.playerId}: {card.id}
                        </li>
                    ))}
            </ul>
        </div>

    </div>

}

