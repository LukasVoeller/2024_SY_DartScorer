// types.ts
export interface Player {
    id: number;
    name: string;
}

export interface Game {
    id: number;
    date: string;
    player1: Player;
    player2: Player;
}
