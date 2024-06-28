import axios from "axios";
import { apiUpdateTallyLegSet } from './apiTallyService';

export const apiCreateLeg = (context, gameId, setId) => {
    const postData = {
        gameId: gameId,
        setId: setId,
    };

    return axios.post('/api/leg/create', postData)
        .then(response => {
            const legId = response.data.legId;
            context.player1.currentLegId = legId;
            context.player2.currentLegId = legId;

            apiUpdateTallyLegSet(context, gameId, context.player1.id, legId, null);
            apiUpdateTallyLegSet(context, gameId, context.player2.id, legId, null);

            return response.data;
        })
        .catch(error => {
            console.error('Error creating leg:', error);
            throw error;
        });
};

export const apiUpdateLeg = (legId, winnerPlayerId) => {
    const postData = {
        legId: legId,
        winnerPlayerId: winnerPlayerId,
    };

    axios.post('/api/leg/update', postData)
        .then(response => {

        })
        .catch(error => {
            console.error('Error updating leg:', error);
        });
};