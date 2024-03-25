<template>
  <div class="row px-1">
    <div class="col p-1">
      <div id="game-header" class="card" style="padding: 5px; background-color: lightgray">
        <div class="row px-1">
          <div class="col-2 text-center p-0">
            <strong>ID: {{ game.id }}</strong>
          </div>
          <div class="col-3 text-center p-0">
            {{ game.finishType }} Out
          </div>
          <div class="col-3 text-center p-0">
            {{ game.matchMode }}
          </div>
          <div class="col-2 text-center p-0">
            Set: {{ game.matchModeSets }}
          </div>
          <div class="col-2 text-center p-0">
            Legs: {{ game.matchModeLegs }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GameHeaderComponent',
  props: {
    game: Object,
  }
};
</script>