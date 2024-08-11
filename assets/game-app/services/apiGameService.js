import axios from "axios";
import { apiFetchTally } from './apiTallyService';
// import { apiFetchPlayerData } from './apiPlayerService';
import { apiFetchLastScores } from './apiScoreService';

export const apiFetchGameData = (context, gameId) => {
    return axios.get(`/api/game/id/${gameId}`)
        .then(response => {
            //console.log("game.response.data")
            // console.log("response.data")
            // console.log(response.data)

            context.game = response.data;
            context.player1.startScore = response.data.startScore;
            context.player2.startScore = response.data.startScore;
            context.player1.id = response.data.player1.id;
            context.player2.id = response.data.player2.id;
            context.player1.name = response.data.player1.name;
            context.player2.name = response.data.player2.name;
            //context.game.state = response.data.state;
            //context.toThrowPlayerId = response.data.toThrowPlayerId;
            //context.startingLegPlayerId = response.data.startingLegPlayerId;

            return Promise.all([
                apiFetchTally(context, gameId, context.player1.id),
                apiFetchTally(context, gameId, context.player2.id),
                // apiFetchPlayerData(context, context.player1.id, context.player2.id),
                apiFetchLastScores(context, gameId, context.player1.id),
                apiFetchLastScores(context, gameId, context.player2.id)
            ]);
        })
        .catch(error => {
            console.error('Error fetching game data:', error);
            throw error;
        });
};

export const apiUpdateGameShot = (gameId, winnerPlayerId, gameState) => {
    const postData = {
        winnerPlayerId: winnerPlayerId,
        gameState: gameState,
    };

    return axios.put(`/api/game/id/${gameId}`, postData)
        .then(response => {
            return response.data; // Return response data
        })
        .catch(error => {
            console.error('Error updating game state:', error);
            throw error;
        });
};