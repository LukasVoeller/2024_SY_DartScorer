<template>
  <!-- Numberpad row -->
  <div class="px-1">
    <div class="row" style="height: 100%">
      <div class="col p-1 pt-0">
        <button id="btn-undo" type="button" class="btn btn-light custom-btn-score-row-tablet" :disabled="disableThrowButton">
          <i class="bi bi-arrow-counterclockwise"></i>
          {{ undoButtonText }}
        </button>
      </div>
      <div class="col p-1 pt-0">
        <input id="scoreInput" style="height: 100px;" class="form-control text-center" :class="{'exceeds-limit': exceedsLimit}" type="text" readonly>
      </div>
      <div class="col p-1 pt-0">
        <button id="btn-left" type="button" class="btn btn-light custom-btn-score-row-tablet">
          <i class="bi bi-chevron-bar-down"></i>
          {{ leftButtonText }}
        </button>
      </div>
    </div>

    <div class="row">
      <div class="col p-1">
        <button id="btn-1" type="button" class="btn btn-dark custom-btn-number-tablet-scores">26</button>
      </div>
      <div class="col p-1">
        <button id="btn-1" type="button" class="btn btn-dark custom-btn-number-tablet">1</button>
      </div>
      <div class="col p-1">
        <button id="btn-2" type="button" class="btn btn-dark custom-btn-number-tablet">2</button>
      </div>
      <div class="col p-1">
        <button id="btn-3" type="button" class="btn btn-dark custom-btn-number-tablet">3</button>
      </div>
      <div class="col p-1">
        <button id="btn-3" type="button" class="btn btn-dark custom-btn-number-tablet-scores">60</button>
      </div>
    </div>

    <div class="row">
      <div class="col p-1">
        <button id="btn-4" type="button" class="btn btn-dark custom-btn-number-tablet-scores">41</button>
      </div>
      <div class="col p-1">
        <button id="btn-4" type="button" class="btn btn-dark custom-btn-number-tablet">4</button>
      </div>
      <div class="col p-1">
        <button id="btn-5" type="button" class="btn btn-dark custom-btn-number-tablet">5</button>
      </div>
      <div class="col p-1">
        <button id="btn-6" type="button" class="btn btn-dark custom-btn-number-tablet">6</button>
      </div>
      <div class="col p-1">
        <button id="btn-6" type="button" class="btn btn-dark custom-btn-number-tablet-scores">80</button>
      </div>
    </div>

    <div class="row">
      <div class="col p-1">
        <button id="btn-7" type="button" class="btn btn-dark custom-btn-number-tablet-scores">45</button>
      </div>
      <div class="col p-1">
        <button id="btn-7" type="button" class="btn btn-dark custom-btn-number-tablet">7</button>
      </div>
      <div class="col p-1">
        <button id="btn-8" type="button" class="btn btn-dark custom-btn-number-tablet">8</button>
      </div>
      <div class="col p-1">
        <button id="btn-9" type="button" class="btn btn-dark custom-btn-number-tablet">9</button>
      </div>
      <div class="col p-1">
        <button id="btn-6" type="button" class="btn btn-dark custom-btn-number-tablet-scores">100</button>
      </div>
    </div>

    <div class="row" style="padding-bottom: 5px;">
      <div class="col p-1">
        <button id="btn-clr" type="button" class="btn btn-danger custom-btn-clear-tablet">Clear</button>
      </div>
      <div class="col p-1">
        <button id="btn-0" type="button" class="btn btn-dark custom-btn-number-tablet">0</button>
      </div>
      <div class="col p-1">
        <button id="btn-ok" type="button" class="btn btn-success custom-btn-ok-tablet">{{ okButtonText }}</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NumberpadTabletComponent',
  emits: ['score-entered', 'score-cleared', 'score-confirmed', 'score-undo', 'score-left'],
  props: {
    //playerName: String,
    player1Score: Number,
    player2Score: Number,
    player1ToThrow: Boolean,
    player2ToThrow: Boolean,
    player1LastScores: Array,
    player2LastScores: Array,
    disableThrowButton: Boolean,
  },

  data() {
    return {
      currentInput: 0,
    };
  },

  mounted() {
    // Get scoreInput
    const scoreInput = document.getElementById("scoreInput");

    // CLR
    document.getElementById("btn-clr").addEventListener('click', () => {
      try {
        window.navigator.vibrate([100]);
      } catch (error) {
        console.warn("Your device doesn't support vibration.");
      }

      this.$emit('score-cleared', scoreInput.value);
      scoreInput.value = "";
      this.currentInput = 0;
    });

    // OK
    document.getElementById("btn-ok").addEventListener('click', () => {
      try {
        window.navigator.vibrate([100]);
      } catch (error) {
        console.warn("Your device doesn't support vibration.");
      }

      if (this.player1ToThrow) {
        if (scoreInput.value > this.player1Score) {
          this.$emit('score-confirmed', "0");
          scoreInput.value = "";
          this.currentInput = 0;
        } else if (scoreInput.value <= this.player1Score) {
          if (this.player1Score - scoreInput.value >= 2 || this.player1Score - scoreInput.value === 0) {
            if (!this.scoreIsImpossible(scoreInput.value)) {
              this.$emit('score-confirmed', scoreInput.value, this.player1Id);
              scoreInput.value = "";
              this.currentInput = 0;
            }
          }
        }
      } else if (this.player2ToThrow) {
        if (scoreInput.value > this.player2Score) {
          this.$emit('score-confirmed', "0");
          scoreInput.value = "";
          this.currentInput = 0;
        } else if (scoreInput.value <= this.player2Score) {
          if (this.player2Score - scoreInput.value >= 2 || this.player2Score - scoreInput.value === 0) {
            if (!this.scoreIsImpossible(scoreInput.value)) {
              this.$emit('score-confirmed', scoreInput.value);
              scoreInput.value = "";
              this.currentInput = 0;
            }
          }
        }
      }
    });

    // UNDO
    document.getElementById("btn-undo").addEventListener('click', () => {
      try {
        window.navigator.vibrate([100]);
      } catch (error) {
        console.warn("Your device doesn't support vibration.");
      }

      this.$emit('score-undo', scoreInput.value);
      scoreInput.value = "";
      this.currentInput = 0;
    });

    // Attach click event listeners to number buttons
    const numberButtons = document.querySelectorAll('.custom-btn-number-tablet');
    numberButtons.forEach(button => {
      button.addEventListener('click', () => {
        this.handleButtonClick(button.textContent); // Using arrow function to retain 'this' context
      });
    });

    // LEFT
    document.getElementById("btn-left").addEventListener('click', () => {
      try {
        window.navigator.vibrate([100]);
      } catch (error) {
        console.warn("Your device doesn't support vibration.");
      }

      this.$emit('score-left', scoreInput.value);
      scoreInput.value = "";
      this.currentInput = 0;
    });
  },

  computed: {
    exceedsLimit() {
      return this.scoreIsImpossible(this.currentInput);
    },

    leftButtonText() {
      // Change the button text based on player1ToThrow and whether player1Score is bogey
      if (this.player1ToThrow && this.scoreIsCheckable(this.player1Score) && this.currentInput < 1 ||
          this.player2ToThrow && this.scoreIsCheckable(this.player2Score) && this.currentInput < 1) {
        return "Check";
      } else {
        return "Left";
      }
    },

    undoButtonText() {
      // Change the button text based on player1ToThrow and whether player1Score is bogey
      if (this.player1LastScores.length === 0 && this.player2LastScores.length === 0) {
        return "Throw";
      } else {
        return "Undo";
      }
    },

    okButtonText() {
      if (this.player1ToThrow) {
        if (this.currentInput - this.player1Score === 0) {
          return "Check";
        }
      } else if (this.player2ToThrow) {
        if (this.currentInput - this.player2Score === 0) {
          return "Check";
        }
      }

      if (this.player1ToThrow) {
        if (this.currentInput > this.player1Score) {
          return "No Score";
        }
      } else if (this.player2ToThrow) {
        if (this.currentInput > this.player2Score) {
          return "No Score";
        }
      }

      if (this.currentInput >= 1) {
        return "OK";
      } else {
        return "No Score";
      }
    },
  },

  methods: {
    handleButtonClick(buttonValue) {
      try {
        window.navigator.vibrate([100]);
      } catch (error) {
        console.warn("Your device doesn't support vibration.");
      }

      const scoreInput = document.getElementById("scoreInput");
      const currentInputValue = scoreInput.value;

      if (currentInputValue.length < 3) {
        scoreInput.value += buttonValue;
        this.$emit('score-entered', scoreInput.value);
      }

      this.currentInput = scoreInput.value;

      //console.log("CurrentInput: ", this.currentInput)
      //console.log("true: ", scoreInput.value);
    },

    scoreIsCheckable(score) {
      const bogeyNumbers = [169, 168, 166, 165, 163, 162, 159];

      if (score <= 170) {
        return !(bogeyNumbers.includes(score));
      } else {
        false;
      }
    },

    scoreIsImpossible(score) {
      const impossibleScores = [179, 178, 176, 175, 173, 172, 169, 166, 163, 162];
      score = parseInt(score, 10);

      if (score > 180) {
        return true;
      } else return impossibleScores.includes(score);
    },
  }
};
</script>