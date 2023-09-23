import React, {useEffect, useState} from 'react';
import ReactModal from 'react-modal';

/**
 *
 * @param announceValues
 * @param eventSourceUrl
 * @param currentUserId
 * @param {{players, gameState, fold, id, currentPlayerId}} skull
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
    const [winMessageFold, setWinMessageFold] = useState(null);
    let currentPlayer = skullState.players.find((player) => player.userId === currentUserId);


    const calculateTotalScore = (playerId) => {
        let totalScore = 0;

        for (let i = 0; i < 10; i++) {
            if (skullState.scoreBoard[playerId] && skullState.scoreBoard[playerId][i]) {
                totalScore += skullState.scoreBoard[playerId][i].score;
            }
        }

        return totalScore;
    }

    const findWinner = () => {
        if (skullState.gameState !== 'GAMEOVER') {
            return null;
        }
        let winner = null;
        let maxScore = -1;

        skullState.players.forEach((player) => {
            const playerId = player.id;
            const totalScore = calculateTotalScore(playerId);

            if (totalScore > maxScore) {
                maxScore = totalScore;
                winner = player;
            }
        });

        return winner;
    }

    const onPlayerAnnounced = (data) => {

        setSkullState((oldSkull) => ({
            ...oldSkull,
            gameState: data.gamePhase,
            players: oldSkull.players.map((player) => {
                if (player.userId === data.userId) {
                    return {
                        ...player,
                        announce: parseInt(data.announce),
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
            setError(error.message);
        }
        return body;

    }

    const onCardPlayed = async (eventData) => {

        const skullKing = await getSkullKing(eventData.gameId);

        setSkullState((oldSkull) => {
            return ({
                ...skullKing,
                fold: oldSkull.fold.concat({
                    id: eventData.cardId,
                    playerId: eventData.playerId,
                })
            });
        });
        if (skullKing.fold.length === 0) {

            setBlockPlayCard(true);
            const currentPlayerId = skullKing.currentPlayerId;
            const winnerPlayerTurn = skullState.players.find(player => player.id === String(currentPlayerId));
            setWinMessageFold(winnerPlayerTurn.name);

            window.setTimeout(() => {
                setSkullState(skullKing);
                setBlockPlayCard(false);
                setWinMessageFold(null);
            }, 3000)
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
                throw new Error(errorMessage.message);
            }

        } catch (error) {
            setError(error.message);
            setTimeout(() => {
                setError(null);
            }, 3000);
        }
    };


    return <div>
        <div className="container">
            <div className="players-info">
                {error && <div className={`error-message ${error ? 'show' : ''}`}>{error}</div>}
                {winMessageFold && (
                    <div className={`winner-message`}>
                        Le joueur gagnant est {winMessageFold}
                    </div>
                )}
                {
                    skullState.players.map((player) => {
                        return <div key={player.id} className="player-card">
                            <p>{player.name}</p>
                            <p>{displayPlayerAnnounce(player)}</p>
                        </div>
                    })
                }
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

            <div className="score-container score-table-container">
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

        <ReactModal
            onRequestClose={handleCloseGameOverModal}
            className="game-over-modal"
            overlayClassName="game-over-overlay"
        >
            <div className="game-over">

                <button className="button-back"><a href="/game/room">
                    Retourner à la salle de jeu
                </a>
                </button>
                <h1>Partie terminée</h1>
                {skullState.gameState === 'GAMEOVER' && (
                    <p>Le joueur gagnant est {findWinner().name}</p>
                )}
            </div>
        </ReactModal>

    </div>

}

