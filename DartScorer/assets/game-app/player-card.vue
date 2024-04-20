<template>
  <div id="player" class="card">
    <div class="card-header" :style="{ backgroundColor: toThrow ? 'lightyellow' : '' }">
      <div class="row">
        <div class="col-7 p-0">
          <h5>{{ playerName }}</h5>
        </div>
        <div class="col-5 text-end p-0">
          <p>
            {{ dartsThrown }}
            <img src="/homepage/assets/img/dart-arrow-32px.png" alt="" style="max-width: 20px">
          </p>
        </div>
      </div>
    </div>

    <ul class="list-group list-group-flush" style="/*flex-grow: 1;*/ height: 120px;">
      <li class="list-group-item p-0">
        <div class="row justify-content-center">
          <div class="col-auto">
            <h1 :style="{ fontSize: dynamicFontSize, lineHeight: '1', paddingTop: dynamicPadding, margin: '0px', color: isBogey(score) ? 'red' : 'black'}"><strong>{{ displayScore }}</strong></h1>
          </div>
        </div>

        <CalculateCheckouts :score="score" @checkouts-calculated="calculatedCheckouts" />
      </li>
    </ul>

    <ul class="list-group list-group-flush" style="margin-top: auto;">
      <li class="list-group-item">
        <h1 style="font-size: 15px; white-space: nowrap; overflow: hidden;">Last: {{ lastThrow }}</h1>

        <div class="row">
          <div class="col">
            <h1 style="font-size: 15px; margin: 0px;">Leg</h1>
            <h1 style="font-size: 15px; margin: 0px;">Avg.: 0</h1>
          </div>
          <div class="col">
            <h1 style="font-size: 15px; margin: 0px;">Game</h1>
            <h1 style="font-size: 15px; margin: 0px;">Avg.: 0</h1>
          </div>
        </div>

      </li>
      <li class="list-group-item">
        <div class="row">
          <div class="col">
            Sets: {{ sets }}
          </div>
          <div class="col">
            Legs: {{ legs }}
          </div>
        </div>
      </li>
    </ul>
  </div>

</template>

<script>
import CalculateCheckouts from './calculate-checkouts.vue';
import calculateCheckouts from "./calculate-checkouts.vue";

export default {
  name: 'PlayerCardComponent',
  components: {
    CalculateCheckouts,
  },
  props: {
    playerName: String,
    score: Number,
    lastThrow: Array,
    legAverage: Number,
    gameAverage: Number,
    toThrow: Boolean,
    dartsThrown: Number,
    sets: Number,
    legs: Number
  },
  data() {
    return {
      possibleCheckouts: null // Initialize with null or appropriate default value
    };
  },
  methods: {
    isBogey(score) {
      const bogeyScores = [169, 168, 166, 165, 163, 162, 159];
      return bogeyScores.includes(score);
    },
    calculatedCheckouts(checkouts) {
      //console.log("Possible checkouts: " + checkouts);
      this.possibleCheckouts = checkouts; // Store the checkouts in data
    }
  },
  computed: {
    // TODO: Delete
    calculateCheckouts() {
      return calculateCheckouts
    },
    displayScore() {
      return this.score < 0 ? "bust" : this.score;
    },
    dynamicPadding() {
      if (this.possibleCheckouts === 0) {
        return '25px'; // Font size when there are checkouts
      } else if (this.possibleCheckouts === 1) {
        return '18px'; // Font size when there are no checkouts
      } else if (this.possibleCheckouts === 2 || this.possibleCheckouts === 3) {
        return '17px'; // Font size when there are no checkouts
      }
    },
    dynamicFontSize() {
      if (this.possibleCheckouts === 0) {
        return '85px'; // Font size when there are checkouts
      } else if (this.possibleCheckouts === 1) {
        return '65px'; // Font size when there are no checkouts
      } else if (this.possibleCheckouts === 2 || this.possibleCheckouts === 3) {
        return '45px'; // Font size when there are no checkouts
      }
    }
  }
};
</script>

<style scoped>

</style>