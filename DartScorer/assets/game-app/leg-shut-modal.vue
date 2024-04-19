<template>
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Leg shut</h1>
          <!--
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          -->
        </div>
        <div class="modal-body">
          <p>How many darts were needed?</p>

          <div class="btn-group" role="group" aria-label="Basic radio toggle button group" style="width: 100%">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" v-model="dartsForCheckout" value="1">
            <label class="btn btn-outline-dark" for="btnradio1">1</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" v-model="dartsForCheckout" value="2">
            <label class="btn btn-outline-dark" for="btnradio2">2</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off" v-model="dartsForCheckout" value="3" checked>
            <label class="btn btn-outline-dark" for="btnradio3">3</label>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="resumeModal">Resume</button>
          <button type="button" class="btn btn-success" data-bs-dismiss="modal" @click="confirmModal">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {EventBus} from '../event-bus';

export default {
  name: 'LegShutModalComponent',
  data() {
    return {
      dartsForCheckout: '3' // Default selected value
    };
  },
  created() {
    EventBus.on('show-leg-shut-modal', () => {
      const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
      modal.show();
    });
  },
  methods: {
    confirmModal() {
      //const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
      EventBus.emit('modal-confirmed', this.dartsForCheckout);
    },

    resumeModal() {
      //const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
      EventBus.emit('modal-resumed', this.dartsForCheckout);
    }
  }
}
</script>
