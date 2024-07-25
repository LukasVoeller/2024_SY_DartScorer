<template>
  <div class="card shadow" style="margin-top: 15px; padding: 20px;">
    <h3 style="color: black; align-self: center; color: white; margin-bottom: 10px">Games ({{ filteredGamesCount }})</h3>

    <div class="row" style="margin-bottom: 10px">
      <div class="col-9 pe-1">
        <input
            type="text"
            class="form-control"
            placeholder="Search by player name"
            v-model="searchTerm"
            @input="resetToFirstPage"
        />
      </div>

      <div class="col-3 ps-1">
        <button class="btn btn-success w-100" style="height: 40px;" @click="resetFilters">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

    <div class="row" style="margin-bottom: 10px">
      <div class="col-6 pe-1 d-flex align-items-end">
        <Datepicker v-model="startDate" class="w-100 rounded" style="padding-left: 15px; border: none; height: 30px;" placeholder="Start" :format="customFormatter"/>
      </div>
      <div class="col-3 px-1 d-flex align-items-end">
        <button class="btn btn-success w-100" style="padding: 0px; padding-top: 3px; height: 30px;" @click="setLast30Days">30d</button>
      </div>
      <div class="col-3 ps-1 d-flex align-items-end">
        <button class="btn btn-success w-100" style="padding: 0px; padding-top: 3px; height: 30px;" @click="setLast90Days">90d</button>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-6 pe-1 d-flex align-items-end">
        <Datepicker v-model="endDate" class="w-100 rounded" style="padding-left: 15px; border: none; height: 30px;" placeholder="End" :format="customFormatter"/>
      </div>
      <div class="col-3 px-1 d-flex align-items-end">
        <button class="btn btn-success w-100" style="padding: 0px; padding-top: 3px; height: 30px;" @click="setYearToDate">YTD</button>
      </div>
      <div class="col-3 ps-1 d-flex align-items-end">
        <button class="btn btn-success w-100" style="padding: 0px; padding-top: 3px; height: 30px;" @click="setLastYear">{{lastYear}}</button>
      </div>
    </div>


    <table v-if="paginatedGames.length > 0" class="table table-hover">
      <thead class="table">
      <tr>
        <th style="color: white;">Date</th>
        <th style="color: white;">M</th>
        <th style="color: white;">Player 1</th>
        <th style="color: white;">Player 2</th>
        <th style="color: white;"></th>
      </tr>
      </thead>
      <tbody>
      <template v-for="game in paginatedGames" :key="game.id">
        <tr data-bs-toggle="collapse" :data-bs-target="'#collapse' + game.id">
          <td style="color: white;">{{ formatDate(game.date) }}</td>
          <td style="color: white;">{{ game.gameMode }}</td>
          <td
              :style="{
            color: game.winnerPlayerId === game.player1Id ? '#50BE96' : 'white',
            maxWidth: '70px',
            whiteSpace: 'nowrap',
            overflow: 'hidden',
            textOverflow: 'ellipsis'
          }"
          >
            {{ getPlayerName(game.player1Id) }}
          </td>
          <td
              :style="{
            color: game.winnerPlayerId === game.player2Id ? '#50BE96' : 'white',
            maxWidth: '70px',
            whiteSpace: 'nowrap',
            overflow: 'hidden',
            textOverflow: 'ellipsis'
          }"
          >
            {{ getPlayerName(game.player2Id) }}
          </td>
          <td>
            <i v-if="game.state === 'Live'" style="color: #FF5E5E" class="bi bi-record-circle"></i>
            <i v-else-if="game.state === 'Finished'" style="color: #50BE96" class="bi bi-check-circle"></i>
          </td>
        </tr>
        <tr>
          <td colspan="5" style="padding: 0px;">
            <div :id="'collapse' + game.id" class="collapse">
              <div style="padding: 10px;">
                <div class="row">
                  <div class="col-8" style="color: white;">
                    <p style="margin-bottom: 5px">ID: {{ game.id }}</p>
                    <p style="margin-bottom: 5px">Start score: {{ game.startScore }}</p>
                    <p style="margin-bottom: 5px">Finish type: {{ game.finishType }}</p>
                    <p style="margin-bottom: 5px">Match mode: {{ game.matchMode }}</p>
                    <p style="margin-bottom: 5px">Date: {{ new Date(game.date).toLocaleString() }}</p>
                  </div>
                  <div class="col-4 d-flex flex-column align-items-end align-items-bottom">
                    <button
                        type="button"
                        style="width: 100px;"
                        class="btn btn-success"
                        @click="viewGame(game.id)"
                    >
                      View
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
      </template>
      </tbody>
    </table>


    <div v-if="error">An error occurred while fetching game data.</div>
    <div style="display: flex; justify-content: center">
      <VueSpinnerDots v-if="loading" size="40" color="white" />
    </div>

    <div v-if="!loading && !paginatedGames.length" style="color: white; text-align: center; padding-bottom: 10px">
      No games found.
    </div>

    <div style="text-align: center;">
      <button
          class="btn btn-secondary"
          :disabled="currentPage === 1"
          style="margin-right: 20px"
          @click="goToPreviousPage">
        <i class="bi bi-chevron-left"></i>
      </button>
      <span style="color: white"> Page {{ currentPage }} of {{ totalPages }} </span>
      <button
          class="btn btn-secondary"
          :disabled="currentPage === totalPages"
          style="margin-left: 20px"
          @click="goToNextPage">
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  </div>
</template>

