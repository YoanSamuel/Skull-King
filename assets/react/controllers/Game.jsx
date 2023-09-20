import React, {useEffect, useState} from 'react';
import '/public/css/game.css';


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
    const [error, setError] = useState(null);
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
            setError(error.message);
        }
        return body;

    }

    const onCardPlayed = async (eventData) => {

        const skullKing = await getSkullKing(eventData.gameId);
        console.log(skullState);
        setSkullState((oldSkull) => {
            return ({
                ...skullKing,
                fold: oldSkull.fold.concat({
                    id: eventData.cardId,
                    playerId: eventData.playerId,
                    playerName: eventData.playerName
                })
            });
        });

        if (skullKing.fold.length === 0) {
            setBlockPlayCard(true);
            window.setTimeout(() => {
                setSkullState(skullKing);
                setBlockPlayCard(false);
            }, 4000)
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
            return `Annonce : ${player.announce}`;
        }

        if (skullState.gameState !== 'ANNOUNCE') {
            return `Annonce : ${player.announce}`;
        }

        return 'A voté, en attente des autres joueurs.';
    }


    const playCard = async (playerId, card) => {

        const url = `/game/${skullState.id}/player/${playerId}/playcard/${card.id}`;
        try {
            const response = await fetch(url, {
                method: "POST",
            });

            if (!response.ok) {
                const errorMessage = await response.json();
                console.log(errorMessage);
                throw new Error(errorMessage.message);
            }

            const body = await response.json();
        } catch (error) {
            setError(error.message);
            setTimeout(() => {
                setError(null);
            }, 3000);
        }
    };


    return <div id="body">
        <div className="container">
            <div className="players-info">
                {error && <div className={`error-message ${error ? 'show' : ''}`}>{error}</div>}

                {
                    skullState.players.map((player) => {
                        return <div key={player.id} className="player-card">
                            <p>{player.name}</p>
                            <p>{displayPlayerAnnounce(player)}</p>
                        </div>
                    })
                }
                <div className="turn-indicator">
                    <p>C'est à {skull.currentPlayerTurnId} de jouer.</p>
                </div>
                <div className="button-container-announce">

                    {
                        (skullState.gameState === 'ANNOUNCE') && announceValues.map((announce) => {
                            return <form key={announce} action={`/game/${skullState.id}/announce/${announce}`}
                                         method="POST">
                                <button type="submit" id="announce-button"> {announce} </button>
                            </form>
                        })

                    }
                </div>
                <p> Votre main : </p>
                <div id="player-hand">
                    {currentPlayer.cards.map((card, index) => {
                        return (skullState.gameState === 'PLAYCARD') ?
                            <form key={`${card.id}_${index}`}
                                  onSubmit={(event) => {
                                      event.preventDefault();
                                      playCard(currentUserId, card);
                                  }}>
                                <button type="submit" disabled={blockPlayCard}>
                                    <img src={`/images/game/cards/${card.id}.png`} alt={card.id}
                                         className="card-player-hand"/>
                                </button>
                            </form>
                            : <span key={`${card.id}_${index}`}>
                                <img src={`/images/game/cards/${card.id}.png`} alt={card.id}
                                     className="card-player-hand"/>
                            </span>
                    })
                    }
                </div>
                <div className="fold">
                    <h2>LA FOLD</h2>
                    <div className="table">
                        {skullState.fold.map((card) => {
                            const playingPlayer = skullState.players.find(p => p.id === String(card.playerId));
                            const playerName = playingPlayer ? playingPlayer.name : "Joueur inconnu";

                            return (
                                <div key={card.id} className="card folded-card">

                                    <span className="player-name">{playerName}</span>
                                    <img src={`/images/game/cards/${card.id}.png`}
                                         alt={card.id}
                                         className="card-player-hand"/>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>

            <div className="score-container">
                <table className="score-table">
                    <thead>
                    <tr>
                        <th key="round/player" className="thead-round-players"> Round/Players</th>
                        {Object.keys(skullState.scoreBoard).map((playerId) => {
                            const player = skullState.players.find(p => p.id === playerId);
                            return <th key={playerId}>{player ? player.name : playerId}</th>
                        })}
                    </tr>
                    </thead>
                    <tbody>
                    {[...Array(10).keys()].map((roundNumber) => <tr key={roundNumber + 1} className="column-round">
                        <td>{roundNumber + 1}</td>
                        {Object.keys(skullState.scoreBoard).map((playerId) => <td key={`${playerId}_${roundNumber}`}
                                                                                  className="column-score">
                            {!!skullState.scoreBoard[playerId] && !!skullState.scoreBoard[playerId][roundNumber]
                                ? <div className="score-cell">
                                    <span>Annonce : {skullState.scoreBoard[playerId][roundNumber].announced}
                                        Pli : {skullState.scoreBoard[playerId][roundNumber].done} </span>
                                    <span>Potentiel Bonus : {skullState.scoreBoard[playerId][roundNumber].potentialBonus} </span>
                                    {roundNumber + 1 < skullState.roundNumber &&
                                        <span>Score : {scoreByPlayer(roundNumber + 1, playerId)} </span>
                                    }
                                </div>
                                : "----"}
                        </td>)}

                    </tr>)}
                    </tbody>
                </table>
                <div className="background-image"></div>
            </div>
        </div>

    </div>

}

