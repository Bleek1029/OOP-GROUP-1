const player1Container = document.getElementById('player1-container');
const player2Container = document.getElementById('player2-container');
const player1Grid = document.getElementById('player1-grid');
const player2Grid = document.getElementById('player2-grid');
const player1Health = document.getElementById('player1-health');
const player2Health = document.getElementById('player2-health');
const playAgainButton = document.getElementById('play-again');
const player1Combo = document.getElementById('player1-combo');
const player2Combo = document.getElementById('player2-combo');
const character1 = document.getElementById('character1');
const character2 = document.getElementById('character2');

const blueCardBacks = [
    'BACK CARDS/BLUE (1).png',
     'BACK CARDS/BLUE (2).png',
     'BACK CARDS/BLUE (3).png',
     'BACK CARDS/BLUE (4).png',
     'BACK CARDS/BLUE (5).png',
     'BACK CARDS/BLUE (6).png',
     'BACK CARDS/BLUE (7).png',
     'BACK CARDS/BLUE (8).png',
     'BACK CARDS/BLUE (9).png',
     'BACK CARDS/BLUE (10).png',
     'BACK CARDS/BLUE (11).png',
     'BACK CARDS/BLUE (12).png',
     'BACK CARDS/BLUE (13).png',
     'BACK CARDS/BLUE (14).png',
     'BACK CARDS/BLUE (15).png',
     'BACK CARDS/BLUE (16).png',
     'BACK CARDS/BLUE (17).png',
     'BACK CARDS/BLUE (18).png',
     'BACK CARDS/BLUE (19).png',
     'BACK CARDS/BLUE (20).png',
     'BACK CARDS/BLUE (21).png',
     'BACK CARDS/BLUE (22).png',
     'BACK CARDS/BLUE (23).png',
     'BACK CARDS/BLUE (24).png',

];

const yellowCardBacks = [
    'back cards/YELLOW (1).png',
    'back cards/YELLOW (2).png',
    'back cards/YELLOW (3).png',
    'back cards/YELLOW (4).png',
    'back cards/YELLOW (5).png',
    'back cards/YELLOW (6).png',
    'back cards/YELLOW (7).png',
    'back cards/YELLOW (8).png',
    'back cards/YELLOW (9).png',
    'back cards/YELLOW (10).png',
    'back cards/YELLOW (11).png',
    'back cards/YELLOW (12).png',
    'back cards/YELLOW (13).png',
    'back cards/YELLOW (14).png',
    'back cards/YELLOW (15).png',
    'back cards/YELLOW (16).png',
    'back cards/YELLOW (17).png',
    'back cards/YELLOW (18).png',
    'back cards/YELLOW (19).png',
    'back cards/YELLOW (20).png',
    'back cards/YELLOW (21).png',
    'back cards/YELLOW (22).png',
    'back cards/YELLOW (23).png',
    'back cards/YELLOW (24).png',
];

const cardValues = [
    'NEW CARDS/icon1.png',
    'NEW CARDS/icon2.png',
    'NEW CARDS/icon3.png',
    'NEW CARDS/icon4.png',
    'NEW CARDS/icon5.png',
    'NEW CARDS/icon6.png',
    'NEW CARDS/icon7.png',
    'NEW CARDS/icon8.png',
];
let cards = [...cardValues, ...cardValues];

let flippedCards = [];
let matchedCards = [];
let player1Turn = true;
let player1HealthValue = 100;
let player2HealthValue = 100;
let combo = 0;

function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

function createGrid(grid, player) {
    grid.innerHTML = '';
    shuffle(cards);
    for (let i = 0; i < 16; i++) {
        const card = document.createElement('div');
        card.classList.add('card');
        card.dataset.value = cards[i];
        card.dataset.player = player;

        const front = document.createElement('div');
        front.classList.add('front');
        front.style.backgroundImage = `url('${cards[i]}')`;
        front.style.backgroundSize = 'cover';
        front.style.backgroundPosition = 'center';


        const back = document.createElement('div');
        back.classList.add('back');
        if (player === 1) {
            const randomIndex = Math.floor(Math.random() * blueCardBacks.length);
            back.style.backgroundImage = `url('${blueCardBacks[randomIndex]}')`;
        } else {
            const randomIndex = Math.floor(Math.random() * yellowCardBacks.length);
            back.style.backgroundImage = `url('${yellowCardBacks[randomIndex]}')`;
        }
        back.style.backgroundSize = 'cover';
        back.style.backgroundPosition = 'center';

        card.appendChild(front);
        card.appendChild(back);

        card.addEventListener('click', flipCard);
        grid.appendChild(card);
    }
}

