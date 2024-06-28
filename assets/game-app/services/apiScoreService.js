import axios from "axios";
import {formatScores} from "../game/gameHelper";

export const apiConfirmScore = (gameId, playerId, thrownScore, thrownDarts, switchToTrow, isCheckout) => {
    const postData = {
        gameId: gameId,
        playerId: playerId,
        thrownScore: thrownScore,
        thrownDarts: thrownDarts,
        switchToTrow: switchToTrow,
        isCheckout: isCheckout
    };

    console.log("confirmScore POSTING: ", postData)

    axios.post('/api/score/confirm', postData)
        .then(response => {

        })
        .catch(error => {
            console.error('Error confirm score:', error);
        });
};

export const apiUndoScore = (gameId, playerId, switchToThrow) => {
    const postData = {
        gameId: gameId,
        playerId: playerId,
        switchToThrow: switchToThrow,
    };

    console.log("apiUndoScore Posting: ", postData);

    axios.post('/api/score/undo', postData)
        .then(response => {

        })
        .catch(error => {
            console.error('Error undo score:', error);
        });
};

export const apiFetchLastScores = (context, gameId, playerId) => {
    const postData = {
        gameId: gameId,
        playerId: playerId,
    };

    return axios.post('/api/score/last', postData)
        .then(response => {
            if (playerId === context.player1.id) {
                context.player1.lastScores = formatScores(response.data);
            } else if (playerId === context.player2.id) {
                context.player2.lastScores = formatScores(response.data);
            }
        })
        .catch(error => {
            console.error('Error fetching last scores:', error);
            throw error;
        });
};