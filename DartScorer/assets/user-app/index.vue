<template>
  <h1 style="padding-top: 15px">User Management</h1>

  <!-- Table to display user -->
  <div class="card shadow h-100" style="padding: 20px; margin-bottom: 25px">
    <table class="table">
      <thead>
      <tr>
        <th scope="col">Username</th>
        <th scope="col">Player</th>
        <th scope="col">Role</th>
        <th scope="col">Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="user in users" :key="user.id">
        <td>{{ user.username }}</td>
        <td>{{ user.player ? user.player.name : "None" }}</td>
        <td>
          {{ user.roles.includes('ROLE_ADMIN') ? 'Admin' : (user.roles.includes('ROLE_ASSOCIATE') ? 'Associate' : (user.roles.includes('ROLE_PLAYER') ? 'Player' : user.roles[0])) }}
        </td>
        <td>
          <button @click="deleteUser(user.id)" class="btn btn-danger">Delete</button>
        </td>
      </tr>
      </tbody>
    </table>

    <div style="display: flex; justify-content: center">
      <VueSpinnerDots v-if="isLoading" size="40" color="black" />
    </div>
  </div>

  <div class="row d-flex">
    <div class="col-md-6" style="margin-bottom: 25px">
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
              <button type="submit" class="btn btn-primary w-100">Add User</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="col-md-6 h-100">
      <div class="card shadow h-100" style="padding: 20px">
        <table class="table">
          <thead>
          <tr>
            <th scope="col">Role</th>
            <th scope="col">Manage User</th>
            <th scope="col">Manage Player</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>Admin</td>
            <td>Yes</td>
            <td>Yes</td>
          </tr>
          <tr>
            <td>Associate</td>
            <td>No</td>
            <td>Yes</td>
          </tr>
          <tr>
            <td>Player</td>
            <td>No</td>
            <td>No</td>
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

export default {
  name: 'UserComponent',
  components: {
    VueSpinnerDots
  },
  data() {
    return {
      isLoading: true,
      users: [],
      players: [],
      newUsername: '',
      newPassword: '',
      newRole: '',
      newPlayerId: '',
      formNeedsValidation: false
    };
  },
  mounted() {
    this.fetchUsers();
    this.fetchPlayersWithNoUser();
  },
  methods: {
    fetchUsers() {
      this.isLoading = true;

      axios.get('/api/user')
          .then(response => {
            this.users = response.data;
            this.isLoading = false;
          })
          .catch(error => {
            console.error('Error fetching users:', error);
            this.isLoading = false;
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
      // Check if username is not empty
      if (this.newUsername.trim() && this.newPassword.trim() && this.newRole !== "" && this.newPlayerId !== "") {
        this.formNeedsValidation = false;

        axios.post('/api/user', {
          username: this.newUsername,
          password: this.newPassword,
          role: this.newRole,
          player: this.newPlayerId
        })
            .then(response => {
              // Update data
              this.fetchUsers();
              this.fetchPlayersWithNoUser();

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
        axios.delete(`/api/user/${userId}`)
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
