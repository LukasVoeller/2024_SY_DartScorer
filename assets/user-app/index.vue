<template>
<!--  <h5 style="padding-top: 10px">Manage User</h5>-->

  <!-- Table to display user before -->
  <div class="card shadow h-100" style="padding: 20px; margin-top: 15px;">
    <input
        type="text"
        class="form-control"
        placeholder="Search by user name"
        v-model="searchTerm"
        @input="resetToFirstPage"
        style="margin-bottom: 20px"
    />

    <table class="table">
      <thead>
      <tr>
        <th scope="col" style="color: white;">Username</th>
        <th scope="col" style="color: white;">Player</th>
        <th scope="col" style="color: white;">Role</th>
        <th scope="col" style="color: white;" class="text-end">Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="user in paginatedUsers" :key="user.id">
        <td style="color: white;">{{ user.username }}</td>
        <td style="color: white;">{{ user.player ? user.player.name : "None" }}</td>
        <td style="color: white;">{{ determineRole(user) }}</td>
        <td style="color: white;" class="text-end">
          <button @click="deleteUser(user.id)" class="btn btn-danger">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
      </tbody>
    </table>

    <!-- Loading spinner -->
    <div style="display: flex; justify-content: center">
      <VueSpinnerDots v-if="isLoadingFetchUser" size="40" color="black" />
    </div>

    <!-- Pagination Controls -->
    <div style="text-align: center; margin-top: 10px;">
      <button
          class="btn btn-secondary"
          :disabled="currentPage === 1"
          style="margin-right: 20px"
          @click="goToPreviousPage"
      >
        <i class="bi bi-chevron-left"></i>
      </button>
      <span style="color: white"> Page {{ currentPage }} of {{ totalPages }} </span>
      <button
          class="btn btn-secondary"
          :disabled="currentPage === totalPages"
          style="margin-left: 20px"
          @click="goToNextPage"
      >
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  </div>

  <div class="row d-flex" style="padding-top: 10px">
    <div class="col-md-6" style="margin-bottom: 10px">
      <div class="card shadow h-100" style="padding: 20px;">
        <form @submit.prevent="submitForm" :class="{ 'was-validated': formNeedsValidation }" novalidate="">
          <div class="row">
            <div class="col-6" style="padding-bottom: 20px;">
              <input v-model="newUsername" type="text" class="form-control" id="newUsername" placeholder="Username" required>
            </div>
            <div class="col-6">
              <input v-model="newPassword" type="password" class="form-control" id="newPassword" placeholder="Password"
                     required>
            </div>
          </div>

          <div class="col-12" style="padding-bottom: 20px;">
            <select class="form-select" aria-label="Default select example" v-model="newPlayerId" id="newPlayer" required>
              <option disabled value="">Select Player</option>
              <option v-for="player in players" :key="player.id" :value="player.id">{{ player.name }}</option>
            </select>
          </div>

          <div class="row">
            <div class="col-8" style="padding-bottom: 20px;">
              <select class="form-select" aria-label="Default select example" v-model="newRole" required>
                <option disabled value="">Select Role</option>
                <option value="ROLE_ADMIN">Admin</option>
                <option value="ROLE_ASSOCIATE">Associate</option>
                <option value="ROLE_PLAYER">Player</option>
              </select>
            </div>

            <div class="col-4">
              <button type="submit" class="btn btn-success w-100">Add</button>
            </div>

            <div style="display: flex; justify-content: center">
              <VueSpinnerDots v-if="isLoadingPostUser" size="40" color="black" />
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="col-md-6 h-100">
      <div class="card shadow h-100" style="padding: 20px; margin-bottom: 25px">
        <table class="table">
          <thead>
          <tr>
            <th scope="col" style="color: white;">Role</th>
            <th scope="col" style="color: white;">Manage User</th>
            <th scope="col" style="color: white;">Manage Player</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td style="color: white;">Admin</td>
            <td style="color: white;">Yes</td>
            <td style="color: white;">Yes</td>
          </tr>
          <tr>
            <td style="color: white;">Associate</td>
            <td style="color: white;">No</td>
            <td style="color: white;">Yes</td>
          </tr>
          <tr>
            <td style="color: white;">Player</td>
            <td style="color: white;">No</td>
            <td style="color: white;">No</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import {VueSpinnerDots} from 'vue3-spinners';

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
  name: 'UserComponent',

  components: {
    VueSpinnerDots
  },

  data() {
    return {
      isLoadingFetchUser: true,
      isLoadingPostUser: false,
      users: [],
      players: [],
      newUsername: '',
      newPassword: '',
      newRole: '',
      newPlayerId: '',
      formNeedsValidation: false,
      pageSize: 5,
      currentPage: 1,
      searchTerm: '',
    };
  },

  mounted() {
    this.fetchUsers();
    this.fetchPlayersWithNoUser();
  },

  computed: {
    totalPages() {
      return Math.ceil(this.filteredUsers.length / this.pageSize); // Calculate total pages based on filtered users
    },

    filteredUsers() {
      // Filter users based on the search term
      if (!this.searchTerm.trim()) {
        return this.users; // If no search term, return all users
      }
      const lowerSearchTerm = this.searchTerm.trim().toLowerCase(); // Lowercase search term for case-insensitive search
      return this.users.filter(user =>
          user.username.toLowerCase().includes(lowerSearchTerm) // Check if username contains the search term
      );
    },

    paginatedUsers() {
      const filteredUsers = this.filteredUsers; // Use the filtered users
      const start = (this.currentPage - 1) * this.pageSize;
      const end = start + this.pageSize;
      return filteredUsers.slice(start, end); // Return the appropriate slice for the current page
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

    determineRole(user) {
      if (user.roles.includes('ROLE_ADMIN')) {
        return 'Admin';
      }
      if (user.roles.includes('ROLE_ASSOCIATE')) {
        return 'Associate';
      }
      if (user.roles.includes('ROLE_PLAYER')) {
        return 'Player';
      }
      return user.roles[0]; // Default fallback
    },

    fetchUsers() {
      this.isLoadingFetchUser = true;

      axios.get('/api/user')
          .then(response => {
            this.users = response.data;
            this.isLoadingFetchUser = false;
          })
          .catch(error => {
            console.error('Error fetching users:', error);
            this.isLoadingFetchUser = false;
          });
    },

    fetchPlayersWithNoUser() {
      // Fetch players from the API
      axios.get('/api/player/no-user')
          .then(response => {
            this.players = response.data;
          })
          .catch(error => {
            console.error('Error fetching players:', error);
          });
    },

    submitForm() {
      // Make an API request to add the new player
      // Check if inputs are not empty
      if (this.newUsername.trim() && this.newPassword.trim() && this.newRole !== "" && this.newPlayerId !== "") {

        const usernameExists = this.users.some(user => user.username === this.newUsername.trim());

        if (usernameExists) {
          alert('This username is already taken. Please choose a different one.');
          return; // Prevent the form from submitting
        }

        this.formNeedsValidation = false;
        this.isLoadingPostUser = true;

        axios.post('/api/user', {
          username: this.newUsername,
          password: this.newPassword,
          role: this.newRole,
          player: this.newPlayerId
        })
            .then(response => {
              // Update data
              //this.fetchUsers();
              this.users.push(response.data);
              this.fetchPlayersWithNoUser();
              this.isLoadingPostUser = false;

              // Reset the form inputs
              this.newUsername = '';
              this.newPassword = '';
              this.newRole = ''; // Reset role selection
              this.newPlayerId = ''; // Reset player selection
            })
            .catch(error => {
              console.error('Error adding user:', error);
              alert('An error occurred while adding the user.');
            });
      } else {
        this.formNeedsValidation = true;
      }
    },

    deleteUser(userId) {
      // Display a confirmation dialog
      const confirmed = window.confirm('Are you sure you want to delete this user?');

      if (confirmed) {
        // Make an API request to delete the user
        axios.delete(`/api/user/id/${userId}`)
            .then(() => {
              // Remove the user from the users array
              this.users = this.users.filter(user => user.id !== userId);

              this.fetchPlayersWithNoUser();
            })
            .catch(error => {
              console.error('Error deleting user:', error);
            });
      }
    },
  },
};
</script>
