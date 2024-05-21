<template>
  <!-- Modal -->
  <div class="modal fade" id="gameShutModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="gameShutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <div class="modal-title-wrapper">
            <h1 style="color: black" class="modal-title fs-5">Game shot and the match!</h1>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" style="width: 150px;" class="btn btn-secondary" data-bs-dismiss="modal" @click="resumeModal">New Game</button>
          <button type="button" style="width: 150px;" class="btn btn-success" data-bs-dismiss="modal" @click="confirmModal">Details</button>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import {EventBus} from '../event-bus';

export default {
  name: 'GameShutModalComponent',

  created() {
    EventBus.on('show-game-shut-modal', (payload) => {
      // Reset dartsForCheckout to default
      const modal = new bootstrap.Modal(document.getElementById('gameShutModal'));
      modal.show();
    });

  },

  methods: {
    confirmModal() {
      //const modal = new bootstrap.Modal(document.getElementById('gameShutModal'));
      EventBus.emit('game-shut-modal-confirmed', this.dartsForCheckout, this.average);
    },

    resumeModal() {
      //const modal = new bootstrap.Modal(document.getElementById('gameShutModal'));
      EventBus.emit('game-shut-modal-resumed');
    }
  },
}
</script>
