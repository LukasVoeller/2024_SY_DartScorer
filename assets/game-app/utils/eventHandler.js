import {
    apiConfirmScore,
} from '../services/apiScoreService';

export const handleLegShutModalConfirmed = (context, checkoutScore, checkoutDartCount, checkoutAverage, winnerPlayerId, looserPlayerId) => {
    const winnerPlayer = context.getPlayerById(winnerPlayerId);
    const looserPlayer = context.getPlayerById(looserPlayerId);

    context.processCheckout(winnerPlayer);
    context.processPlayerAverages(winnerPlayer, looserPlayer, checkoutDartCount, checkoutAverage);

    // Don't switch to throw if break of throw in legs
    if (context.toThrowPlayerId !== context.startingLegPlayerId) {
        console.log("apiConfirmScore - BREAK LEG")
        context.startingLegPlayerId = context.toThrowPlayerId;
        apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true);
    } else {

        // Don't switch to throw if break of throw sets
        if (context.game.matchMode === "FirstToSets" && winnerPlayer.legs === context.game.matchModeLegsNeeded && context.toThrowPlayerId !== context.startingSetPlayerId) {
            console.log("apiConfirmScore - BREAK SET")
            context.startingLegPlayerId = context.toThrowPlayerId;
            apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true);
        } else {
            console.log("apiConfirmScore - NO BREAK")
            apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, true, true);
        }
    }
};

export const handleLegShutModalResumed = (context, checkoutScore) => {
    context.setPlayerScore(context.toThrowPlayerId, checkoutScore);
    context.removePlayerLastScore(context.toThrowPlayerId);
};

export const handleGameShutModalConfirmed = () => {
    window.location.reload();
};

export const handleGameShutModalResumed = () => {
    window.location.href = `/game/new`;
};

export const handleGameShutModalHome = () => {
    window.location.href = `/darts`;
};