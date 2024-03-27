<template>
  <!--
  <div :style="{ backgroundColor: toThrow ? 'lightblue' : '' }" id="player-2" class="card border-light border-5">
  -->
  <div :class="{ 'border-warning': toThrow, 'border-light': !toThrow }" id="player-2" class="card border-5" style="width: 100%;">
    <div class="card-header">
      <div class="row">
        <div class="col-7 p-0">
          <h5>{{ game.player2.name }}</h5>
        </div>
        <div class="col-5 text-end p-0">
          <p>
            {{ dartsThrown }}
            <img src="/homepage/assets/img/dart-arrow-32px.png" alt="" style="max-width: 20px">
          </p>
        </div>
      </div>
    </div>

    <ul class="list-group list-group-flush">
      <li class="list-group-item">
        <div class="row justify-content-center">
          <div class="col-auto">
            <h1 :style="{ fontSize: '45px', margin: '0px', color: isBogey(score) ? 'red' : 'black' }"><strong>{{
                score
              }}</strong></h1>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-auto">
            <CalculateCheckouts :score="score"/>
          </div>
        </div>
      </li>

      <li class="list-group-item">
        <h1 style="font-size: 15px">Last: 0</h1>
        <h1 style="font-size: 15px; margin: 0px;">Average: 0</h1>
      </li>
      <li class="list-group-item">
        <div class="row">
          <div class="col text-center p-0">
            Sets: {{ game.player2Sets }}
          </div>
          <div class="col text-center p-0">
            Legs: {{ game.player2Legs }}
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import CalculateCheckouts from './calculate-checkouts.vue';

export default {
  name: 'Player1CardComponent',
  components: {
    CalculateCheckouts,
  },
  props: {
    game: Object,
    score: Number,
    toThrow: Boolean,
    dartsThrown: Number
  },
  methods: {
    isBogey(score) {
      const bogeyScores = [169, 168, 166, 165, 163, 162, 159];
      return bogeyScores.includes(score);
    }
  }
};
</script>

<style scoped>

</style>