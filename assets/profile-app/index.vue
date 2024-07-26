<template>
  <div class="row" style="margin-top: 15px">
    <div class="col-12">

      <div class="card shadow">
        <div class="card-header" >
          <h5 style="padding-top: 5px; color: white; margin: 0;">{{ this.user.username }}</h5>
        </div>

        <div class="card-body">
          <p>Player: {{ this.user.player.name }}</p>
          <p>User since: {{ formatDate(this.user.date) }}</p>
        </div>
      </div>

    </div>
  </div>

  <div class="row">
    <div class="col-6" style="margin-top: 15px">

      <div class="card shadow">
        <div class="card-header" >
          <h5 style="padding-top: 5px; color: white; margin: 0;">Games ({{this.totalGamesPlayed}})</h5>
        </div>

        <div class="card-body" >
          <p>Won: {{this.totalGamesWon}}</p>
          <p>Lost: {{this.totalGamesLost}}</p>
          <p>Open: {{this.liveGamesCount}}</p>
          <p>Finished: {{this.finishedGamesCount}}</p>
          <p>Winning: </p>
        </div>
      </div>

    </div>

    <div class="col-6" style="margin-top: 15px">

      <div class="card shadow">
        <div class="card-header" >
          <h5 style="padding-top: 5px; color: white; margin: 0;">Darts</h5>
        </div>

        <div class="card-body" >
          <p>Sample text</p>
          <p>Sample text</p>
        </div>
      </div>

    </div>
  </div>

  <div class="row">
    <div class="col-6" style="margin-top: 15px">

      <div class="card shadow">
        <div class="card-header" >
          <h5 style="padding-top: 5px; color: white; margin: 0;">Averages</h5>
        </div>

        <div class="card-body" >
          <p>Sample text</p>
          <p>Sample text</p>
        </div>
      </div>

    </div>

    <div class="col-6" style="margin-top: 15px">

      <div class="card shadow">
        <div class="card-header" >
          <h5 style="padding-top: 5px; color: white; margin: 0;">Checkouts</h5>
        </div>

        <div class="card-body" >
          <p>Sample text</p>
          <p>Sample text</p>
        </div>
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
  name: 'ProfileComponent',

  components: {
    VueSpinnerDots,
  },

  data() {
    return {
      isLoading: true,
      user: window.currentUser || {},
      totalGamesPlayed: window.totalGamesPlayed || 0,
      totalGamesWon: window.totalGamesWon || 0,
      totalGamesLost: window.totalGamesLost || 0,
      liveGamesCount: window.liveGamesCount || 0,
      finishedGamesCount: window.finishedGamesCount || 0,
    };
  },

  mounted() {
    console.log(this.user);  // Log the user data to verify it's correctly loaded
    this.isLoading = false;
  },

  computed: {

  },

  methods: {
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      const date = new Date(dateString);
      return date.toLocaleDateString(undefined, options);
    }
  },
};
</script>
