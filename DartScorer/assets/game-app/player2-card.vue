<template>
  <div id="player-1" class="card border-light border-5">
    <div class="card-header">
      <div class="row">
        <div class="col-7 p-0">
          <h5>{{ game.player2.name }}</h5>
        </div>
        <div class="col-5 text-end p-0">
          <p>
            {{ game.player1Darts }}
            <img src="/homepage/assets/img/dart-arrow-24px.png" alt="">
          </p>
        </div>
      </div>
    </div>

    <ul class="list-group list-group-flush">
      <li class="list-group-item">
        <div class="row">
          <div class="col" style="padding-right: 0px;">
            <h1 style="font-size: 45px;">{{ game.startScore }}</h1>
          </div>
          <!--
          <div class="col text-end" style="padding-left: 0px;">
            <div class="row">
              <div class="col">
                <p class="m-0" style="font-size: 12px">T18 D18</p>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <p class="m-0" style="font-size: 12px">D20 D15</p>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <p class="m-0" style="font-size: 12px">BULL T20</p>
              </div>
            </div>
          </div>
          -->
        </div>

      </li>
      <li class="list-group-item">
        <h1 style="font-size: 15px">Last: 0</h1>
        <h1 style="font-size: 15px">Average: 0</h1>
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
export default {
  name: 'Player2CardComponent',
  props: {
    game: Object,
  }
};
</script>
