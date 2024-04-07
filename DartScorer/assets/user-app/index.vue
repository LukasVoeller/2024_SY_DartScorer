<template>
  <h1 style="padding-top: 15px;">User</h1>

    <!-- Table to display user -->
    <table class="table table-bordered mt-4">
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


  <form @submit.prevent="submitForm" :class="{ 'was-validated': formNeedsValidation }"
        class="mt-4 d-flex justify-content-between" novalidate="">

    <div class="row">
      <div class="col-6" style="padding-bottom: 20px;">
        <input v-model="newUsername" type="text" class="form-control" id="newUsername" placeholder="Username" required>
      </div>

      <div class="col-6">
        <input v-model="newPassword" type="password" class="form-control" id="newPassword" placeholder="Password"
               required>
      </div>

      <div class="col-12" style="padding-bottom: 20px;">
        <select class="form-select" aria-label="Default select example" v-model="newRole" required>
          <option disabled value="">Select Role</option>
          <option value="ROLE_ADMIN">Admin</option>
          <option value="ROLE_ASSOCIATE">Associate</option>
          <option value="ROLE_PLAYER">Player</option>
        </select>
      </div>

      <div class="col-12" style="padding-bottom: 20px;">
        <select class="form-select" aria-label="Default select example" v-model="newPlayerId" id="newPlayer" required>
          <option disabled value="">Select Player</option>
          <option v-for="player in playersWithoutUser" :key="player.id" :value="player.id">{{ player.name }}</option>
        </select>
      </div>

      <div class="col-12">
        <button type="submit" class="btn btn-primary">Add User</button>
      </div>
    </div>

  </form>

</template>

<script>
import axios from 'axios';

export default {
  name: 'UserComponent',
  data() {
    return {
      users: [],
      players: [],
      newUsername: '',
      newPassword: '',
      newRole: '',
      newPlayerId: '',
      formNeedsValidation: false
    };
  },
  computed: {
    playersWithoutUser() {
      return this.players.filter(player => player.user === null);
    }
  },
  mounted() {
    this.fetchUsers();
    this.fetchPlayers();
  },
  methods: {
    fetchUsers() {
      // Fetch user from the API
      axios.get('/api/users')
          .then(response => {
            this.users = response.data;
          })
          .catch(error => {
            console.error('Error fetching users:', error);
          });
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
              // Update the players array with the new player
              this.users.push(response.data);

              // Clear the form input
              this.newUserName = '';

              this.fetchPlayers();
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

              this.fetchPlayers();
            })
            .catch(error => {
              console.error('Error deleting user:', error);
            });
      }
    },
  },
};
</script>
