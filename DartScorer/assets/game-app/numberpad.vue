<template>

  <div class="row vh-100">

    <!-- Numberpad row Undo, Input, Rest -->
    <div class="row p-0">
      <div class="col p-0">
        <button id="btn-undo" type="button" class="btn btn-light custom-btn-score-row">Undo</button>
      </div>
      <div class="col p-0">
        <input id="scoreInput" class="form-control text-center" style="height: 100%; font-size: 30px" type="text" aria-label=".form-control-lg example" readonly>
      </div>
      <div class="col p-0">
        <button id="btn-rest" type="button" class="btn btn-light custom-btn-score-row">Rest</button>
      </div>
    </div>

    <!-- Numberpad row 1, 2, 3 -->
    <div class="row p-0">
      <div class="col p-0">
        <button id="btn-26" type="button" class="btn btn-dark custom-btn-number">26</button>
      </div>
      <div class="col p-0">
        <button id="btn-1" type="button" class="btn btn-dark custom-btn-number">1</button>
      </div>
      <div class="col p-0">
        <button id="btn-2" type="button" class="btn btn-dark custom-btn-number">2</button>
      </div>
      <div class="col p-0">
        <button id="btn-3" type="button" class="btn btn-dark custom-btn-number">3</button>
      </div>
      <div class="col p-0">
        <button id="btn-60" type="button" class="btn btn-dark custom-btn-number">60</button>
      </div>
    </div>

    <!-- Numberpad row 4, 5, 6 -->
    <div class="row p-0">
      <div class="col p-0">
        <button id="btn-41" type="button" class="btn btn-dark custom-btn-number">41</button>
      </div>
      <div class="col p-0">
        <button id="btn-4" type="button" class="btn btn-dark custom-btn-number">4</button>
      </div>
      <div class="col p-0">
        <button id="btn-5" type="button" class="btn btn-dark custom-btn-number">5</button>
      </div>
      <div class="col p-0">
        <button id="btn-6" type="button" class="btn btn-dark custom-btn-number">6</button>
      </div>
      <div class="col p-0">
        <button id="btn-85" type="button" class="btn btn-dark custom-btn-number">85</button>
      </div>
    </div>

    <!-- Numberpad row 7, 8, 9 -->
    <div class="row p-0">
      <div class="col p-0">
        <button id="btn-45" type="button" class="btn btn-dark custom-btn-number">45</button>
      </div>
      <div class="col p-0">
        <button id="btn-7" type="button" class="btn btn-dark custom-btn-number">7</button>
      </div>
      <div class="col p-0">
        <button id="btn-8" type="button" class="btn btn-dark custom-btn-number">8</button>
      </div>
      <div class="col p-0">
        <button id="btn-9" type="button" class="btn btn-dark custom-btn-number">9</button>
      </div>
      <div class="col p-0">
        <button id="btn-100" type="button" class="btn btn-dark custom-btn-number">100</button>
      </div>
    </div>

    <!-- Numberpad row CLR, 0, OK -->
    <div class="row p-0">
      <div class="col p-0">
        <button id="btn-clr" type="button" class="btn btn-danger custom-btn-function">CLR</button>
      </div>
      <div class="col p-0">
        <button id="btn-0" type="button" class="btn btn-dark custom-btn-number">0</button>
      </div>
      <div class="col p-0">
        <button id="btn-ok" type="button" class="btn btn-success custom-btn-function">OK</button>
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
  },
  mounted() {
    // Get scoreInput
    const scoreInput = document.getElementById("scoreInput");

    // CLR
    document.getElementById("btn-clr").addEventListener('click', () => {
      window.navigator.vibrate([100]);

      this.$emit('score-cleared', scoreInput.value);
      scoreInput.value = "";
    });

    // OK
    document.getElementById("btn-ok").addEventListener('click', () => {
      window.navigator.vibrate([100]);

      if (scoreInput.value <= 180) {
        this.$emit('score-confirmed', scoreInput.value);
        scoreInput.value = "";
      } else {
        const scoreInputHandle = document.getElementById("scoreInput");
        scoreInputHandle.classList.add('exceeds-limit');
        setTimeout(() => {
          scoreInputHandle.classList.remove('exceeds-limit');
        }, 1000);
      }
    });

    // UNDO
    document.getElementById("btn-undo").addEventListener('click', () => {
      window.navigator.vibrate([100]);

      this.$emit('score-undo', scoreInput.value);
      scoreInput.value = "";
    });

    // Attach click event listeners to number buttons
    const numberButtons = document.querySelectorAll('.custom-btn-number');
    numberButtons.forEach(button => {
      button.addEventListener('click', () => {
        this.handleButtonClick(button.textContent); // Using arrow function to retain 'this' context
      });
    });
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
    }
  }
};
</script>