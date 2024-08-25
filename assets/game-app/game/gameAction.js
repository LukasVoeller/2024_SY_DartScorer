import { EventBus } from '../../event-bus';
import {apiConfirmScore, apiUndoScore} from '../services/apiScoreService';
import {apiSwitchToThrow, apiSwitchToStartLeg, apiSwitchToStartSet} from "../services/apiTallyService";
import {scoreIsImpossible} from "./gameHelper";

export const confirmScore = (context, score) => {
    const player = context.getPlayerById(context.toThrowPlayerId);
    const opponentPlayer = context.getOpponentPlayerById(context.toThrowPlayerId);
    const gameState = context.game.state;

    score = parseInt(score.replace(/^0+/, ''), 10);
    if (isNaN(score)) {
        score = 0;
    }

    if (player.totalScore - score === 0) {
        player.lastScores.unshift(score);
        EventBus.emit('show-leg-shut-modal', player.lastScores, player.id, opponentPlayer.id);
        //console.log("PLAYER 1 DISPLAY SCORE: ", context.player1.displayScore)
        //console.log("PLAYER 2 DISPLAY SCORE: ", context.player2.displayScore)
    } else {
        apiConfirmScore(context.game.id, context.toThrowPlayerId, score, 3, true, false, false);
    }
};

export const clearScore = (context, score) => {
    score = parseInt(score.replace(/^0+/, ''), 10);
    if (isNaN(score)) {
        score = 0;
    }

    if (context.toThrowPlayerId === context.player1.id) {
        context.player1.displayScore = context.player1.totalScore;
        context.player1.scoreBusted = false;
    } else if (context.toThrowPlayerId === context.player2.id) {
        context.player2.displayScore = context.player2.totalScore;
        context.player2.scoreBusted = false;
    }
};

export const leftScore = (context, inputScore) => {
    let score = parseInt(inputScore.replace(/^0+/, ''), 10);
    if (isNaN(score)) {
        score = 0;
    }

    const player = context.toThrowPlayerId === context.player1.id ? context.player1 : context.player2;
    const thrownScore = player.totalScore - score;
    const leftScore = player.totalScore - thrownScore;
    //console.log("leftScore: ", leftScore);

    // Reset if left score is not possible
    if (scoreIsImpossible(thrownScore) || (leftScore < 2 && leftScore !== 0)) {
        //onsole.log("TRIGGERED");
        player.displayScore = player.totalScore;
        player.scoreBusted = false;
    }

    // Checkout
    else if (leftScore === 0) {
        const opponentPlayer = context.getOpponentPlayerById(player.id);
        EventBus.emit('play-score-sound', thrownScore);
        //player.currentDartsThrown.unshift(3);

        player.lastScores.unshift(thrownScore);
        EventBus.emit('show-leg-shut-modal', player.lastScores, player.id, opponentPlayer.id);

        // if (context.eventSourceState === 1) {
        //     console.log("-----> ", thrownScore)
        //     apiConfirmScore(context.game.id, player.id, thrownScore, 3, true, true);
        // }
        //context.setPlayerScore(context, player.id, score);
        //EventBus.emit('show-leg-shut-modal', player.lastScores, player.id, opponentPlayer.id);
    }

    // Left
    else if (leftScore >= 2) {
        //console.log("player.totalScore: ", player.totalScore, " thrownScore: ", thrownScore);
        player.scoreBusted = false;
        EventBus.emit('play-score-sound', thrownScore);
        player.currentDartsThrown.unshift(3);
        if (context.eventSourceState === 1) {
            apiConfirmScore(context.game.id, player.id, thrownScore, 3, true, false, false);
        }
        context.setPlayerScore(context, player.id, score);
    }
};

export const undoScore = (context, score) => {
    //console.log("Player 1 last scores: ", context.player1.lastScores);
    //console.log("Player 2 last scores: ", context.player2.lastScores);

    if (context.player1.lastScores.length === 0 && context.player2.lastScores.length === 0 &&
        context.player1.legs === 0 && context.player2.legs === 0 &&
        context.player1.sets === 0 && context.player2.sets === 0) {
        if (context.eventSourceState === 1) {
            //console.log("--- UNDO SWITCH TO THROW ---");
            apiSwitchToStartLeg(context.game.id);
            apiSwitchToStartSet(context.game.id);
            apiSwitchToThrow(context.game.id, () => {
                context.switchToThrow();
                context.startingLegPlayerId = context.toThrowPlayerId;
                context.startingSetPlayerId = context.toThrowPlayerId;
            });
        }
    } else if (context.player1.lastScores.length > 0 || context.player2.lastScores.length > 0) {
        //console.log("--- UNDO UNDO SCORE ---");
        clearScore(context, score);
        const playerId = context.getNotToThrowPlayerId();
        apiUndoScore(context.game.id, playerId, true);
    }
};