function flipCard() {
    if (!canFlip(this)) return;

    if (flippedCards.length < 2 && !this.classList.contains('flipped')) {
        this.classList.add('flipped');
        flippedCards.push(this);

        if (flippedCards.length === 2) {
            setTimeout(checkForMatch, 1000);
        }
    }
}

function canFlip(card) {
    if (player1Turn && card.dataset.player !== '1') return false;
    if (!player1Turn && card.dataset.player !== '2') return false;
    return true;
}

function checkForMatch() {
    const [card1, card2] = flippedCards;
    if (card1.dataset.value === card2.dataset.value) {
        matchedCards.push(card1, card2);
        card1.classList.add('matched');
        card2.classList.add('matched');
        combo++;
        updateComboCounter();
        if (player1Turn) {
            character1.classList.add('attack');
            character1.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p1-attack.gif';
            setTimeout(() => {
                character1.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p1-idle.gif';
                character1.classList.remove('attack');
            }, 1500);
        } else {
            character2.classList.add('attack');
            character2.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p2-attack.gif';
            setTimeout(() => {
                character2.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p2-idle.gif';
                character2.classList.remove('attack');
            }, 1500);
        }
        dealDamage();
    } else {
        card1.classList.remove('flipped');
        card2.classList.remove('flipped');
        combo = 0;
        updateComboCounter();
        switchTurn();
    }
    flippedCards = [];
}

function dealDamage() {
    const damage = 10 * combo;
    if (player1Turn) {
        player2HealthValue -= damage;
        if (player2HealthValue < 0) player2HealthValue = 0;
        player2Health.style.width = `${player2HealthValue}%`;
        character2.classList.add('take-hit');
        character2.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p2-take-hit.gif';
        if (player2HealthValue > 0) {
            setTimeout(() => {
                character2.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p2-idle.gif';
                character2.classList.remove('take-hit');
            }, 1000);
        }
    } else {
        player1HealthValue -= damage;
        if (player1HealthValue < 0) player1HealthValue = 0;
        player1Health.style.width = `${player1HealthValue}%`;
        character1.classList.add('take-hit');
        character1.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p1-take-hit.gif';
        if (player1HealthValue > 0) {
            setTimeout(() => {
                character1.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p1-idle.gif';
                character1.classList.remove('take-hit');
            }, 1000);
        }
    }

    if (player1HealthValue <= 0 || player2HealthValue <= 0) {
        setTimeout(endGame, 100);
    }
}

function switchTurn() {
    player1Turn = !player1Turn;
    if (player1Turn) {
        player1Container.classList.remove('inactive');
        player2Container.classList.add('inactive');
    } else {
        player1Container.classList.add('inactive');
        player2Container.classList.remove('inactive');
    }
    combo = 0;
    updateComboCounter();
}

function updateComboCounter() {
    if (combo >= 1) {
        if (player1Turn) {
            player1Combo.style.display = 'block';
            player1Combo.textContent = `COMBO x${combo}`;
        } else {
            player2Combo.style.display = 'block';
            player2Combo.textContent = `COMBO x${combo}`;
        }
    } else {
        player1Combo.style.display = 'none';
        player2Combo.style.display = 'none';
    }
}

function endGame() {
    if (player1HealthValue <= 0) {
        character1.classList.add('death');
        character1.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p1-death.gif?_=${new Date().getTime()}';
    }
    if (player2HealthValue <= 0) {
        character2.classList.add('death');
        character2.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p2-death.gif?_=${new Date().getTime()}';
    }
    setTimeout(() => {
        alert(`Player ${player1HealthValue <= 0 ? 2 : 1} wins!`);
        playAgainButton.style.display = 'block';
    }, 1000);
}

function resetGame() {
    flippedCards = [];
    matchedCards = [];
    player1Turn = true;
    player1HealthValue = 100;
    player2HealthValue = 100;
    combo = 0;
    updateComboCounter();

    player1Health.style.width = '100%';
    player2Health.style.width = '100%';

    character1.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p1-idle.gif';
    character2.src = 'CHARACTERS/Wizard Pack/Wizard Pack/p2-idle.gif';
    character1.classList.remove('attack', 'take-hit', 'death');
    character2.classList.remove('attack', 'take-hit', 'death');

    createGrid(player1Grid, 1);
    createGrid(player2Grid, 2);

    player1Container.classList.remove('inactive');
    player2Container.classList.add('inactive');

    playAgainButton.style.display = 'none';
}

playAgainButton.addEventListener('click', resetGame);

createGrid(player1Grid, 1);
createGrid(player2Grid, 2);
player2Container.classList.add('inactive');