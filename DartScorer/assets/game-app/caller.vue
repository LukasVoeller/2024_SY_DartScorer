<template>
  <!-- The component doesn't need visible content -->
  <!-- It can have a hidden div if necessary -->
  <div style="display: none;"></div>
</template>

<script>
import { EventBus } from '../event-bus';
import gameOn from '../../sounds/caller/caller-game_on.mp3';
import gameShut from '../../sounds/caller/caller-game_shut.mp3';
import gameShutAndTheMatch from '../../sounds/caller/caller-game_shut_and_the_match.mp3';

// Importing all score sound files dynamically
const scoreSounds = require.context('../../sounds/score', false, /\.mp3$/);

export default {
  name: 'Caller',
  created() {
    // Set up event listeners for different sound events
    EventBus.on('play-gameOn-sound', this.playGameOnSound);
    EventBus.on('play-gameShut-sound', this.playGameShutSound);
    EventBus.on('play-gameShutAndTheMatch-sound', this.playGameShutAndTheMatchSound);
    EventBus.on('play-score-sound', this.playScoreSound);
  },

  beforeUnmount() {
    // Clean up event listeners when the component is unmounted
    EventBus.off('play-gameOn-sound', this.playGameOnSound);
    EventBus.off('play-gameShut-sound', this.playGameShutSound);
    EventBus.off('play-gameShutAndTheMatch-sound', this.playGameShutAndTheMatchSound);
    EventBus.off('play-score-sound', this.playScoreSound);
  },

  methods: {
    playGameOnSound() {
      this.playSound(gameOn);
    },

    playGameShutSound() {
      this.playSound(gameShut);
    },

    playGameShutAndTheMatchSound() {
      this.playSound(gameShutAndTheMatch);
    },

    playScoreSound(score) {
      const soundFile = scoreSounds(`./score-${score}.mp3`).default;
      this.playSound(soundFile);
    },

    playSound(soundFile) {
      const audio = new Audio(soundFile);
      audio.play();
    },
  },
};
</script>
