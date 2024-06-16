<template>
  <div class="container p-0">
    <h1 id="headline">New Game</h1>

    <div class="card shadow" style="padding: 20px; background-color: #343E4C">
      <form @submit.prevent="submitForm">

        <PlayerSelection
            @update:selectedPlayer1Id="selectedPlayer1Id = $event"
            @update:selectedPlayer2Id="selectedPlayer2Id = $event"
        />

        <!-- Game Mode -->
        <div class="row">
          <div class="col">
            <div class="btn-group" role="group" style="width: 100%">
              <input type="radio" class="btn-check" name="gameMode" id="btnradio1" autocomplete="off"
                     v-model="selectedGameMode" value="X01" checked required>
              <label class="btn btn-outline-success" for="btnradio1">X01</label>

              <input type="radio" class="btn-check" name="gameMode" id="btnradio2" autocomplete="off"
                     v-model="selectedGameMode" value="Cricket" required>
              <label class="btn btn-outline-success" for="btnradio2">Cricket</label>

              <input type="radio" class="btn-check" name="gameMode" id="btnradio3" autocomplete="off"
                     v-model="selectedGameMode" value="Shanghai" required>
              <label class="btn btn-outline-success" for="btnradio3">Shanghai</label>
            </div>
          </div>
        </div>

        <br>

        <!-- Game Mode Options -->
        <div class="row">
          <div class="col" v-if="selectedGameMode === 'X01'">
            <div class="btn-group" role="group" style="width: 100%">
              <input type="radio" class="btn-check" name="startScoreX01" id="btnradio4" autocomplete="off"
                     v-model.number="selectedStartScoreX01" :value=501 checked required>
              <label class="btn btn-outline-success" for="btnradio4">501</label>

              <input type="radio" class="btn-check" name="startScoreX01" id="btnradio5" autocomplete="off"
                     v-model.number="selectedStartScoreX01" :value=301 required>
              <label class="btn btn-outline-success" for="btnradio5">301</label>

              <input type="radio" class="btn-check" name="startScoreX01" id="btnradio6" autocomplete="off"
                     v-model.number="selectedStartScoreX01" :value=101 required>
              <label class="btn btn-outline-success" for="btnradio6">101</label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col" v-if="selectedGameMode === 'X01'">
            <br>
            <div class="btn-group" role="group" style="width: 100%">
              <input type="radio" class="btn-check" name="finishType" id="btnradio7" autocomplete="off"
                     v-model="selectedFinishType" value="Double" checked required>
              <label class="btn btn-outline-success" for="btnradio7">Double</label>

              <input type="radio" class="btn-check" name="finishType" id="btnradio8" autocomplete="off"
                     v-model="selectedFinishType" value="Master" required>
              <label class="btn btn-outline-success" for="btnradio8">Master</label>

              <input type="radio" class="btn-check" name="finishType" id="btnradio9" autocomplete="off"
                     v-model="selectedFinishType" value="Straight" required>
              <label class="btn btn-outline-success" for="btnradio9">Straight</label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col" v-if="selectedGameMode === 'Cricket'">
            <div class="btn-group" role="group" style="width: 100%">
              <input type="radio" class="btn-check" name="startScoreCricket" id="btnradio10" autocomplete="off"
                     v-model="selectedStartScoreCricket" value="15-BULL" checked required>
              <label class="btn btn-outline-success" for="btnradio10">15-BULL</label>

              <input type="radio" class="btn-check" name="startScoreCricket" id="btnradio11" autocomplete="off"
                     v-model="selectedStartScoreCricket" value="Random 5" required>
              <label class="btn btn-outline-success" for="btnradio11">Random 5</label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col" v-if="selectedGameMode === 'Shanghai'">
            <div class="btn-group" role="group" style="width: 100%">
              <input type="radio" class="btn-check" name="startScoreShanghai" id="btnradio12" autocomplete="off"
                     v-model="selectedStartScoreShanghai" value="1-7" checked required>
              <label class="btn btn-outline-success" for="btnradio12">1-7</label>

              <input type="radio" class="btn-check" name="startScoreShanghai" id="btnradio13" autocomplete="off"
                     v-model="selectedStartScoreShanghai" value="1-20" required>
              <label class="btn btn-outline-success" for="btnradio13">1-20</label>
            </div>
          </div>
        </div>

        <br>

        <!-- Match Mode Options -->
        <div class="row">
          <div class="col">
            <div class="btn-group" role="group" style="width: 100%">

              <input type="radio" class="btn-check" name="matchMode" id="btnradio14" autocomplete="off"
                     v-model="selectedMatchMode" value="FirstToLegs" checked required>
              <label class="btn btn-outline-success" for="btnradio14">First to Legs</label>

              <input type="radio" class="btn-check" name="matchMode" id="btnradio15" autocomplete="off"
                     v-model="selectedMatchMode" value="FirstToSets" required>
              <label class="btn btn-outline-success" for="btnradio15">First to Sets</label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col" v-if="selectedMatchMode === 'FirstToSets'">
            <br>
            <p style="color: white">Sets:</p>
            <div class="btn-group" role="group" style="width: 100%">
              <input type="radio" class="btn-check" name="matchModeSets" id="btnradio16" autocomplete="off"
                     v-model="selectedMatchModeSets" value="2" checked required>
              <label class="btn btn-outline-success" for="btnradio16">2</label>

              <input type="radio" class="btn-check" name="matchModeSets" id="btnradio17" autocomplete="off"
                     v-model="selectedMatchModeSets" value="3" required>
              <label class="btn btn-outline-success" for="btnradio17">3</label>

              <input type="radio" class="btn-check" name="matchModeSets" id="btnradio18" autocomplete="off"
                     v-model="selectedMatchModeSets" value="4" required>
              <label class="btn btn-outline-success" for="btnradio18">4</label>
            </div>
          </div>

          <div class="col" v-if="selectedMatchMode === 'FirstToSets'">
            <br>
            <p style="color: white;">Legs:</p>
            <div class="btn-group" role="group" style="width: 100%">
              <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio19" autocomplete="off"
                     v-model="selectedMatchModeSetsLegs" value="2" checked required>
              <label class="btn btn-outline-success" for="btnradio19">2</label>

              <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio20" autocomplete="off"
                     v-model="selectedMatchModeSetsLegs" value="3" required>
              <label class="btn btn-outline-success" for="btnradio20">3</label>

              <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio21" autocomplete="off"
                     v-model="selectedMatchModeSetsLegs" value="4" required>
              <label class="btn btn-outline-success" for="btnradio21">4</label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col" v-if="selectedMatchMode === 'FirstToLegs'">
            <br>
            <p style="color: white">Legs:</p>
            <div class="btn-group" role="group" style="width: 100%">
              <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio19" autocomplete="off"
                     v-model="selectedMatchModeLegs" value="1" checked required>
              <label class="btn btn-outline-success" for="btnradio19">1</label>

              <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio20" autocomplete="off"
                     v-model="selectedMatchModeLegs" value="2" required>
              <label class="btn btn-outline-success" for="btnradio20">2</label>

              <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio21" autocomplete="off"
                     v-model="selectedMatchModeLegs" value="3" required>
              <label class="btn btn-outline-success" for="btnradio21">3</label>
            </div>
          </div>
        </div>

        <br>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-success w-100">Play</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</template>

