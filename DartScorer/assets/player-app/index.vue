<template>
  <h1 style="padding-top: 15px;">Player</h1>

  <!-- Table to display player -->
  <div class="card shadow" style="padding: 20px; margin-bottom: 25px">
    <table class="table">
      <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Player Name</th>
        <th scope="col">Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="player in players" :key="player.id">
        <td>{{ player.id }}</td>
        <td>{{ player.name }}</td>
        <td>
          <button v-if="!isAdmin(player)" @click="deletePlayer(player.id)" class="btn btn-danger">Delete</button>
        </td>
      </tr>
      </tbody>
    </table>

    <div style="display: flex; justify-content: center">
      <VueSpinnerDots v-if="isLoading" size="40" color="black" />
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="card shadow" style="padding: 20px">
        <form @submit.prevent="submitForm" :class="{ 'was-validated': fromIsValid }" id="player-form" class="row g-3 needs-validation" novalidate="">
          <div class="col-8">
            <input v-model="newPlayerName" type="text" class="form-control" id="newPlayerName" required="" placeholder="Player name">
          </div>

          <div class="col-4">
            <button class="btn btn-primary w-100" type="submit">Add Player</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import {VueSpinnerDots} from "vue3-spinners";

export default {
  name: 'PlayerComponent',
  components: {
    VueSpinnerDots
  },
  data() {
    return {
      isLoading: true,
      players: [],
      newPlayerName: '',
      fromIsValid: false
    };
  },
  mounted() {
    this.fetchPlayers();
  },
  methods: {
    isAdmin(player) {
      if (!player || !player.user) {
        return false;
      }
      return player.user.roles.includes('ROLE_ADMIN');
    },

    fetchPlayers() {
      this.isLoading = true;

      axios.get('/api/player')
          .then(response => {
            this.players = response.data;
            this.isLoading = false;
          })
          .catch(error => {
            console.error('Error fetching players:', error);
            this.isLoading = false;
          });
    },

    submitForm() {
      if (this.newPlayerName.trim() !== '') {
        this.fromIsValid = false;

        axios.post('/api/player', {
          name: this.newPlayerName
        })
            .then(response => {
              this.players.push(response.data);
              this.newPlayerName = '';
            })
            .catch(error => {
              console.error('Error adding player:', error);
            });
      } else {
        this.fromIsValid = true;
      }
    },

    deletePlayer(playerId) {
      const confirmed = window.confirm('Are you sure you want to delete this player?');

      if (confirmed) {
        axios.delete(`/api/player/${playerId}`)
            .then(() => {
              this.players = this.players.filter(player => player.id !== playerId);
            })
            .catch(error => {
              console.error('Error deleting player:', error);
            });
      }
    },
  },
};
</script>
