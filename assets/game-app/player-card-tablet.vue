<template>
  <div id="player" class="card" style="height: 550px">
    <div class="card-header p-0" :style="{
      backgroundColor: toThrow ? '#2CAB73' : '',
      color: toThrow ? 'white' : 'black',
      height: '50px'
    }"> <!-- 40px --->
      <div class="row h-100" style="padding-left: 12px; padding-right: 12px">

        <!-- TODO: Make Leg/Set-Start-Component -->

        <template v-if="startingPlayerLeg && startingPlayerSet">
          <div class="col-6 p-0 d-flex align-items-center">
          <span  :style="{
            color: toThrow ? 'white' : 'black',
            'white-space': 'nowrap',
            'padding-left': '8px',
            'font-size': '15pt'
          }">
            {{ playerName }}
          </span>&nbsp;
          </div>

          <div class="col-4 p-0 d-flex align-items-center text-end">
            <span class="text-end" style="width: 100%; font-size: 15pt">
              {{ dartsThrown }}
              <img
                  src="/homepage/assets/img/dart-arrow-32px.png"
                  alt="dart arrow"
                  style="max-width: 28px;"
                  :style="{
                filter: toThrow ? 'invert(100%)' : 'none',
                'padding-right': '8px'
              }"
              >
            </span>
          </div>

          <div class="col-2">
            <div class="row" style="background-color: white; border-top-right-radius: 0.25rem;">
              <div class="col-12 text-center p-0" style="height: 25px; border-left: 1px solid #ccc; border-bottom: 1px solid #ccc; border-top-right-radius: 0.3rem; background-color: #536379">
                <span v-if="startingPlayerLeg" style="font-size: 7pt; vertical-align: 0px; color: white">
                 LEG
                </span>
              </div>
              <div class="col-12 text-center p-0" style="height: 25px; border-left: 1px solid #ccc; border-bottom: 1px solid #ccc; background-color: #343E4C">
                <span v-if="startingPlayerSet" style="font-size: 7pt; vertical-align: 0px; color: white;">
                  SET
                </span>
              </div>
            </div>
          </div>
        </template>



        <template v-else-if="startingPlayerLeg || startingPlayerSet">
          <div class="col-6 p-0 d-flex align-items-center">
          <span  :style="{
            color: toThrow ? 'white' : 'black',
            'white-space': 'nowrap',
            'padding-left': '8px',
            'font-size': '15pt'
          }">
            {{ playerName }}
          </span>&nbsp;
          </div>

          <div class="col-4 p-0 d-flex align-items-center text-end">
            <span class="text-end" style="width: 100%; font-size: 15pt">
              {{ dartsThrown }}
              <img
                  src="/homepage/assets/img/dart-arrow-32px.png"
                  alt="dart arrow"
                  style="max-width: 28px;"
                  :style="{
                filter: toThrow ? 'invert(100%)' : 'none',
                'padding-right': '8px'
              }"
              >
            </span>
          </div>

          <div class="col-2">
            <div class="row h-100" style="background-color: white; border-top-right-radius: 0.25rem;">
              <template v-if="startingPlayerLeg">
              <div class="col-12 text-center p-0" style="border-left: 1px solid #ccc; border-top-right-radius: 0.3rem; background-color: #536379">
                <span v-if="startingPlayerLeg" style="font-size: 10pt; vertical-align: -10px; color: white">
                 LEG
                </span>
              </div>
              </template>
              <template v-else-if="startingPlayerSet">
              <div class="col-12 text-center p-0" style="border-left: 1px solid #ccc; border-top-right-radius: 0.3rem; background-color: #343E4C">
                <span v-if="startingPlayerSet" style="font-size: 10pt; vertical-align: -10px; color: white;">
                  SET
                </span>
              </div>
              </template>
            </div>
          </div>
        </template>



        <template v-else-if="!startingPlayerLeg && !startingPlayerSet">
          <div class="col-8 p-0 d-flex align-items-center">
          <span  :style="{
            color: toThrow ? 'white' : 'black',
            'white-space': 'nowrap',
            'padding-left': '8px',
            'font-size': '15pt'
          }">
            {{ playerName }}
          </span>&nbsp;
          </div>

          <div class="col-4 p-0 d-flex align-items-center text-end">
            <span class="text-end" style="width: 100%; font-size: 15pt">
              {{ dartsThrown }}
              <img
                  src="/homepage/assets/img/dart-arrow-32px.png"
                  alt="dart arrow"
                  style="max-width: 28px;"
                  :style="{
                filter: toThrow ? 'invert(100%)' : 'none',
                'padding-right': '8px'
              }"
              >
            </span>
          </div>
        </template>




      </div>
    </div>

    <ul class="list-group list-group-flush" style="height: 175px;">
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
        <CalculateCheckouts v-if="!scoreBusted" :score="playerScore" :isTablet="true" @checkouts-calculated="calculatedCheckouts" />

      </li>
    </ul>

    <!-- Scrollable table for last throws -->
    <div class="list-group-item" style="height: 250px; overflow-y: auto; white-space: nowrap; scrollbar-width: none;" ref="scoreTableContainer">
      <table class="table table-bordered table-striped" style="margin: 0px">
        <thead>
        <tr style="font-size: 16pt;">
          <th style="position: sticky; top: 0; text-align: left">Score</th>
          <th style="position: sticky; top: 0; text-align: left">Points</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(score, index) in reversedLastThrowsArray" :key="score" style="font-size: 16pt">
          <td>{{ score }}</td>
          <td>{{ calculateScore(index) }}</td>
        </tr>
        </tbody>
      </table>
    </div>


    <ul class="list-group list-group-flush" style="margin-top: auto;">
      <li class="list-group-item">

        <!-- Display lastThrows in a table -->
        <!--
        <div class="list-group-item">
          <table class="table table-bordered">
            <thead>
            <tr>
              <th>Score</th>
              <th>Points</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="score in lastThrows" :key="score">
              <td>{{ score }}</td>
              <td>501</td>
            </tr>
            </tbody>
          </table>
        </div>
        -->

        <!--
        <h1 class="info-text" style="font-size: 15px; white-space: nowrap; overflow: hidden;">Last: {{ lastThrows }}</h1>
        -->

        <div class="row">
          <div class="col" style="padding-right: 0;">
            <h1 class="info-text" style="font-size: 20px; margin: 0px;">Leg:</h1>
            <h1 class="info-text" style="font-size: 20px; margin: 0px;">&empty; {{ legAverage }}</h1>
          </div>
          <div class="col">
            <h1 class="info-text" style="font-size: 20px; margin: 0px;">Game:</h1>
            <h1 class="info-text" style="font-size: 20px; margin: 0px;">&empty; {{ gameAverage }}</h1>
          </div>
        </div>

      </li>
      <li class="list-group-item">
        <div class="row">
          <div v-if="matchMode === 'FirstToSets'" class="col" style="font-size: 18pt">
            Sets: {{ sets }}
          </div>
          <div class="col text-center" style="font-size: 18pt">
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
  name: 'PlayerCardTabletComponent',
  components: {
    CalculateCheckouts,
  },

  props: {
    matchMode: String,
    playerName: String,
    playerScore: Number,
    scoreBusted: Boolean,
    lastThrows: String,
    legAverage: String,
    gameAverage: String,
    toThrow: Boolean,
    startingPlayerLeg: Boolean,
    startingPlayerSet: Boolean,
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
    calculateScore(index) {
      let cumulativeScore = 501;
      for (let i = 0; i <= index; i++) {
        cumulativeScore -= this.reversedLastThrowsArray[i];
      }
      console.log("---> ", cumulativeScore)
      return cumulativeScore;
    },

    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.scoreTableContainer;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },

    isBogey(score) {
      const bogeyScores = [169, 168, 166, 165, 163, 162, 159];
      return bogeyScores.includes(score);
    },

    calculatedCheckouts(checkouts) {
      this.possibleCheckouts = checkouts;
    }
  },

  computed: {
    reversedLastThrowsArray() {
      if (!this.lastThrows) return [];
      return this.lastThrows.slice().reverse(); // Ensure we work on a copy and reverse it
    },

    displayScore() {
      if (this.scoreBusted) {
        return 'bust';
      } else {
        return this.playerScore;
      }
    },

    dynamicPadding() {
      if (this.displayScore === "bust"){
        return '55px';
      }

      if (this.possibleCheckouts === 0) {
        return '45px'; // Font size when there are checkouts
      } else if (this.possibleCheckouts === 1) {
        return '35px'; // Font size when there are no checkouts
      } else if (this.possibleCheckouts === 2 || this.possibleCheckouts === 3) {
        return '20px'; // Font size when there are no checkouts
      }
    },

    dynamicFontSize() {
      if (this.displayScore === "bust"){
        return '75px';
      }

      if (this.possibleCheckouts === 0) {
        return '100px'; // Font size when there are checkouts
      } else if (this.possibleCheckouts === 1) {
        return '85px'; // Font size when there are no checkouts
      } else if (this.possibleCheckouts === 2 || this.possibleCheckouts === 3) {
        return '85px'; // Font size when there are no checkouts
      }
    }
  },

  watch: {
    // Watch for changes in lastThrows and scroll to bottom
    reversedLastThrowsArray(newVal, oldVal) {
      this.scrollToBottom();
    }
  },

  mounted() {
    this.scrollToBottom();
  },

};
</script>

<style scoped>

</style>