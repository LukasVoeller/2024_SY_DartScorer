<template>
  <div class="container p-0">
    <h1 id="headline">New Game</h1>

    <div class="card shadow" style="padding: 20px">
      <form @submit.prevent="submitForm">
        <div class="row">
          <div class="col">
            <select class="selectpicker" data-live-search="true" data-width="100%" data-style="btn-success" data-size="5" title="Player 1" v-model="selectedPlayer1" required>
              <option v-for="player in players" :key="player.id" :value="player.id">{{ player.name }}</option>
            </select>
          </div>
        </div>

        <br>

        <div class="row">
          <div class="col">
            <select class="selectpicker" data-live-search="true" data-width="100%" data-style="btn-success" data-size="5" title="Player 2" v-model="selectedPlayer2" required>
              <option v-for="player in filteredPlayers" :key="player.id" :value="player.id">{{ player.name }}</option>
            </select>
          </div>
        </div>

        <br>

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
                     v-model="selectedStartScoreX01" value="501" checked required>
              <label class="btn btn-outline-success" for="btnradio4">501</label>

              <input type="radio" class="btn-check" name="startScoreX01" id="btnradio5" autocomplete="off"
                     v-model="selectedStartScoreX01" value="301" required>
              <label class="btn btn-outline-success" for="btnradio5">301</label>

              <input type="radio" class="btn-check" name="startScoreX01" id="btnradio6" autocomplete="off"
                     v-model="selectedStartScoreX01" value="101" required>
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
            <p>Sets:</p>
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
            <p>Legs:</p>
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
            <p>Legs:</p>
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
          <div class="col-6">
            <select class="form-select" aria-label="Default select example" v-model="selectedPlayerStarting"
                    :disabled="!selectedPlayer2" required>
              <option disabled value="">Throw first</option>
              <option v-for="player in filteredStartingPlayers" :key="player.id" :value="player.id">{{
                  player.name
                }}
              </option>
            </select>
          </div>

          <div class="col-6">
            <button type="submit" class="btn btn-success w-100">Play</button>
          </div>
        </div>
      </form>
    </div>


  </div>
</template>

<script>
import axios from 'axios';

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('jwt_token');  // Retrieve JWT token from localStorage
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;  // Add JWT token to Authorization header
  }
  return config;
}, error => {
  return Promise.reject(error);
});

export default {
  name: 'NewGameComponent',
  data() {
    return {
      players: [],
      selectedGameMode: "X01",
      selectedStartScoreX01: "501",
      selectedStartScoreCricket: "",
      selectedStartScoreShanghai: "",
      selectedFinishType: "Double",
      selectedMatchMode: "FirstToLegs",
      selectedMatchModeSets: "2",
      selectedMatchModeSetsLegs: "2",
      selectedMatchModeLegs: "1",
      selectedPlayer1: "",
      selectedPlayer2: "",
      selectedPlayerStarting: "",
    };
  },

  mounted() {
    this.fetchPlayers();
  },

  beforeDestroy() {
    $('.selectpicker').selectpicker('destroy');
  },

  computed: {
    filteredPlayers() {
      // Filter out the selected player from the options for Player 2
      return this.players.filter(player => player.id !== this.selectedPlayer1);
    },

    filteredStartingPlayers() {
      // Filter out players who are not selected as Player 1 or Player 2
      const selectedPlayerIds = [this.selectedPlayer1, this.selectedPlayer2];
      return this.players.filter(player => selectedPlayerIds.includes(player.id));
    }
  },

  methods: {
    fetchPlayers() {
      // Fetch players from the API
      axios.get('/api/player')
          .then(response => {
            // Sort players alphabetically by name
            this.players = response.data.sort((a, b) => a.name.localeCompare(b.name));
          })
          .catch(error => {
            console.error('Error fetching players:', error);
          });
    },

    submitForm() {
      // Send selected player data to the backend
      // TODO: Only send startScore and finishType if selectedGameMode is X01
      const postData = {
        gameMode: this.selectedGameMode,
        startScore: this.selectedStartScoreX01,
        finishType: this.selectedFinishType,
        matchMode: this.selectedMatchMode,
        player1Id: this.selectedPlayer1,
        player2Id: this.selectedPlayer2,
        playerStartingId: this.selectedPlayerStarting,
      };

      if (this.selectedMatchMode === 'FirstToSets') {
        postData.matchModeSetsNeeded = this.selectedMatchModeSets;
        postData.matchModeLegsNeeded = this.selectedMatchModeSetsLegs;
      } else if (this.selectedMatchMode === 'FirstToLegs') {
        postData.matchModeSetsNeeded = 0;
        postData.matchModeLegsNeeded = this.selectedMatchModeLegs;
      }

      axios.post('/api/game/create', postData)
          .then(response => {
            console.log("Game started successfully.");
            this.gameId = response.data.gameId;
            window.location.href = `/game/${this.gameId}`;
          })
          .catch(error => {
            console.error('Error starting the game:', error);
          });
    }
  },

  watch: {
    players(newVal, oldVal) {
      this.$nextTick(() => {
        $('.selectpicker').selectpicker('refresh');
      });
    },

    selectedPlayer1() {
      this.$nextTick(() => {
        $('.selectpicker').selectpicker('refresh');
      });
    },

    selectedPlayer2() {
      this.$nextTick(() => {
        $('.selectpicker').selectpicker('refresh');
      });
    }
  }
};
</script>
