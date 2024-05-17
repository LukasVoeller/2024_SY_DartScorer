<template>
  <h1 id="headline" >Player Management</h1>

  <!-- Table to display player before -->
  <div class="card shadow" style="padding: 20px; margin-bottom: 25px">
    <input
        type="text"
        class="form-control"
        placeholder="Search by player name"
        v-model="searchTerm"
        @input="resetToFirstPage"
        style="margin-bottom: 20px"
    />

    <table class="table">
      <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Player Name</th>
        <th scope="col" class="text-end">Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="player in paginatedPlayers" :key="player.id">
        <td>{{ player.id }}</td>
        <td>{{ player.name }}</td>
        <td class="text-end">
          <button v-if="!isAdmin(player)" @click="deletePlayer(player.id)" class="btn btn-danger">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
      </tbody>
    </table>

    <!-- Loading spinner -->
    <div style="display: flex; justify-content: center">
      <VueSpinnerDots v-if="isLoading" size="40" color="black" />
    </div>

    <!-- Pagination Controls -->
    <div style="text-align: center; margin-top: 10px;">
      <button
          class="btn btn-secondary"
          :disabled="currentPage === 1"
          style="margin-right: 20px"
          @click="goToPreviousPage">
        <i class="bi bi-chevron-left"></i>
      </button>
      <span> Page {{ currentPage }} of {{ totalPages }} </span>
      <button
          class="btn btn-secondary"
          :disabled="currentPage === totalPages"
          style="margin-left: 20px"
          @click="goToNextPage">
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  </div>

  <div class="row">
    <!-- Add Player -->
    <div class="col-md-6">
      <div class="card shadow" style="padding: 20px">
        <form @submit.prevent="submitForm" :class="{ 'was-validated': fromIsValid }" id="player-form" class="row g-3 needs-validation" novalidate="">
          <div class="col-8">
            <input v-model="newPlayerName" type="text" class="form-control" id="newPlayerName" required="" placeholder="Player name">
          </div>

          <div class="col-4">
            <button id="custom-btn" class="btn btn-primary w-100" type="submit">Add</button>
          </div>

          <div style="display: flex; justify-content: center">
            <VueSpinnerDots v-if="isLoadingPostPlayer" size="40" color="black" />
          </div>
        </form>
      </div>
    </div>
  </div>

</template>

<script>
import axios from 'axios';
import { VueSpinnerDots } from "vue3-spinners";

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
  name: 'PlayerComponent',

  components: {
    VueSpinnerDots,
  },

  data() {
    return {
      isLoading: true,
      isLoadingPostPlayer: false,
      players: [],
      newPlayerName: '',
      fromIsValid: false,
      currentPage: 1,
      pageSize: 5,
      searchTerm: '',
    };
  },

  mounted() {
    this.fetchPlayers();
  },

  computed: {
    totalPages() {
      const filteredPlayers = this.filteredPlayers;
      return Math.ceil(filteredPlayers.length / this.pageSize);
    },

    paginatedPlayers() {
      const filteredPlayers = this.filteredPlayers;
      const start = (this.currentPage - 1) * this.pageSize;
      const end = start + this.pageSize;
      return filteredPlayers.slice(start, end);
    },

    filteredPlayers() {
      // Filter players based on the search term
      if (!this.searchTerm.trim()) {
        return this.players;
      }
      const lowerSearchTerm = this.searchTerm.trim().toLowerCase();
      return this.players.filter(player =>
          player.name.toLowerCase().includes(lowerSearchTerm)
      );
    },
  },

  methods: {
    resetToFirstPage() {
      this.currentPage = 1; // When the search term changes, go back to the first page
    },

    goToPreviousPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },

    goToNextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },

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
        this.isLoadingPostPlayer = true;

        axios.post('/api/player', {
          name: this.newPlayerName,
        })
            .then((response) => {
              this.players.push(response.data);
              this.newPlayerName = '';
              this.currentPage = this.totalPages;
              this.isLoadingPostPlayer = false;
            })
            .catch((error) => {
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
              if (this.currentPage > this.totalPages) {
                this.currentPage = this.totalPages;
              }
            })
            .catch((error) => {
              console.error('Error deleting player:', error);
            });
      }
    },
  },
};
</script>
