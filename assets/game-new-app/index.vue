<template>
<!--  <h5 style="padding-top: 10px">New Game</h5>-->

    <div class="card shadow" style="padding: 20px; margin-top: 10px">
      <form @submit.prevent="submitForm">

        <PlayerSelection
            @update:selectedPlayer1Id="selectedPlayer1Id = $event"
            @update:selectedPlayer2Id="selectedPlayer2Id = $event"
            @update:selectedPlayerStartingId="selectedPlayerStartingId = $event"
        />

        <GameSelection
            @update:selectedGameMode="selectedGameMode = $event"
            @update:selectedStartScoreX01="selectedStartScoreX01 = $event"
            @update:selectedFinishType="selectedFinishType = $event"
            @update:selectedMatchMode="selectedMatchMode = $event"
            @update:selectedMatchModeSets="selectedMatchModeSets = $event"
            @update:selectedMatchModeSetsLegs="selectedMatchModeSetsLegs = $event"
            @update:selectedMatchModeLegs="selectedMatchModeLegs = $event"
        />

        <br>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-success w-100">Play</button>
          </div>
        </div>
      </form>
    </div>

</template>

<script lang="ts">
import axios from 'axios';
import 'jquery';
import { defineComponent } from 'vue';
import PlayerSelection from './select-player.vue';
import GameSelection from './select-game.vue';

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('jwt_token');  // Retrieve JWT token from localStorage
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;  // Add JWT token to Authorization header
  }
  return config;
}, error => {
  return Promise.reject(error);
});

interface Player {
  id: number;
  name: string;
}

export default defineComponent({
  name: 'NewGameComponent',

  components: {
    PlayerSelection,
    GameSelection,
  },

  data() {
    return {
      // PlayerSelection
      selectedPlayer1Id: 0 as Number,
      selectedPlayer2Id: 0 as Number,
      selectedPlayerStartingId: 0 as Number,

      // Game Selection
      selectedGameMode: "X01" as String,
      selectedStartScoreX01: 501 as Number,
      selectedStartScoreCricket: 0 as Number,
      selectedStartScoreShanghai: 0 as Number,
      selectedFinishType: "Double" as String,
      selectedMatchMode: "FirstToLegs" as String,
      selectedMatchModeSets: 2 as Number,
      selectedMatchModeSetsLegs: 2 as Number,
      selectedMatchModeLegs: 1 as Number,
    };
  },

  methods: {
    submitForm: function () {
      // Send selected player data to the backend
      // TODO: Only send startScore and finishType if selectedGameMode is X01
      const postData = {
        gameMode: this.selectedGameMode as String,
        startScore: this.selectedStartScoreX01 as Number,
        finishType: this.selectedFinishType as String,
        matchMode: this.selectedMatchMode as String,
        player1Id: this.selectedPlayer1Id as Number,
        player2Id: this.selectedPlayer2Id as Number,
        playerStartingId: this.selectedPlayerStartingId as Number,
        matchModeSetsNeeded: 0 as Number,
        matchModeLegsNeeded: 0 as Number
      };

      if (this.selectedMatchMode === 'FirstToSets') {
        postData.matchModeSetsNeeded = Number(this.selectedMatchModeSets);
        postData.matchModeLegsNeeded = Number(this.selectedMatchModeSetsLegs);
      } else if (this.selectedMatchMode === 'FirstToLegs') {
        postData.matchModeSetsNeeded = 0;
        postData.matchModeLegsNeeded = Number(this.selectedMatchModeLegs);
      }

      console.log("SUBMITTING:", postData);

      axios.post('/api/game/create', postData)
          .then(response => {
            window.location.href = `/game/${response.data.gameId}`;
          })
          .catch(error => {
            console.error('Error starting the game:', error);
          });
    },

    /*
    refreshSelectPickers() {
      this.$nextTick(() => {
        $('.selectpicker').selectpicker('refresh');
      });
    }
    */
  },
})
</script>
