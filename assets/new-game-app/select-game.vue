<template>
  <!-- Game Mode -->
  <div class="row">
    <div class="col">
      <div class="btn-group" role="group" style="width: 100%">
        <input type="radio" class="btn-check" name="gameMode" id="btnradio1" autocomplete="off"
               v-model="selectedGameMode" value="X01" @change="emitGameMode" checked required>
        <label class="btn btn-outline-success" for="btnradio1">X01</label>

        <input type="radio" class="btn-check" name="gameMode" id="btnradio2" autocomplete="off" :disabled=true
               v-model="selectedGameMode" value="Cricket" @change="emitGameMode" required>
        <label class="btn btn-outline-success" for="btnradio2">Cricket</label>

        <input type="radio" class="btn-check" name="gameMode" id="btnradio3" autocomplete="off" :disabled=true
               v-model="selectedGameMode" value="Shanghai" @change="emitGameMode" required>
        <label class="btn btn-outline-success" for="btnradio3">Shanghai</label>
      </div>
    </div>
  </div>

  <br>

  <!-- Game Mode Options -->
  <div class="row">
    <div class="col" v-if="selectedGameMode === 'X01'">
      <div class="btn-group" role="group" style="width: 100%">
        <input type="radio" class="btn-check" name="startScoreX01" id="btnradio4" autocomplete="off"
               v-model="selectedStartScoreX01" :value=501 @change="emitStartScoreX01" checked required>
        <label class="btn btn-outline-success" for="btnradio4">501</label>

        <input type="radio" class="btn-check" name="startScoreX01" id="btnradio5" autocomplete="off"
               v-model="selectedStartScoreX01" :value=301 @change="emitStartScoreX01" required>
        <label class="btn btn-outline-success" for="btnradio5">301</label>

        <input type="radio" class="btn-check" name="startScoreX01" id="btnradio6" autocomplete="off"
               v-model="selectedStartScoreX01" :value=101 @change="emitStartScoreX01" required>
        <label class="btn btn-outline-success" for="btnradio6">101</label>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col" v-if="selectedGameMode === 'X01'">
      <br>
      <div class="btn-group" role="group" style="width: 100%">
        <input type="radio" class="btn-check" name="finishType" id="btnradio7" autocomplete="off"
               v-model="selectedFinishType" value="Double" @change="emitFinishType" checked required>
        <label class="btn btn-outline-success" for="btnradio7">Double</label>

        <input type="radio" class="btn-check" name="finishType" id="btnradio8" autocomplete="off"
               v-model="selectedFinishType" value="Master" @change="emitFinishType" required>
        <label class="btn btn-outline-success" for="btnradio8">Master</label>

        <input type="radio" class="btn-check" name="finishType" id="btnradio9" autocomplete="off"
               v-model="selectedFinishType" value="Straight" @change="emitFinishType" required>
        <label class="btn btn-outline-success" for="btnradio9">Straight</label>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col" v-if="selectedGameMode === 'Cricket'">
      <div class="btn-group" role="group" style="width: 100%">
        <input type="radio" class="btn-check" name="startScoreCricket" id="btnradio10" autocomplete="off"
               v-model="selectedStartScoreCricket" value="15-BULL" checked required>
        <label class="btn btn-outline-success" for="btnradio10">15-BULL</label>

        <input type="radio" class="btn-check" name="startScoreCricket" id="btnradio11" autocomplete="off"
               v-model="selectedStartScoreCricket" value="Random 5" required>
        <label class="btn btn-outline-success" for="btnradio11">Random 5</label>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col" v-if="selectedGameMode === 'Shanghai'">
      <div class="btn-group" role="group" style="width: 100%">
        <input type="radio" class="btn-check" name="startScoreShanghai" id="btnradio12" autocomplete="off"
               v-model="selectedStartScoreShanghai" value="1-7" checked required>
        <label class="btn btn-outline-success" for="btnradio12">1-7</label>

        <input type="radio" class="btn-check" name="startScoreShanghai" id="btnradio13" autocomplete="off"
               v-model="selectedStartScoreShanghai" value="1-20" required>
        <label class="btn btn-outline-success" for="btnradio13">1-20</label>
      </div>
    </div>
  </div>

  <br>

  <!-- Match Mode Options -->
  <div class="row">
    <div class="col">
      <div class="btn-group" role="group" style="width: 100%">

        <input type="radio" class="btn-check" name="matchMode" id="btnradio14" autocomplete="off"
               v-model="selectedMatchMode" value="FirstToLegs" @change="emitMatchMode" checked required>
        <label class="btn btn-outline-success" for="btnradio14">First to Legs</label>

        <input type="radio" class="btn-check" name="matchMode" id="btnradio15" autocomplete="off"
               v-model="selectedMatchMode" value="FirstToSets" @change="emitMatchMode" required>
        <label class="btn btn-outline-success" for="btnradio15">First to Sets</label>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col" v-if="selectedMatchMode === 'FirstToSets'">
      <br>
      <p style="color: white">Sets:</p>
      <div class="btn-group" role="group" style="width: 100%">
        <input type="radio" class="btn-check" name="matchModeSets" id="btnradio16" autocomplete="off"
               v-model="selectedMatchModeSets" :value=2 @change="emitMatchModeSets" checked required>
        <label class="btn btn-outline-success" for="btnradio16">2</label>

        <input type="radio" class="btn-check" name="matchModeSets" id="btnradio17" autocomplete="off"
               v-model="selectedMatchModeSets" :value=3 @change="emitMatchModeSets" required>
        <label class="btn btn-outline-success" for="btnradio17">3</label>

        <input type="radio" class="btn-check" name="matchModeSets" id="btnradio18" autocomplete="off"
               v-model="selectedMatchModeSets" :value=4 @change="emitMatchModeSets" required>
        <label class="btn btn-outline-success" for="btnradio18">4</label>
      </div>
    </div>

    <div class="col" v-if="selectedMatchMode === 'FirstToSets'">
      <br>
      <p style="color: white;">Legs:</p>
      <div class="btn-group" role="group" style="width: 100%">
        <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio19" autocomplete="off"
               v-model="selectedMatchModeSetsLegs" :value=2 @change="emitMatchModeSetsLegs" checked required>
        <label class="btn btn-outline-success" for="btnradio19">2</label>

        <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio20" autocomplete="off"
               v-model="selectedMatchModeSetsLegs" :value=3 @change="emitMatchModeSetsLegs" required>
        <label class="btn btn-outline-success" for="btnradio20">3</label>

        <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio21" autocomplete="off"
               v-model="selectedMatchModeSetsLegs" :value=4 @change="emitMatchModeSetsLegs" required>
        <label class="btn btn-outline-success" for="btnradio21">4</label>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col" v-if="selectedMatchMode === 'FirstToLegs'">
      <br>
      <p style="color: white">Legs:</p>
      <div class="btn-group" role="group" style="width: 100%">
        <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio19" autocomplete="off"
               v-model="selectedMatchModeLegs" :value=1 @change="emitMatchModeLegs" checked required>
        <label class="btn btn-outline-success" for="btnradio19">1</label>

        <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio20" autocomplete="off"
               v-model="selectedMatchModeLegs" :value=2 @change="emitMatchModeLegs" required>
        <label class="btn btn-outline-success" for="btnradio20">2</label>

        <input type="radio" class="btn-check" name="matchModeLegs" id="btnradio21" autocomplete="off"
               v-model="selectedMatchModeLegs" :value=3 @change="emitMatchModeLegs" required>
        <label class="btn btn-outline-success" for="btnradio21">3</label>
      </div>
    </div>
  </div>
