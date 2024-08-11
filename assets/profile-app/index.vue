<template>
  <div class="row" style="margin-top: 15px">
    <div class="col-12">

      <div class="card shadow">
        <div class="card-header" >
          <h5 style="padding-top: 5px; color: white; margin: 0;">{{ user.username }}</h5>
        </div>

        <div class="card-body">
          <p>Player: {{ user.player.name }}</p>
          <p>User since: {{ formatDate(user.date) }}</p>
        </div>
      </div>

    </div>
  </div>

  <div class="row">
    <div class="col-6" style="margin-top: 15px">

      <div class="card shadow">
        <div class="card-header" >
          <h6 style="padding-top: 5px; color: white; margin: 0;">Games ({{totalGamesPlayed}})</h6>
        </div>

        <div class="card-body" >
          <p>Won: {{totalGamesWon}}</p>
          <p>Lost: {{totalGamesLost}}</p>
          <p>Open: {{liveGamesCount}}</p>
          <p>Finished: {{finishedGamesCount}}</p>
          <p>Winning: {{winningChance}}%</p>
        </div>
      </div>

    </div>

    <div class="col-6" style="margin-top: 15px">

      <div class="card shadow">
        <div class="card-header" >
          <h6 style="padding-top: 5px; color: white; margin: 0;">Darts ({{totalDartsThrown}})</h6>
        </div>

        <div class="card-body" >
          <p>Sum scores: {{totalScore}}</p>
          <p>Scores thrown: {{scoreCount}}</p>
        </div>
      </div>

    </div>
  </div>

  <div class="row">
    <div class="col-6" style="margin-top: 15px">

      <div class="card shadow">
        <div class="card-header" >
          <h6 style="padding-top: 5px; color: white; margin: 0;">Averages</h6>
        </div>

        <div class="card-body" >
          <p>Average score: {{averageScore}}</p>
        </div>
      </div>

    </div>

    <div class="col-6" style="margin-top: 15px">

      <div class="card shadow">
        <div class="card-header" >
          <h6 style="padding-top: 5px; color: white; margin: 0;">Checkouts ({{checkoutCount}})</h6>
        </div>

        <div class="card-body" >
          <p>Highest CO: {{highestCheckoutScore}}</p>
          <p>1 Dart COs: {{ checkout1DartCount }}</p>
          <p>2 Dart COs: {{ checkout2DartCount }}</p>
          <p>3 Dart COs: {{ checkout3DartCount }}</p>
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
      scores: JSON.parse(window.scores),
    };
  },

  mounted() {
    console.log(this.user);  // Log the user data to verify it's correctly loaded
    this.isLoading = false;
  },

  computed: {
    totalScore() {
      // Calculate the sum of all the score values
      return this.scores.reduce((sum, score) => sum + score.value, 0);
    },

    scoreCount() {
      // Check if scores is indeed an array and return its length
      if (Array.isArray(this.scores)) {
        return this.scores.length;
      }
      return 0;
    },

    totalDartsThrown() {
      // Calculate the total number of darts thrown
      if (Array.isArray(this.scores)) {
        return this.scores.reduce((total, score) => {
          return total + (score.dartsThrown || 0);
        }, 0);
      }
      return 0;
    },

    averageScore() {
      // Calculate the average score based on all entries
      if (Array.isArray(this.scores) && this.scores.length > 0) {
        const totalValue = this.scores.reduce((total, score) => {
          return total + (score.value || 0);
        }, 0);
        return (totalValue / this.scores.length).toFixed(2);
      }
      return 0;
    },

    checkoutCount() {
      // Count the number of scores with checkout set to true
      if (Array.isArray(this.scores)) {
        return this.scores.filter(score => score.checkout === true).length;
      }
      return 0;
    },

    highestCheckoutScore() {
      // Find the highest score among those where checkout is true
      if (Array.isArray(this.scores)) {
        return this.scores
            .filter(score => score.checkout === true)
            .reduce((max, score) => {
              return score.value > max ? score.value : max;
            }, 0);
      }
      return 0;
    },

    winningChance() {
      // Calculate the winning percentage based on games won and lost
      const totalGames = this.totalGamesWon + this.totalGamesLost;
      if (totalGames > 0) {
        return ((this.totalGamesWon / totalGames) * 100).toFixed(2);
      }
      return 0;
    },

    checkout1DartCount() {
      // Count the number of checkouts with exactly 1 dart
      if (Array.isArray(this.scores)) {
        return this.scores.filter(score => score.checkout === true && score.dartsThrown === 1).length;
      }
      return 0;
    },

    checkout2DartCount() {
      // Count the number of checkouts with exactly 2 darts
      if (Array.isArray(this.scores)) {
        return this.scores.filter(score => score.checkout === true && score.dartsThrown === 2).length;
      }
      return 0;
    },

    checkout3DartCount() {
      // Count the number of checkouts with exactly 3 darts
      if (Array.isArray(this.scores)) {
        return this.scores.filter(score => score.checkout === true && score.dartsThrown === 3).length;
      }
      return 0;
    },
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
