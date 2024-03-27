<template>
  <div class="row px-1">
    <div class="col p-1">
      <button type="button" class="btn btn-light custom-btn-score-row">Undo</button>
    </div>
    <div class="col p-1">
      <input id="scoreInput" class="form-control text-center" style="height: 55px; font-size: 25px" type="text" aria-label=".form-control-lg example" readonly>
    </div>
    <div class="col p-1">
      <button type="button" class="btn btn-light custom-btn-score-row">Rest</button>
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
        <button id="btn-clr" type="button" class="btn btn-danger custom-btn-function">CLR</button>
      </div>
      <div class="col p-1">
        <button id="btn-0" type="button" class="btn btn-dark custom-btn-number">0</button>
      </div>
      <div class="col p-1">
        <button id="btn-ok" type="button" class="btn btn-success custom-btn-function">OK</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NumberpadComponent',
  props: {
    playerName: String,
  },
  mounted() {
    // Get scoreInput
    const scoreInput = document.getElementById("scoreInput");

    // CLR
    document.getElementById("btn-clr").addEventListener('click', function () {
      scoreInput.value = "";
    });

    // OK
    document.getElementById("btn-ok").addEventListener('click', () => {
      if (scoreInput.value <= 180) {
        this.$emit('score-entered', scoreInput.value);
        scoreInput.value = "";
        //alert("Entered value: " + scoreInput.value);
      } else {
        const scoreInputHandle = document.getElementById("scoreInput");
        scoreInputHandle.classList.add('exceeds-limit');
        setTimeout(() => {
          scoreInputHandle.classList.remove('exceeds-limit');
        }, 1000);
      }
    });

    // Attach click event listeners to number buttons
    const numberButtons = document.querySelectorAll('.custom-btn-number');
    numberButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        handleButtonClick(button.textContent);
      });
    });

    // Handle the entered digits
    function handleButtonClick(buttonValue) {
      const currentInputValue = scoreInput.value;
      scoreInput.value = currentInputValue + buttonValue;
    }
  }
};
</script>