</template>

<script lang="ts">

import axios from "axios";
import {defineComponent} from "vue";

export default defineComponent({
  name: 'GameSelection',

  data() {
    return {
      selectedGameMode: "X01" as String,
      selectedStartScoreX01: 101 as Number,
      selectedFinishType: "Double" as String,
      selectedStartScoreCricket: 0 as Number,
      selectedStartScoreShanghai: 0 as Number,
      selectedMatchMode: "FirstToSets" as String,
      selectedMatchModeSets: 2 as Number,
      selectedMatchModeSetsLegs: 2 as Number,
      selectedMatchModeLegs: 3 as Number,
    }
  },

  methods: {
    emitGameMode() {
      this.$emit('update:selectedGameMode', String(this.selectedGameMode));
    },

    emitStartScoreX01() {
      this.$emit('update:selectedStartScoreX01', Number(this.selectedStartScoreX01));
    },

    emitFinishType() {
      this.$emit('update:selectedFinishType', String(this.selectedFinishType));
    },

    emitMatchMode() {
      this.$emit('update:selectedMatchMode', String(this.selectedMatchMode));
    },

    emitMatchModeSets() {
      this.$emit('update:selectedMatchModeSets', Number(this.selectedMatchModeSets));
    },

    emitMatchModeSetsLegs() {
      this.$emit('update:selectedMatchModeSetsLegs', Number(this.selectedMatchModeSetsLegs));
    },

    emitMatchModeLegs() {
      this.$emit('update:selectedMatchModeLegs', Number(this.selectedMatchModeLegs));
    },
  },

})
</script>