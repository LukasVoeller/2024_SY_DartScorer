<template>
  <!-- The component doesn't need visible content -->
  <!-- It can have a hidden div if necessary -->
  <div style="display: none;"></div>
</template>

<script>
import { EventBus } from '../event-bus';
import gameOn from '../../sound/caller/caller-game_on.mp3';

export default {
  name: 'SoundPlayer',
  created() {
    // Set up event listeners for different sound events
    EventBus.on('play-gameOn-sound', this.playgameOnSound);
  },

  beforeUnmount() {
    // Clean up event listeners when the component is unmounted
    EventBus.off('play-gameOn-sound', this.playgameOnSound);
  },

  methods: {
    playgameOnSound() {
      const audio = new Audio(gameOn);
      audio.play();
    },

  },
};
</script>
