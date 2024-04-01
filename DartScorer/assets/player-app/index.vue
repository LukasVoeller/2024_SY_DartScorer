<template>
  <h1 style="padding-top: 20px;">Player</h1>
  <!-- Table to display player -->

  <table class="table table-bordered mt-4">
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

  <form @submit.prevent="submitForm" :class="{ 'was-validated': formNeedsValidation }" id="player-form" class="row g-3 needs-validation" novalidate="">
    <div class="col-8">
      <input v-model="newPlayerName" type="text" class="form-control" id="newPlayerName" required="" placeholder="Player name">
    </div>

    <div class="col-4">
      <button class="btn btn-primary" type="submit">Add Player</button>
    </div>
  </form>
</template>

<script>
import axios from 'axios';
export default {
  name: 'PlayerComponent',
  data() {
    return {
      players: [],
      newPlayerName: '',
      formNeedsValidation: false
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
      // Fetch players from the API
      axios.get('/api/players')
          .then(response => {
            this.players = response.data;
          })
          .catch(error => {
            console.error('Error fetching players:', error);
          });
    },
    submitForm() {
      if (this.newPlayerName.trim() !== '') {
        this.formNeedsValidation = false;

        // Make an API request to add the new player
        axios.post('/api/player', {
          name: this.newPlayerName
        })
            .then(response => {
              // Update the players array with the new player
              this.players.push(response.data);

              // Clear the form input
              this.newPlayerName = '';
            })
            .catch(error => {
              console.error('Error adding player:', error);
            });
      } else {
        this.formNeedsValidation = true;
      }
    },
    deletePlayer(playerId) {
      // Display a confirmation dialog
      const confirmed = window.confirm('Are you sure you want to delete this player?');

      if (confirmed) {
        // Make an API request to delete the player
        axios.delete(`/api/player/${playerId}`)
            .then(() => {
              // Remove the player from the players array
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
