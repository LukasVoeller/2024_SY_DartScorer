export const scoreIsImpossible = (score) => {
    const impossibleScores = [179, 178, 176, 175, 173, 172, 169, 166, 163, 162];
    score = parseInt(score, 10);

    if (score < 1) {
        return true;
    } else if (score > 180) {
        return true;
    } else return impossibleScores.includes(score);
};

export const formatScores = (scores) => {
    return scores.reverse().map(score => score.value);
};