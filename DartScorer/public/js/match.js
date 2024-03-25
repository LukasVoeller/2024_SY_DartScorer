var playerToThrow;

document.addEventListener('DOMContentLoaded', function () {
    /*
    var scoreInput = document.getElementById("scoreInput");

    function handleButtonClick(buttonValue) {
        var currentInputValue = scoreInput.value;
        scoreInput.value = currentInputValue + buttonValue;
    }

    document.getElementById("btn-clr").addEventListener('click', function () {
        scoreInput.value = "";
    });

    document.getElementById("btn-ok").addEventListener('click', function () {
        if (scoreInput.value <= 180) {
            alert("Entered value: " + scoreInput.value);
        } else {
            //alert("Value cannot exceed 180");

            var scoreInputHandle = document.getElementById("scoreInput");
            scoreInputHandle.classList.add('exceeds-limit');
            setTimeout(() => {
                scoreInputHandle.classList.remove('exceeds-limit');
            }, 1000);
        }
    });

    // Attach click event listeners to number buttons
    var numberButtons = document.querySelectorAll('.custom-btn-number');
    numberButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            handleButtonClick(button.textContent);
        });
    });
    */

    // Attach click event listeners to player cards
    var player1= document.getElementById("player-1");
    var player2= document.getElementById("player-2");

    player1.addEventListener('click', function () {
        handlePlayer1Click(player1, player2);
    });

    player2.addEventListener('click', function () {
        handlePlayer2Click(player2, player1);
    });

    function handlePlayer1Click(player1, player2) {
        if (player1.classList.contains('border-light')){
            player1.classList.remove('border-light');
            player1.classList.add('border-warning');
            player2.classList.remove('border-warning');
            player2.classList.add('border-light');
        }
    }

    function handlePlayer2Click(player2, player1) {
        if (player2.classList.contains('border-light')) {
            player2.classList.remove('border-light');
            player2.classList.add('border-warning');
            player1.classList.remove('border-warning');
            player1.classList.add('border-light');
        }
    }
});