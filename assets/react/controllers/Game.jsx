import React, {useEffect, useState} from 'react';

/**
 *
 * @param announceValues
 * @param eventSourceUrl
 * @param currentUserId
 * @param {{players, gameState, fold, id}} skull
 * @returns {Element}
 */
export default function ({
                             announceValues,
                             eventSourceUrl,
                             userId: currentUserId,
                             skull,
                         }) {

    const [skullState, setSkullState] = useState(skull);
    const [blockPlayCard, setBlockPlayCard] = useState(false);

    let currentPlayer = skullState.players.find((player) => player.userId === currentUserId)

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

    const getSkullKing = async (skullId) => {

        const url = `/api/game/${skullId}`;
        const response = await fetch(url, {
            method: "GET",
        });

        const body = await response.json();
        if (!response.ok) {
            //todo player display message error
            console.log(body);
            throw new Error('Issue fetching get Skull');
        }
        return body;

    }

    const onCardPlayed = async (eventData) => {

        const skullKing = await getSkullKing(eventData.gameId)
        setSkullState((oldSkull) => {
            return ({
                ...skullKing,
                fold: oldSkull.fold.concat({id: eventData.cardId, playerId: eventData.playerId})
            });

        });

        if (skullKing.fold.length === 0) {
            setBlockPlayCard(true);
            window.setTimeout(() => {
                setSkullState(skullKing);
                setBlockPlayCard(false);
            }, 5000)
        }


    }

    const scoreByPlayer = (roundNumber, playerId) => {

        const announces = skullState.scoreBoard[playerId];
        let sum = 0;
        announces.forEach((announce, index) => {
            const announceRoundNumber = index + 1;
            if (announceRoundNumber <= roundNumber) {
                sum += announce.score;
            }

        })

        return sum;

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

        if (player.userId === currentUserId) {
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
            {currentPlayer.cards.map((card, index) => {
                return (skullState.gameState === 'PLAYCARD') ?
                    <form key={`${card.id}_${index}`}
                          onSubmit={(event) => {
                              event.preventDefault();
                              playCard(currentUserId, card);
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
        <div>
            <h2>SCORE</h2>
            <table>
                <thead>
                <tr>
                    <th key="round/player"> Round/Players</th>
                    {Object.keys(skullState.scoreBoard).map((playerId) => <th key={playerId}>{playerId}</th>)}
                </tr>
                </thead>
                <tbody>
                {[...Array(10).keys()].map((roundNumber) => <tr key={roundNumber + 1}>
                    <td>{roundNumber + 1}</td>
                    {Object.keys(skullState.scoreBoard).map((playerId) => <td key={`${playerId}_${roundNumber}`}>
                        {!!skullState.scoreBoard[playerId] && !!skullState.scoreBoard[playerId][roundNumber]
                            ? <div>
                                <span>Announced : {skullState.scoreBoard[playerId][roundNumber].announced} </span>
                                <span>Done : {skullState.scoreBoard[playerId][roundNumber].done} </span>
                                <span>Potential Bonus : {skullState.scoreBoard[playerId][roundNumber].potentialBonus} </span>
                                {roundNumber + 1 < skullState.roundNumber &&
                                    <span>Score : {scoreByPlayer(roundNumber + 1, playerId)} </span>
                                }
                            </div>
                            : "----"}
                    </td>)}

                </tr>)}
                </tbody>
            </table>
        </div>


    </div>

}