<script lang="ts">
import axios from 'axios';
import 'jquery';
import { defineComponent } from 'vue';
import PlayerSelection from './player-selection.vue';

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
  },

  data() {
    return {
      gameId: 0 as Number,
      players: [] as Player[],
      selectedGameMode: "X01" as String,
      selectedStartScoreX01: 501 as Number,
      selectedStartScoreCricket: 0 as Number,
      selectedStartScoreShanghai: 0 as Number,
      selectedFinishType: "Double" as String,
      selectedMatchMode: "FirstToLegs" as String,
      selectedMatchModeSets: 2 as Number,
      selectedMatchModeSetsLegs: 2 as Number,
      selectedMatchModeLegs: 1 as Number,
      selectedPlayer1Id: 0 as Number,
      selectedPlayer2Id: 0 as Number,
      selectedPlayerStartingId: 0 as Number,
      newPlayerName: '' as String,
      showAlert: false as Boolean,
      alertMessage: '' as String,
      alertClass: 'alert-success' as String
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
        postData.matchModeSetsNeeded = this.selectedMatchModeSets;
        postData.matchModeLegsNeeded = this.selectedMatchModeSetsLegs;
      } else if (this.selectedMatchMode === 'FirstToLegs') {
        postData.matchModeSetsNeeded = 0;
        postData.matchModeLegsNeeded = this.selectedMatchModeLegs;
      }

      console.log("SUBMITTING:", postData);

      axios.post('/api/game/create', postData)
          .then(response => {
            console.log("Game started successfully.");
            this.gameId = response.data.gameId;
            window.location.href = `/game/${this.gameId}`;
          })
          .catch(error => {
            console.error('Error starting the game:', error);
          });
    },

    refreshSelectPickers() {
      this.$nextTick(() => {
        $('.selectpicker').selectpicker('refresh');
      });
    }
  },
})
</script>
