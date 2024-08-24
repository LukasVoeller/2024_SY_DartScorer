<template>
  <div class="pyro" v-if="winner === 1">
    <div class="before"></div>
    <div class="after"></div>
  </div>

  <!-- Modal -->
    <div class="modal fade" id="gameShutModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="gameShutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <div class="modal-title-wrapper">
            <h1 class="modal-title fs-5">Game shot and the match!</h1>
          </div>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col">
              <button type="button" style="width: 100%;" class="btn btn-secondary" data-bs-dismiss="modal" @click="homeModal">Home</button>
            </div>
            <div class="col">
              <button type="button" style="width: 100%;" class="btn btn-secondary" data-bs-dismiss="modal" @click="newGameModal">Rematch</button>
            </div>
            <div class="col">
              <button type="button" style="width: 100%;" class="btn btn-success" data-bs-dismiss="modal" @click="detailsModal">Details</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import {EventBus} from '../../event-bus';

export default {
  name: 'GameShutModalComponent',

  data() {
    return {
      winner: ""
    };
  },

  created() {
    EventBus.on('show-game-shut-modal', (payload) => {
      // Reset dartsForCheckout to default
      //console.log("--->", payload);
      this.winner = payload;
      const modal = new bootstrap.Modal(document.getElementById('gameShutModal'));
      modal.show();
    });

  },

  methods: {
    homeModal() {
      EventBus.emit('game-shut-modal-home');
    },

    newGameModal() {
      EventBus.emit('game-shut-modal-new-game');
    },

    detailsModal() {
      EventBus.emit('game-shut-modal-confirmed', this.dartsForCheckout, this.average);
    },
  },
}
</script>