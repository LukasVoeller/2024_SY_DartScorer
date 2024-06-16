<template>
  <div id="player" class="card">
    <div class="card-header" :style="{
      backgroundColor: toThrow ? '#50BE96' : '',
      color: toThrow ? 'white' : 'black',
      height: '40px'
    }">
      <div class="row">
        <div class="col-7 p-0">
          <h5 :style="{
            color: toThrow ? 'white' : 'black'
          }">
            {{ playerName }}</h5>
        </div>
        <div class="col-5 text-end p-0">
          <p>
            {{ dartsThrown }}
            <img
                src="/homepage/assets/img/dart-arrow-32px.png"
                alt="dart arrow"
                style="max-width: 20px"
                :style="{
              filter: toThrow ? 'invert(100%)' : 'none'  // Conditional filter
            }"
            >
          </p>
        </div>
      </div>
    </div>

    <ul class="list-group list-group-flush" style="height: 120px;">
      <li class="list-group-item p-0">
        <div class="row justify-content-center">
          <div class="col-auto">
            <h1 :style="{
              fontSize: dynamicFontSize,
              lineHeight: '1',
              paddingTop: dynamicPadding,
              margin: '0px',
              color: isBogey(playerScore) ? '#FF5E5E' : 'black'}">
              <strong>{{ displayScore }}</strong>
            </h1>
          </div>
        </div>

        <!-- <CalculateCheckouts :score="playerScore" @checkouts-calculated="calculatedCheckouts"/> -->
        <CalculateCheckouts v-if="!scoreBusted" :score="playerScore" @checkouts-calculated="calculatedCheckouts" />

      </li>
    </ul>

    <ul class="list-group list-group-flush" style="margin-top: auto;">
      <li class="list-group-item">
        <h1 class="info-text" style="font-size: 15px; white-space: nowrap; overflow: hidden;">Last: {{ lastThrows }}</h1>

        <div class="row">
          <div class="col" style="padding-right: 0;">
            <h1 class="info-text" style="font-size: 15px; margin: 0px;">Leg:</h1>
            <h1 class="info-text" style="font-size: 15px; margin: 0px;">&empty; {{ legAverage }}</h1>
          </div>
          <div class="col">
            <h1 class="info-text" style="font-size: 15px; margin: 0px;">Game:</h1>
            <h1 class="info-text" style="font-size: 15px; margin: 0px;">&empty; {{ gameAverage }}</h1>
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

export default {
  name: 'PlayerCardComponent',
  components: {
    CalculateCheckouts,
  },
  props: {
    playerName: String,
    playerScore: Number,
    scoreBusted: Boolean,
    lastThrows: String,
    legAverage: String,
    gameAverage: String,
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
      this.possibleCheckouts = checkouts;
    }
  },

  computed: {
    displayScore() {
      if (this.scoreBusted) {
        return 'bust';
      } else {
        return this.playerScore;
      }
    },

    dynamicPadding() {
      if (this.displayScore === "bust"){
        return '30px';
      }

      if (this.possibleCheckouts === 0) {
        return '25px'; // Font size when there are checkouts
      } else if (this.possibleCheckouts === 1) {
        return '18px'; // Font size when there are no checkouts
      } else if (this.possibleCheckouts === 2 || this.possibleCheckouts === 3) {
        return '17px'; // Font size when there are no checkouts
      }
    },

    dynamicFontSize() {
      if (this.displayScore === "bust"){
        return '75px';
      }

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