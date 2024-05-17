<template>
  <div class="row px-1">
    <div class="col p-1">
      <button id="btn-undo" type="button" class="btn btn-light custom-btn-score-row">
        <i class="bi bi-arrow-counterclockwise"></i>
        Undo
      </button>
    </div>
    <div class="col p-1">
      <input id="scoreInput" class="form-control text-center" :class="{'exceeds-limit': exceedsLimit}" type="text" readonly>
    </div>
    <div class="col p-1">
      <button id="btn-left" type="button" class="btn btn-light custom-btn-score-row">
        <i class="bi bi-chevron-bar-down"></i>
        {{ leftButtonText }}
      </button>
    </div>
  </div>

  <!-- Numberpad row -->
  <div class="px-1">
    <div class="row">
      <div class="col p-1">
        <button id="btn-1" type="button" class="btn btn-dark custom-btn-number">1</button>
      </div>
      <div class="col p-1">
        <button id="btn-2" type="button" class="btn btn-dark custom-btn-number">2</button>
      </div>
      <div class="col p-1">
        <button id="btn-3" type="button" class="btn btn-dark custom-btn-number">3</button>
      </div>
    </div>
    <div class="row">
      <div class="col p-1">
        <button id="btn-4" type="button" class="btn btn-dark custom-btn-number">4</button>
      </div>
      <div class="col p-1">
        <button id="btn-5" type="button" class="btn btn-dark custom-btn-number">5</button>
      </div>
      <div class="col p-1">
        <button id="btn-6" type="button" class="btn btn-dark custom-btn-number">6</button>
      </div>
    </div>
    <div class="row">
      <div class="col p-1">
        <button id="btn-7" type="button" class="btn btn-dark custom-btn-number">7</button>
      </div>
      <div class="col p-1">
        <button id="btn-8" type="button" class="btn btn-dark custom-btn-number">8</button>
      </div>
      <div class="col p-1">
        <button id="btn-9" type="button" class="btn btn-dark custom-btn-number">9</button>
      </div>
    </div>
    <div class="row" style="padding-bottom: 5px;">
      <div class="col p-1">
        <button id="btn-clr" type="button" class="btn btn-danger custom-btn-clear">CLR</button>
      </div>
      <div class="col p-1">
        <button id="btn-0" type="button" class="btn btn-dark custom-btn-number">0</button>
      </div>
      <div class="col p-1">
        <button id="btn-ok" type="button" class="btn btn-success custom-btn-ok">{{ okButtonText }}</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NumberpadComponent',
  emits: ['score-entered', 'score-cleared', 'score-confirmed', 'score-undo'], // Declare the custom events
  props: {
    playerName: String,
    player1Score: Number,
    player2Score: Number,
    player1ToThrow: Boolean,
    player2ToThrow: Boolean
  },

  data() {
    return {
      currentInput: 0
    };
  },

  mounted() {
    // Get scoreInput
    const scoreInput = document.getElementById("scoreInput");

    // CLR
    document.getElementById("btn-clr").addEventListener('click', () => {
      window.navigator.vibrate([100]);

      this.$emit('score-cleared', scoreInput.value);
      scoreInput.value = "";
      this.currentInput = 0;
    });

    // OK
    document.getElementById("btn-ok").addEventListener('click', () => {
      window.navigator.vibrate([100]);
      console.log("XXX ", this.player1Score)

      if (scoreInput.value <= this.player1Score && this.player1ToThrow) {
        if (!this.scoreIsImpossible(scoreInput.value)) {
          this.$emit('score-confirmed', scoreInput.value);
          scoreInput.value = "";
          this.currentInput = 0;
        }
      } else if (scoreInput.value <= this.player2Score && this.player2ToThrow) {
        if (!this.scoreIsImpossible(scoreInput.value)) {
          this.$emit('score-confirmed', scoreInput.value);
          scoreInput.value = "";
          this.currentInput = 0;
        }
      }
    });

    // UNDO
    document.getElementById("btn-undo").addEventListener('click', () => {
      window.navigator.vibrate([100]);

      this.$emit('score-undo', scoreInput.value);
      scoreInput.value = "";
      this.currentInput = 0;
    });

    // Attach click event listeners to number buttons
    const numberButtons = document.querySelectorAll('.custom-btn-number');
    numberButtons.forEach(button => {
      button.addEventListener('click', () => {
        this.handleButtonClick(button.textContent); // Using arrow function to retain 'this' context
      });
    });

    // LEFT
    document.getElementById("btn-left").addEventListener('click', () => {
      window.navigator.vibrate([100]);
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
      if (this.player1ToThrow && this.scoreIsCeckable(this.player1Score) && this.currentInput < 1 ||
          this.player2ToThrow && this.scoreIsCeckable(this.player2Score) && this.currentInput < 1) {
        return "Check";
      }
      return "Left";
    },

    okButtonText() {
      if (this.currentInput >= 1) {
        return "OK";
      } else {
        return "No Score";
      }
    },
  },

  methods: {
    handleButtonClick(buttonValue) {
      window.navigator.vibrate([100]);

      const scoreInput = document.getElementById("scoreInput");
      const currentInputValue = scoreInput.value;

      if (currentInputValue.length < 3) {
        scoreInput.value += buttonValue;
        this.$emit('score-entered', scoreInput.value);
      }

      this.currentInput = scoreInput.value;

      console.log("CurrentInput: ", this.currentInput)
      console.log("true: ", scoreInput.value);
    },

    scoreIsCeckable(score) {
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