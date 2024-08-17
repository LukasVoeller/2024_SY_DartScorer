<template>
  <div id="player" class="card flex-grow-1 d-flex flex-column" style="height: 100%">
    <!-- Card Header -->
    <div class="card-header p-0" :style="{
      backgroundColor: toThrow ? '#2CAB73' : '#282828',
      color: toThrow ? 'white' : 'black',
      height: '40px',
      border: '1px solid grey'
    }">
      <div class="row h-100" style="padding-left: 12px; padding-right: 12px">
        <!-- Starting Player Leg and Set Indicator -->
        <template v-if="startingPlayerLeg && startingPlayerSet">
          <div class="col-6 p-0 d-flex align-items-center">
            <span :style="{
              color: toThrow ? 'white' : 'white',
              'white-space': 'nowrap',
              'padding-left': '8px'
            }">
              {{ playerName }}
            </span>&nbsp;
          </div>

          <div class="col-4 p-0 d-flex align-items-center text-end">
            <span class="text-end" style="width: 100%; color: white">
              {{ dartsThrown }}
              <img
                  src="/homepage/assets/img/dart-arrow-32px.png"
                  alt="dart arrow"
                  style="max-width: 20pt; filter: invert(100%); padding-right: 8px;"
              >
            </span>
          </div>

          <div class="col-2">
            <div class="row h-100" style="flex-direction: column; background-color: #2CAB73; border-top-right-radius: 0.25rem;">
              <div class="col-12 text-center p-0" style="display: flex; align-items: center; justify-content: center; flex: 1; border-left: 1px solid #ccc; border-bottom: 1px solid #ccc; border-top-right-radius: 0.3rem; background-color: #505050">
                <span v-if="startingPlayerLeg" style="font-size: 7pt; color: white">
                  LEG
                </span>
                        </div>
                        <div class="col-12 text-center p-0" style="display: flex; align-items: center; justify-content: center; flex: 1; border-left: 1px solid #ccc; border-bottom: 1px solid #ccc; background-color: #3C3C3C">
                <span v-if="startingPlayerSet" style="font-size: 7pt; color: white;">
                  SET
                </span>
              </div>
            </div>
          </div>
        </template>

        <!-- Other Conditions for Player Indicators -->
        <template v-else-if="startingPlayerLeg || startingPlayerSet">
          <div class="col-6 p-0 d-flex align-items-center">
            <span :style="{
              color: toThrow ? 'white' : 'white',
              'white-space': 'nowrap',
              'padding-left': '8px'
            }">
              {{ playerName }}
            </span>&nbsp;
          </div>

          <div class="col-4 p-0 d-flex align-items-center text-end">
            <span class="text-end" style="width: 100%; color: white">
              {{ dartsThrown }}
              <img
                  src="/homepage/assets/img/dart-arrow-32px.png"
                  alt="dart arrow"
                  style="max-width: 20pt; filter: invert(100%); padding-right: 8px;"
              >
            </span>
          </div>

          <div class="col-2">
            <div class="row h-100" style="background-color: #2CAB73; border-top-right-radius: 0.25rem;">
              <template v-if="startingPlayerLeg">
                <div class="col-12 text-center p-0" style="display: flex; align-items: center; justify-content: center; flex: 1; border-left: 1px solid gray; border-top-right-radius: 0.3rem; background-color: #505050">
                  <span v-if="startingPlayerLeg" style="font-size: 7pt; color: white">
                    LEG
                  </span>
                </div>
              </template>
              <template v-else-if="startingPlayerSet">
                <div class="col-12 text-center p-0" style="display: flex; align-items: center; justify-content: center; flex: 1; border-left: 1px solid gray; background-color: #3C3C3C">
                  <span v-if="startingPlayerSet" style="font-size: 7pt; color: white;">
                    SET
                  </span>
                </div>
              </template>
            </div>
          </div>
        </template>

        <!-- No Starting Player Leg or Set -->
        <template v-else-if="!startingPlayerLeg && !startingPlayerSet">
          <div class="col-8 p-0 d-flex align-items-center">
            <span :style="{
              color: toThrow ? 'white' : 'white',
              'white-space': 'nowrap',
              'padding-left': '8px',
            }">
              {{ playerName }}
            </span>&nbsp;
          </div>

          <div class="col-4 p-0 d-flex align-items-center text-end">
            <span class="text-end" style="width: 100%; color: white">
              {{ dartsThrown }}
              <img
                  src="/homepage/assets/img/dart-arrow-32px.png"
                  alt="dart arrow"
                  style="max-width: 20pt; filter: invert(100%); padding-right: 8px;"
              >
            </span>
          </div>
        </template>

      </div>
    </div>

    <!-- Main Score Display -->
    <div class="list-group-flush flex-grow-1 d-flex flex-column" style="border-left: 1px solid gray; border-right: 1px solid gray; min-height: 130px;">
      <li class="list-group-item p-0 flex-grow-1 d-flex flex-column justify-content-center">
        <div class="row justify-content-center">
          <div class="col-auto">
            <h1 :style="{
              fontSize: '45pt',
              lineHeight: '1',
              paddingTop: '1vh',
              margin: '0px',
              color: isBogey(playerScore) ? '#FF5E5E' : 'white'}">
              <strong>{{ displayScore }}</strong>
            </h1>
          </div>
        </div>

        <!-- Calculate Checkouts Component -->
        <CalculateCheckouts v-if="!scoreBusted" :score="playerScore" @checkouts-calculated="calculatedCheckouts" />

      </li>
    </div>

    <!-- Footer Information -->
    <ul class="list-group list-group-flush" style="border-left: 1px solid gray; border-right: 1px solid gray;">
      <li class="list-group-item">
        <h1 class="info-text" style="font-size: 10pt; white-space: nowrap; overflow: hidden; color: white">Last: {{ lastThrows }}</h1>

        <div class="row">
          <div class="col" style="padding-right: 0;">
            <h1 class="info-text" style="font-size: 10pt; margin: 0px; color: white">Leg:</h1>
            <h1 class="info-text" style="font-size: 10pt; margin: 0px; color: white">&empty; {{ legAverage }}</h1>
          </div>
          <div class="col">
            <h1 class="info-text" style="font-size: 10pt; margin: 0px; color: white">Game:</h1>
            <h1 class="info-text" style="font-size: 10pt; margin: 0px; color: white">&empty; {{ gameAverage }}</h1>
          </div>
        </div>

      </li>
      <li class="list-group-item" style="border-bottom: 1px solid gray;">
        <div class="row">
          <div class="col pe-0" style="color: white">
            Sets: {{ sets }}
          </div>
          <div class="col p-0" style="color: white">
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