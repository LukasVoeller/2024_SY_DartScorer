<template>
  <div class="container">
    <h1 style="padding-top: 10px; padding-bottom: 10px;">New Game</h1>

    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col">
          <select class="form-select" aria-label="Default select example" v-model="selectedGameMode">
            <option disabled value="">Game Mode</option>
            <option value="X01">X01</option>
            <option value="Cricket">Cricket</option>
            <option value="Shanghai">Shanghai</option>
          </select>
        </div>

        <div class="col" v-if="selectedGameMode === 'X01'">
          <select class="form-select" aria-label="Default select example" v-model="selectedStartScore">
            <option disabled value="">Score</option>
            <option value="501">501</option>
            <option value="401">401</option>
            <option value="301">301</option>
          </select>
        </div>

        <div class="col" v-if="selectedGameMode === 'X01'">
          <select class="form-select" aria-label="Default select example"  v-model="selectedFinishType">
            <option disabled value="">Finish</option>
            <option value="Straight">Straight</option>
            <option value="Double">Double</option>
            <option value="Master">Master</option>
          </select>
        </div>

        <div class="col" v-if="selectedGameMode === 'Cricket'">
          <select class="form-select" aria-label="Default select example" v-model="selectedStartScore">
            <option disabled value="">Score</option>
            <option value="15-BULL">15-BULL</option>
            <option value="Random-5">Random 5</option>
          </select>
        </div>

        <div class="col" v-if="selectedGameMode === 'Shanghai'">
          <select class="form-select" aria-label="Default select example" v-model="selectedStartScore">
            <option disabled value="">Score</option>
            <option value="1-7">1-7</option>
            <option value="1-20">1-20</option>
          </select>
        </div>
      </div>

      <br>

      <div class="row">
        <div class="col">
          <select class="form-select" aria-label="Default select example"  v-model="selectedMatchMode">
            <option disabled value="">Match Mode</option>
            <option value="FirstToSets">First to Sets</option>
            <option value="FirstToLegs">First to Legs</option>
          </select>
        </div>

        <div class="col-6" v-if="selectedMatchMode === 'FirstToSets'">
          <select class="form-select" aria-label="Default select example"  v-model="selectedMatchModeSets">
            <option disabled value="">Sets</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
          </select>
        </div>

        <div v-if="selectedMatchMode === 'FirstToSets'">
          <br>
          <div class="row">
            <div class="col-6">
              <p>Legs needed for a Set:</p>
            </div>

            <div class="col-6">
              <select class="form-select" aria-label="Default select example"  v-model="selectedMatchModeLegs">
                <option disabled value="">Legs</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-6" v-if="selectedMatchMode === 'FirstToLegs'">
          <select class="form-select" aria-label="Default select example"  v-model="selectedMatchModeLegs">
            <option disabled value="">Legs</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
          </select>
        </div>
      </div>

      <br>

      <div class="row">
        <div class="col">
          <select class="form-select" aria-label="Default select example" v-model="selectedPlayer1">
            <option disabled value="">Player 1</option>
            <option v-for="player in players" :key="player.id" :value="player.id">{{ player.name }}</option>
          </select>
        </div>
      </div>

      <br>

      <div class="row">
        <div class="col">
          <select class="form-select" aria-label="Default select example" v-model="selectedPlayer2" :disabled="!selectedPlayer1">
            <option disabled value="">Player 2</option>
            <option v-for="player in filteredPlayers" :key="player.id" :value="player.id">{{ player.name }}</option>
          </select>
        </div>
      </div>

      <!--
      <div v-if="this.selectedPlayer1 && this.selectedPlayer2">
        <br>
        <div class="row" >
          <div class="col">
            <select class="form-select" aria-label="Default select example" v-model="selectedPlayer3">
              <option disabled value="">Player 3</option>
              <option v-for="player in filteredPlayers" :key="player.id" :value="player.id">{{ player.name }}</option>
            </select>
          </div>
        </div>
      </div>
      -->

      <div v-if="showPlayerWarning" class="alert alert-warning mt-3" role="alert">
        Please select at least two players before starting the game.
      </div>

      <div v-if="showGameWarning" class="alert alert-warning mt-3" role="alert">
        Please select the Game mode correctly.
      </div>

      <br>

      <div class="row">
        <div class="col-6">
          <select class="form-select" aria-label="Default select example" v-model="selectedPlayerStarting" :disabled="!selectedPlayer2">
            <option disabled value="">Throw first</option>
            <option v-for="player in filteredStartingPlayers" :key="player.id" :value="player.id">{{ player.name }}</option>
          </select>
        </div>

        <div class="col-6">
          <button id="custom-btn" class="btn btn-primary" type="submit" style="width: 100%">
            Play
          </button>
        </div>
      </div>

    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'NewGameComponent',
  data() {
    return {
      players: [],
      selectedGameMode: "", // Initialize with an empty string
      selectedStartScore: "", // Initialize with an empty string
      selectedFinishType: "", // Initialize with an empty string
      selectedMatchMode: "", // Initialize with an empty string
      selectedMatchModeSets: "", // Initialize with an empty string
      selectedMatchModeLegs: "", // Initialize with an empty string
      selectedPlayer1: "", // Initialize with an empty string
      selectedPlayer2: "", // Initialize with an empty string
      selectedPlayerStarting: "", // Initialize with an empty string

      showPlayerWarning: false, // Flag to track whether to show the warning
      showGameWarning: false // Flag to track whether to show the warning
    };
  },
  mounted() {
    this.fetchPlayers();
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
      axios.get('/api/players')
          .then(response => {
            // Sort players alphabetically by name
            this.players = response.data.sort((a, b) => a.name.localeCompare(b.name));
          })
          .catch(error => {
            console.error('Error fetching players:', error);
          });
    },
    submitForm() {
      if (!this.selectedPlayer1 || !this.selectedPlayer2) {
        // If either player is not selected, prevent form submission
        this.showPlayerWarning = true;
        return;
      }

      this.showPlayerWarning = false;

      if (!this.selectedGameMode || !this.selectedStartScore || !this.selectedFinishType) {
        // If either player is not selected, prevent form submission
        this.showGameWarning = true;
        return;
      }

      this.showGameWarning = false;

      // Send selected player data to the backend
      // TODO: Only send startScore and finishType if selectedGameMode is X01
      const postData = {
        gameMode: this.selectedGameMode,
        startScore: this.selectedStartScore,
        finishType: this.selectedFinishType,
        matchMode: this.selectedMatchMode,
        player1Id: this.selectedPlayer1,
        player2Id: this.selectedPlayer2,
        playerStartingId: this.selectedPlayerStarting,
      };

      if (this.selectedMatchMode === 'FirstToSets') {
        postData.matchModeSets = this.selectedMatchModeSets;
        postData.matchModeLegs = this.selectedMatchModeLegs;
      } else if (this.selectedMatchMode === 'FirstToLegs') {
        postData.matchModeSets = 0;
        postData.matchModeLegs = this.selectedMatchModeLegs;
      }

      axios.post('/api/game', postData)
          .then(response => {
            // Handle success response
            console.log("Game started successfully.");
            this.gameCode = response.data.gameCode;
            const gameUrl = `/game/${this.gameCode}`;
            window.location.href = gameUrl;
          })
          .catch(error => {
            // Handle error response
            console.error('Error starting game:', error);
          });
    }
  }
};
</script>
