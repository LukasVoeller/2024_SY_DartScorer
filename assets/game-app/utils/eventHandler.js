import {
    apiConfirmScore,
} from '../services/apiScoreService';
import {apiSwitchToStartLeg} from "../services/apiTallyService";

export const handleLegShutModalConfirmed = (context, checkoutScore, checkoutDartCount, checkoutAverage, winnerPlayerId, looserPlayerId) => {
    const winnerPlayer = context.getPlayerById(winnerPlayerId);
    const looserPlayer = context.getPlayerById(looserPlayerId);

    context.processCheckout(winnerPlayer);
    context.processPlayerAverages(winnerPlayer, looserPlayer, checkoutDartCount, checkoutAverage);

    // TODO: Maybe make the gameState a callback from processCheckout()?
    const gameState = context.game.state;

    // Don't switch to throw if break of throw in legs
    if (context.toThrowPlayerId !== context.startingLegPlayerId) {
        console.log("apiConfirmScore - BREAK LEG")
        context.startingLegPlayerId = context.toThrowPlayerId;

        if (winnerPlayer.legs !== context.game.matchModeLegsNeeded) {
            if (gameState === "Finished") {
                apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true, true);
            } else {
                apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true, false);
            }
            apiSwitchToStartLeg(context.game.id);
        } else {
            if (gameState === "Finished") {
                apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, true, true, true);
            } else {
                apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, true, true, false);
            }
        }
    } else {
        // Don't switch to throw if break of throw sets
        if (context.game.matchMode === "FirstToSets" && winnerPlayer.legs === context.game.matchModeLegsNeeded && context.toThrowPlayerId !== context.startingSetPlayerId) {
            console.log("apiConfirmScore - BREAK SET")
            context.startingLegPlayerId = context.toThrowPlayerId;
            if (gameState === "Finished") {
                apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true, true);
            } else {
                apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true, false);
            }
        } else {
            console.log("apiConfirmScore - NO BREAK")
            if (gameState === "Finished") {
                apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, true, true, true);
            } else {
                apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, true, true, false);
            }
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