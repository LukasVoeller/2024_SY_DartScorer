<template>
  <!-- Alert Box -->
  <div v-if="alertShow" :class="['alert', alertClass, 'overlay-alert']">
    {{ alertMessage }}
  </div>

  <!-- Select player row" -->
  <div class="row">
    <div class="col-6">
      <select class="selectpicker" data-live-search="true" data-width="100%" data-style="btn-success" data-size="5" title="Player 1" v-model="selectedPlayer1Id" @change="emitPlayer1Id" required>
        <option :style="isSelectedPlayer1(player.id) ? 'background: #4FBE96; color: #fff;' : ''" v-for="player in players" :key="player.id" :value="player.id">{{ player.name }}</option>
      </select>
    </div>

    <div class="col-6">
      <select class="selectpicker" data-live-search="true" data-width="100%" data-style="btn-success" data-size="5" title="Player 2" v-model="selectedPlayer2Id" @change="emitPlayer2Id" required>
        <option :style="isSelectedPlayer2(player.id) ? 'background: #4FBE96; color: #fff;' : ''" v-for="player in filteredPlayers" :key="player.id" :value="player.id">{{ player.name }}</option>
      </select>
    </div>
  </div>

  <br>

  <!-- To throw and add player row" -->
  <div class="row">
    <div class="col-6">
      <select class="selectpicker" data-width="100%" data-style="btn-success" data-size="5" title="To Throw first" v-model="selectedPlayerStartingId" :disabled="!selectedPlayer1Id" @change="emitPlayerStartingId" required>
        <option :style="isSelectedPlayer1(player.id) ? 'background: #4FBE96; color: #fff;' : ''" v-for="player in startingPlayerOptions" :key="player.id" :value="player.id">{{ player.name }} begins</option>
      </select>
    </div>

    <div class="col-6">
      <button class="btn btn-success w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
        <!-- <i class="bi bi-plus-lg"></i> -->
        <!-- <i class="bi bi-chevron-down"></i> -->
        New Player
      </button>
    </div>
  </div>

  <br>

  <!-- Add new player collapse -->
  <div class="collapse" id="collapseExample">
    <form @submit.prevent="submitNewPlayer" id="player-form">
      <div class="row">
        <div class="col-6">
          <input v-model="newPlayerName" type="text" class="form-control" id="newPlayerName"
                 placeholder="Player name">
        </div>

        <div class="col-6">
          <button id="custom-btn" class="btn btn-success w-100" type="submit">Add</button>
        </div>
      </div>
    </form>

    <br>
  </div>
</template>

<script lang="ts">

import axios from "axios";
import {defineComponent} from "vue";

interface Player {
  id: number;
  name: string;
}

export default defineComponent({
  name: 'PlayerSelection',

  data() {
    return {
      players: [] as Player[],
      selectedPlayer1Id: 1 as Number,
      selectedPlayer2Id: 2 as Number,
      selectedPlayerStartingId: 1 as Number,
      newPlayerName: '' as String,
      alertShow: false as Boolean,
      alertMessage: '' as String,
      alertClass: 'alert-success' as String,
      loading: false as Boolean
    }
  },

  mounted() {
    this.fetchPlayers();
  },

  /*
  beforeUnmount() {
    $('.selectpicker').selectpicker('destroy');
  },
  */

  computed: {
    filteredPlayers(): Player[] {
      // Filter out the selected player from the options for Player 2
      return this.players.filter(player => player.id !== this.selectedPlayer1Id);
    },

    startingPlayerOptions(): Player[] {
      // Filter the players based on the selected players
      if (this.selectedPlayer2Id) {
        return this.players.filter(player => player.id === this.selectedPlayer1Id || player.id === this.selectedPlayer2Id);
      } else if (this.selectedPlayer1Id) {
        return this.players.filter(player => player.id === this.selectedPlayer1Id);
      }
      return [];
    }
  },

  methods: {
    emitPlayer1Id() {
      this.$emit('update:selectedPlayer1Id', Number(this.selectedPlayer1Id));
    },

    emitPlayer2Id() {
      this.$emit('update:selectedPlayer2Id', Number(this.selectedPlayer2Id));
    },

    emitPlayerStartingId() {
      console.log("emitPlayerStartingId")
      this.$emit('update:selectedPlayerStartingId', Number(this.selectedPlayerStartingId));
    },

    triggerAlert(message: string, type: string) {
      this.alertMessage = message;
      this.alertClass = `alert-${type}`;
      this.alertShow = true;

      setTimeout(() => {
        this.alertShow = false;
      }, 3000);
    },

    submitNewPlayer() {
      if (this.newPlayerName.trim() === '') {
        this.triggerAlert(`Empty player name!`, 'warning');
        return;
      }

      if (this.newPlayerName.trim() !== '') {
        const playerExists = this.players.some(player => player.name.toLowerCase() === this.newPlayerName.trim().toLowerCase());

        if (playerExists) {
          this.triggerAlert(`${this.newPlayerName} already exists!`, 'danger');
          return;
        }

        axios.post('/api/player', {
          name: this.newPlayerName,
        })
            .then((response) => {
              // TODO: Use this.players.push(response.data) instead of this.fetchPlayers()
              //this.players.push(response.data);
              //console.log("RESPONSE:", response.data);
              if (!this.selectedPlayer1Id) {
                this.selectedPlayer1Id = response.data.id;
              } else {
                this.selectedPlayer2Id = response.data.id;
              }

              //this.emitPlayer1Id()
              this.fetchPlayers();
              this.newPlayerName = '';

              let playerName = response.data.name;
              this.triggerAlert(`${playerName} added!`, 'success');
            })
            .catch((error) => {
              console.error('Error adding player:', error);

              this.alertMessage = 'Failed to add new player!';
              this.alertShow = true;
              setTimeout(() => this.alertShow = false, 3000);
            });
      }
    },

    isSelectedPlayer1(playerId: number): boolean {
      return playerId === this.selectedPlayerStartingId;
    },

    isSelectedPlayer2(playerId: number): boolean {
      return playerId === this.selectedPlayer2Id;
    },

    fetchPlayers() {
      this.loading = true;
      // Fetch players from the API
      axios.get('/api/player')
          .then(response => {
            this.loading = false;
            // Sort players alphabetically by name
            this.players = response.data.sort((a: Player, b: Player) => a.name.localeCompare(b.name));
          })
          .catch(error => {
            this.loading = false;
            console.error('Error fetching players:', error);
          });
    },

    refreshSelectPickers() {
      this.$nextTick(() => {
        $('.selectpicker').selectpicker('refresh');
      });
    }
  },

  watch: {
    players() {
      this.refreshSelectPickers();
    },

    selectedPlayer1Id(newVal) {
      if (newVal) {
        this.emitPlayer1Id();
        this.selectedPlayerStartingId = newVal;
        this.emitPlayerStartingId();
      }
      this.refreshSelectPickers();
    },

    selectedPlayer2Id(newVal) {
      if (newVal) {
        this.emitPlayer2Id();
      }
      this.refreshSelectPickers();
    },

    selectedPlayerStartingId() {
      this.refreshSelectPickers();
    }
  }

})
</script>