<script lang="ts">
import axios from 'axios';
import { defineComponent } from 'vue';
import { VueSpinnerDots } from 'vue3-spinners';
import Datepicker from 'vue3-datepicker';
//import 'vue3-datepicker/style.css';

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

interface Game {
  id: number;
  date: string;
  gameMode: string;
  player1Id: number;
  player2Id: number;
  winnerPlayerId: number;
  state: string;
  startScore: number;
  finishType: string;
  matchMode: string;
}

export default defineComponent({
  name: 'GameListComponent',

  components: {
    VueSpinnerDots,
    Datepicker,
  },

  data() {
    return {
      games: [] as Game[],
      players: [] as Player[],
      loading: true,
      error: false,
      currentPage: 1,
      pageSize: 6,
      searchTerm: '',
      startDate: null as Date | null,
      endDate: null as Date | null,
      lastYear: 0,
    };
  },

  computed: {
    filteredGames() {
      const lowerSearchTerm = this.searchTerm.trim().toLowerCase();
      return this.games.filter(game => {
        const gameDate = new Date(game.date);
        const inDateRange = (!this.startDate || gameDate >= this.startDate) && (!this.endDate || gameDate <= this.endDate);
        const matchesSearchTerm = !this.searchTerm.trim() ||
            this.getPlayerName(game.player1Id).toLowerCase().includes(lowerSearchTerm) ||
            this.getPlayerName(game.player2Id).toLowerCase().includes(lowerSearchTerm);
        return inDateRange && matchesSearchTerm;
      }).sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());
    },

    paginatedGames() {
      const start = (this.currentPage - 1) * this.pageSize;
      const end = start + this.pageSize;
      return this.filteredGames.slice(start, end);
    },

    totalPages() {
      return Math.ceil(this.filteredGames.length / this.pageSize);
    },

    filteredGamesCount() {
      return this.filteredGames.length;
    },
  },

  mounted() {
    this.lastYear = new Date().getFullYear() - 1;

    this.fetchGames();
    this.fetchPlayers();
  },

  methods: {
    formatDate(dateString: string) {
      const date = new Date(dateString);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = String(date.getFullYear()).slice(-2);
      return `${day}.${month}.${year}`;
    },

    viewGame(id: number) {
      window.location.href = `/game/${id}`;
    },

    fetchGames() {
      axios.get('/api/game/')
          .then(response => {
            this.games = response.data;
            this.loading = false;
          })
          .catch(error => {
            console.error('Error fetching game data:', error);
            this.error = true;
            this.loading = false;
          });
    },

    fetchPlayers() {
      this.loading = true;
      axios.get('/api/player')
          .then(response => {
            this.players = response.data;
            this.loading = false;
          })
          .catch(error => {
            this.loading = false;
            console.error('Error fetching players:', error);
          });
    },

    getPlayerName(playerId: number) {
      const player = this.players.find(player => player.id === playerId);
      return player ? player.name : 'Unknown';
    },

    resetToFirstPage() {
      this.currentPage = 1;
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

    customFormatter(date: Date) {
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      return `${day}.${month}.${year}`;
    },

    resetFilters() {
      this.startDate = null;
      this.endDate = null;
      this.searchTerm = ''; // Clear the search input
      this.resetToFirstPage();
    },

    setLast30Days() {
      const today = new Date();
      this.startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 30);
      this.endDate = today;
      this.resetToFirstPage();
    },

    setLast90Days() {
      const today = new Date();
      this.startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 90);
      this.endDate = today;
      this.resetToFirstPage();
    },

    setYearToDate() {
      const today = new Date();
      this.startDate = new Date(today.getFullYear(), 0, 1);
      this.endDate = today;
      this.resetToFirstPage();
    },

    setLastYear() {
      this.startDate = new Date(this.lastYear, 0, 1);
      this.endDate = new Date(this.lastYear, 11, 31);
      this.resetToFirstPage();
    },
  }
});
</script>

<style scoped>
/* Add any necessary styling for the date picker */
</style>
