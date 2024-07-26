import axios from "axios";

// TODO: Call setLegSetId() once per game and not for every player?
export const apiFetchTally = (context, gameId, playerId) => {
    const postData = {
        gameId: gameId,
        playerId: playerId,
    };

    return axios.post('/api/tally', postData)
        .then(response => {
            console.log("tally.response.data")
            console.log(response.data)

            const responseData = response.data;
            context.setPlayerScore(playerId, responseData.score);
            context.setPlayerLegSetWon(playerId, responseData.legsWon, responseData.setsWon);
            context.setLegSetId(responseData.legId, responseData.setId);
            if (responseData.toThrow) {
                context.toThrowPlayerId = responseData.playerId;
            }
            if (responseData.startedLeg) {
                context.startingLegPlayerId = responseData.playerId;
            }
            if (responseData.startedSet) {
                context.startingSetPlayerId = responseData.playerId;
            }
        })
        .catch(error => {
            console.error('Error fetching tally data:', error);
            throw error;
        });
};

export const apiSwitchToThrow = (gameId, callback) => {
    const postData = {
        gameId: gameId,
    };

    axios.post('/api/tally/switch-to-throw', postData)
        .then(response => {
            if (callback) {
                callback(response);
            }
        })
        .catch(error => {
            console.error('Error switching to throw:', error);
        });
};

export const apiSwitchToStartLeg = (gameId) => {
    const postData = {
        gameId: gameId,
    };

    axios.post('/api/tally/switch-to-start-leg', postData)
        .then(response => {

        })
        .catch(error => {
            console.error('Error switch to start leg:', error);
        });
};

export const apiSwitchToStartSet = (gameId) => {
    const postData = {
        gameId: gameId,
    };

    //console.log("apiSwitchToStartSet", postData)

    axios.post('/api/tally/switch-to-start-set', postData)
        .then(response => {

        })
        .catch(error => {
            console.error('Error switch to start set:', error);
        });
};

export const apiUpdateTallyScore = (gameId, playerId, score, legsWon, setsWon) => {
    const postData = {
        gameId: gameId,
        playerId: playerId,
        score: score,
        legsWon: legsWon,
        setsWon: setsWon,
    };

    //console.log("apiUpdateTallyScore POSTING: ", postData)

    axios.post('/api/tally/update-score', postData)
        .then(response => {

        })
        .catch(error => {
            console.error('Error updating tally score:', error);
        });
};

export const apiUpdateTallyLegSet = (context, gameId, playerId, legId, setId) => {
    const postData = {
        gameId: gameId,
        playerId: playerId,
        legId: legId,
        setId: setId,
    };

    return axios.post('/api/tally/update-leg-set', postData)
        .then(response => {
            // If there's anything specific you need to do with context here, you can add it.
            // For now, this is just a placeholder to show context usage
        })
        .catch(error => {
            console.error('Error updating tally leg set:', error);
            throw error;
        });
};