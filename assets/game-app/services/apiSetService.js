import axios, {post} from "axios";
import { apiUpdateTallyLegSet } from './apiTallyService';

export const apiCreateSet = (context, gameId, callback) => {
    const postData = {
        gameId: gameId,
    };

    return axios.post('/api/set/create', postData)
        .then(response => {
            const setId = response.data.setId;
            context.game.currentSetId = setId;

            apiUpdateTallyLegSet(context, gameId, context.player1.id, null, setId);
            apiUpdateTallyLegSet(context, gameId, context.player2.id, null, setId);

            if (callback) {
                callback(setId); // Pass setId to the callback
            }

            return response.data;
        })
        .catch(error => {
            console.error('Error creating leg:', error);
            throw error;
        });
};

export const apiUpdateSet = (setId, winnerPlayerId) => {
    const postData = {
        setId: setId,
        winnerPlayerId: winnerPlayerId,
    };

    //console.log("apiUpdateSet: ", postData)

    axios.post('/api/set/update', postData)
        .then(response => {

        })
        .catch(error => {
            console.error('Error updating set:', error);
        });
};