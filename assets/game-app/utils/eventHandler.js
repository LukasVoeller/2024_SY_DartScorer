import {
    apiUpdateLeg
} from '../services/apiLegService';

import {
    apiConfirmScore,
} from '../services/apiScoreService';
import {apiSwitchToStartLeg} from "../services/apiTallyService";

export const handleLegShutModalConfirmed = (context, checkoutScore, checkoutDartCount, checkoutAverage, winnerPlayerId, looserPlayerId) => {
    const winnerPlayer = context.getPlayerById(winnerPlayerId);
    const looserPlayer = context.getPlayerById(looserPlayerId);

    context.processCheckout(winnerPlayer);
    context.processPlayerAverages(winnerPlayer, looserPlayer, checkoutDartCount, checkoutAverage);

    // Don't switch to throw if break of throw in legs
    if (context.toThrowPlayerId !== context.startingLegPlayerId) {
        console.log("apiConfirmScore - BREAK LEG")
        context.startingLegPlayerId = context.toThrowPlayerId;

        if (winnerPlayer.legs !== context.game.matchModeLegsNeeded) {
            apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true);
            apiSwitchToStartLeg(context.game.id);
        } else {
            apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, true, true);
        }
    } else {
        // Don't switch to throw if break of throw sets
        if (context.game.matchMode === "FirstToSets" && winnerPlayer.legs === context.game.matchModeLegsNeeded && context.toThrowPlayerId !== context.startingSetPlayerId) {
            console.log("apiConfirmScore - BREAK SET")
            context.startingLegPlayerId = context.toThrowPlayerId;
            apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true);
        } else {
            console.log("apiConfirmScore - NO BREAK")
            apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, true, true);
            apiSwitchToStartLeg(context.game.id);
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