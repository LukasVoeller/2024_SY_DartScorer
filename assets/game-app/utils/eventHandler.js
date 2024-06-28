import {
    apiUpdateLeg
} from '../services/apiLegService';

import {
    apiConfirmScore,
} from '../services/apiScoreService';

export const handleLegShutModalConfirmed = (context, checkoutScore, checkoutDartCount, checkoutAverage, winnerPlayerId, looserPlayerId) => {
    const winnerPlayer = context.getPlayerById(winnerPlayerId);
    const looserPlayer = context.getPlayerById(looserPlayerId);

    context.processCheckout(winnerPlayer);
    context.processPlayerAverages(winnerPlayer, looserPlayer, checkoutDartCount, checkoutAverage);

    // Don't switch to throw if break of throw
    if (context.toThrowPlayerId !== context.startingPlayerId) {
        context.startingPlayerId = context.toThrowPlayerId;
        apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true);
    } else {
        apiConfirmScore(context.game.id, context.toThrowPlayerId, checkoutScore, checkoutDartCount, true, true);
    }

    apiUpdateLeg(winnerPlayer.currentLegId, winnerPlayerId);
};

export const handleLegShutModalResumed = (context, checkoutScore) => {
    context.setPlayerScore(context.toThrowPlayerId, checkoutScore);
    context.removePlayerLastScore(context.toThrowPlayerId);
};

export const handleGameShutModalConfirmed = () => {
    window.location.reload();
};

export const handleGameShutModalResumed = () => {
    window.location.href = `/new-game`;
};

export const handleGameShutModalHome = () => {
    window.location.href = `/darts`;
};