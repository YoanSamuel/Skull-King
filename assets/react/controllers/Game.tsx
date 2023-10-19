import {CSSProperties, FC, useEffect, useState} from 'react';
import ReactModal from 'react-modal';

type Player = {
    id: string;
    name: string;
    userId: string;
    cards: Card[];
    announce: number | undefined;
}

type RoundScore = {
    score: number;
    announced: number;
    done: number;
    potentialBonus: number;
};

type ScoreBoard = { [key in Player['id']]: RoundScore[] };

type Card = {
    id: string;
    playerId: string;
}

type Fold = Card[];

type SkullKing = {
    id: string;
    players: Player[];
    scoreBoard: ScoreBoard;
    gameState: 'ANNOUNCE' | 'PLAYCARD' | 'GAMEOVER';
    fold: Fold;
    roundNumber: number;
};

type Props = {
    announceValues: number[];
    eventSourceUrl: string;
    userId: string;
    skull: SkullKing;
}

type Cell = {
    player?: Player;
    alignItems: CSSProperties['alignItems'];
    justifyContent: CSSProperties['justifyContent'];
    position: CSSProperties;
}

const cellOne = (player?: Player): Cell => ({
    player,
    alignItems: "flex-start",
    justifyContent: "left",
    position: {top: '65px', left: '-15px'}
});
const cellTwo = (player?: Player): Cell => ({
    player,
    alignItems: "flex-start",
    justifyContent: "center",
    position: {top: '-40px'}
});
const cellThree = (player?: Player): Cell => ({
    player,
    alignItems: "flex-start",
    justifyContent: "right",
    position: {top: '65px', right: '-15px'}
});
const cellFour = (player?: Player): Cell => ({
    player,
    alignItems: "center",
    justifyContent: "left",
    position: {left: '-10px'}
});
const cellFive = (player?: Player): Cell => ({player, alignItems: "center", justifyContent: "center", position: {}});
const cellSix = (player?: Player): Cell => ({
    player,
    alignItems: "center",
    justifyContent: "right",
    position: {right: '-10px'}
});
const cellSeven = (player?: Player): Cell => ({
    player,
    alignItems: "flex-end",
    justifyContent: "left",
    position: {bottom: '65px', left: '-15px'}
});
const cellEight = (player?: Player): Cell => ({
    player,
    alignItems: "flex-end",
    justifyContent: "center",
    position: {bottom: '-20px'}
});
const cellNine = (player?: Player): Cell => ({
    player,
    alignItems: "flex-end",
    justifyContent: "right",
    position: {bottom: '65px', right: '-15px'}
});

const Fold: FC<{ skullKing: SkullKing, currentUserId: string }> = ({skullKing, currentUserId}) => {

    const playersCount = skullKing.players.length;
    const first = skullKing.players[0];
    const second = skullKing.players[1];
    const third = skullKing.players[2];
    const fourth = skullKing.players[3];
    const fifth = skullKing.players[4];
    const sixth = skullKing.players[5];

    const tableConfig = {
        2: [cellOne(null), cellTwo(first), cellThree(null), cellFour(null), cellFive(null), cellSix(null), cellSeven(null), cellEight(second), cellNine(null)],
        3: [cellOne(null), cellTwo(first), cellThree(null), cellFour(null), cellFive(null), cellSix(null), cellSeven(third), cellEight(null), cellNine(second)],
        4: [cellOne(null), cellTwo(first), cellThree(null), cellFour(null), cellFive(second), cellSix(null), cellSeven(third), cellEight(null), cellNine(fourth)],
        5: [cellOne(first), cellTwo(null), cellThree(second), cellFour(third), cellFive(null), cellSix(fourth), cellSeven(null), cellEight(fifth), cellNine(null)],
        6: [cellOne(first), cellTwo(second), cellThree(third), cellFour(null), cellFive(null), cellSix(null), cellSeven(fourth), cellEight(fifth), cellNine(sixth)],
    };

    const config: Cell[] = tableConfig[playersCount];

    const displayPlayerAnnounce = (player: Player) => {
        if (!player.announce && player.announce !== 0) {
            return 'En attente...';
        }

        if (player.userId === currentUserId) {
            return `Annonce : ${player.announce}`;
        }

        if (skullKing.gameState !== 'ANNOUNCE') {
            return `Annonce : ${player.announce}`;
        }

        return 'A voté, en attente des autres joueurs.';
    }

    return <>
        <div className="fold">
            <img src="/images/table.png" alt="table"/>
            {config.map((cell, index) => <div key={index + 1} className="fold-slot" style={{
                    justifyContent: cell.justifyContent,
                    alignItems: cell.alignItems,
                }}>
                    {!cell.player
                        ? undefined
                        : <div className="player-info" style={cell.position}>
                            <p>{cell.player.name}</p>
                            {displayPlayerAnnounce(cell.player)}

                            {skullKing.fold
                                .map((card) => {
                                    const playingPlayer = skullKing.players.find(p => p.id === String(card.playerId));
                                    if (!playingPlayer || cell.player.id != card.playerId) {
                                        return null;
                                    }

                                    return (
                                        <div key={card.id} className="card folded-card">
                                            <span className="player-name">{playingPlayer?.name}</span>
                                            <img src={`/images/game/cards/${card.id}.png`}
                                                 alt={card.id}
                                            />
                                        </div>
                                    );
                                })}
                        </div>
                    }
                </div>
            )}
        </div>
    </>
}

