import axios from "axios";

export const apiFetchPlayerData = (context, player1Id, player2Id) => {
    return Promise.all([
        axios.get(`/api/player/id/${player1Id}`),
        axios.get(`/api/player/id/${player2Id}`)
    ])
        .then(responses => {
            context.player1.name = responses[0].data.name;
            context.player2.name = responses[1].data.name;
            return {
                player1: responses[0].data,
                player2: responses[1].data
            };
        })
        .catch(error => {
            console.error('Error fetching player data:', error);
            throw error;
        });
};