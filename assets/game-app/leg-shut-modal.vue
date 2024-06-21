<template>
  <!-- Modal -->
  <div class="modal fade" id="legShutModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="legShutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <div class="modal-title-wrapper">
            <h1 style="color: black" class="modal-title fs-5">{{ checkout }} Checkout!</h1>
            <p style="color: black; margin: 0px">Average: {{ average }}</p>
            <p style="color: black; margin: 0px">Darts: {{ darts }}</p>
          </div>
        </div>

        <div class="modal-body">
          <p style="color: black">How many darts were needed?</p>

          <div class="btn-group" role="group" aria-label="Basic radio toggle button group" style="width: 100%">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" v-model="dartsForCheckout" :disabled="oneDartCheckoutDisabled" value="1">
            <label class="btn btn-outline-dark" for="btnradio1">1</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" v-model="dartsForCheckout" :disabled="threeDartsNeeded" value="2">
            <label class="btn btn-outline-dark" for="btnradio2">2</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off" v-model="dartsForCheckout" value="3" checked>
            <label class="btn btn-outline-dark" for="btnradio3">3</label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" style="width: 100px;" class="btn btn-secondary" data-bs-dismiss="modal" @click="resumeModal">Resume</button>
          <button type="button" style="width: 100px;" class="btn btn-success" data-bs-dismiss="modal" @click="confirmModal">Save</button>
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
      dartsForCheckout: 3,
      winnerPlayerId: 0,
      looserPlayerId: 0,
      scores: [],
      checkout: 0,
      average: 0,
      darts: 0
    };
  },

  created() {
    EventBus.on('show-leg-shut-modal', (thrownScores, winnerPlayerId, looserPlayerId) => {
      this.dartsForCheckout = 3;
      this.winnerPlayerId = winnerPlayerId;
      this.looserPlayerId = looserPlayerId;
      const modal = new bootstrap.Modal(document.getElementById('legShutModal'));
      this.scores = thrownScores;
      this.checkout = this.scores[0];
      this.calculateAverage();
      modal.show();
    });

  },

  computed: {
    threeDartsNeeded() {
      return this.checkout > 110
    },

    oneDartCheckoutDisabled() {
      if (this.checkout % 2 === 0) {
        return !(this.checkout === 50 || this.checkout <= 40);
      } else {
        return true;
      }
    },
  },

  methods: {
    calculateAverage() {
      if (this.scores.length > 0) {
        const sum = this.scores.reduce((acc, score) => acc + score, 0); // Sum all elements
        const darts = this.scores.length * 3
        this.darts = darts - 3 + parseInt(this.dartsForCheckout);
        const throws = this.scores.length;
        //console.log("---> Sum: ", sum, " Darts: ", darts, " darts: ", this.darts, " throws: ", throws)
        this.average = (sum / this.darts * 3).toFixed(1); // Calculate the average
      } else {
        this.average = 0; // Handle the case when there's no data
      }
    },

    confirmModal() {
      //const modal = new bootstrap.Modal(document.getElementById('legShutModal'));
      EventBus.emit('leg-shut-modal-confirmed', this.dartsForCheckout, this.average, this.winnerPlayerId, this.looserPlayerId);
    },

    resumeModal() {
      //const modal = new bootstrap.Modal(document.getElementById('legShutModal'));
      EventBus.emit('leg-shut-modal-resumed');
    }
  },

  watch: {
    dartsForCheckout(newValue, oldValue) {
      this.calculateAverage(); // Recalculate when darts needed changes
    },
  },
}
</script>
