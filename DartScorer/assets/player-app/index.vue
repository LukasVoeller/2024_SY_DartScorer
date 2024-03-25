<template>
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
          <button @click="deletePlayer(player.id)" class="btn btn-danger">Delete</button>
        </td>
      </tr>
      </tbody>
    </table>

    <!-- Form to add a new player -->
    <form @submit.prevent="submitForm" class="mt-4 d-flex justify-content-between">
      <div class="col-auto">
        <label for="newPlayerName" class="sr-only">New Player Name:</label>
        <input v-model="newPlayerName" type="text" class="form-control" id="newPlayerName" required>
      </div>
      <button type="submit" class="btn btn-primary">Add Player</button>
    </form>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PlayerComponent',
  data() {
    return {
      players: [],
      newPlayerName: '' // New player name entered in the form
    };
  },
  mounted() {
    this.fetchPlayers();
  },
  methods: {
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