const Game: FC<Props> = ({
                             announceValues,
                             eventSourceUrl,
                             userId: currentUserId,
                             skull,
                         }) => {

    const [skullState, setSkullState] = useState(skull);
    const [blockPlayCard, setBlockPlayCard] = useState(false);
    const [error, setError] = useState(null);
    const [winMessageFold, setWinMessageFold] = useState(null);
    const [modalIsOpen, setModalIsOpen] = useState(false);
    let currentPlayer = skullState.players.find((player) => player.userId === currentUserId);
    console.log(skullState, modalIsOpen);

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
        if (skullState.gameState != 'GAMEOVER') {
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

    const onPlayerAnnounced = async (eventData) => {


        const skullKing = await getSkullKing(eventData.gameId);
        setSkullState(skullKing);
    }

    const getSkullKing = async (skullId) => {

        const url = `/api/game/${skullId}`;
        const response = await fetch(url, {
            method: "GET",
        });
        if (response.url.includes('login')) {
            window.location.href = response.url;
            return;
        }
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

            if (skullState.gameState === 'GAMEOVER') {
                setModalIsOpen(true);
            }
        };
    }, [skullState.gameState])


    const playCard = async (playerId, card) => {

        const url = `/game/${skullState.id}/player/${playerId}/playcard/${card.id}`;
        try {
            const response = await fetch(url, {
                method: "POST",
            });
            if (response.url.includes('login')) {
                window.location.href = response.url;
                return;
            }
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

    // @ts-ignore
    // @ts-ignore
    return <div className="container">

        {error && <div className={`error-message ${error ? 'show' : ''}`}>{error}</div>}
        {winMessageFold && (
            <div className={`winner-message`}>
                Le joueur gagnant est {winMessageFold}
            </div>
        )}

        <div className="game-container">
            <Fold skullKing={skullState} currentUserId={currentUserId}/>

            <div className="button-container-announce">

                {
                    (skullState.gameState === 'ANNOUNCE') && Array.from({length: Math.max(...skullState.players.map(player => player.cards.length)) + 1}, (_, i) => i).map((announce) => {
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

        <ReactModal
            className="game-over-modal"
            overlayClassName="game-over-overlay"
            isOpen={modalIsOpen}>

            {(skullState.gameState === 'GAMEOVER' && skullState.roundNumber > 10) && (
                <div className="game-over">
                    <button className="button-back" aria-description="Retourner aux salon de jeux"><a href="/game/room">
                        Retourner à la salle de jeu
                    </a>
                    </button>
                    <h1>Partie terminée</h1>
                    <h4>Le roi des pirates est {findWinner().name}</h4>

                </div>)}
        </ReactModal>
    </div>
}

export default Game;