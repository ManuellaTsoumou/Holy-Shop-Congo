 const burger = document.getElementById('burger');
    const nav = document.getElementById('nav');

    burger.addEventListener('click', () => {
        nav.classList.toggle('show');
    });

// Sélection des éléments du DOM
const priceInput = document.getElementById("price");
const weightInput = document.getElementById("weight");

const buyPriceEl = document.getElementById("buyPrice");
const commissionEl = document.getElementById("commission");
const shippingEl = document.getElementById("shipping");
const totalEUREl = document.getElementById("totalEUR");
const totalXAFEl = document.getElementById("totalFCFA");
const accompteEl = document.getElementById("accompte");
const resteEl = document.getElementById("reste");

// Constantes
const COMMISSION_RATE = 0.15;       // 15% de commission
const SHIPPING_RATE_EUR = 10.5;     // 10,5 € / kg
const EUR_TO_XAF = 665;             // 1 € = 665 XAF

// Fonction de calcul
function calculate() {
    const price = parseFloat(priceInput.value) || 0;
    const weight = parseFloat(weightInput.value) || 0;

    // Commission
    const commission = price * COMMISSION_RATE;

    // Frais de transport
    const shipping = weight * SHIPPING_RATE_EUR;

    // Total en euros
    const totalEUR = price + commission + shipping;

    // Conversion en XAF
    const totalXAF = totalEUR * EUR_TO_XAF;

    // Calcul de l'accompte et du reste
    const accompte = totalXAF * 0.8;
    const reste = totalXAF * 0.2;

    // Affichage
    buyPriceEl.textContent = price.toFixed(2) + " €";
    commissionEl.textContent = commission.toFixed(2) + " €";
    shippingEl.textContent = shipping.toFixed(2) + " €";
    totalEUREl.textContent = totalEUR.toFixed(2) + " €";
    totalXAFEl.textContent = Math.round(totalXAF).toLocaleString() + " XAF";
}

// Écouteurs d'événements
priceInput.addEventListener("input", calculate);
weightInput.addEventListener("input", calculate);

// Calcul initial
calculate();

// Sélection des éléments
const track = document.getElementById("partnersTrack");
const inner = document.getElementById("partnersInner");

// Dupliquer les logos pour le scroll infini
inner.innerHTML += inner.innerHTML;

let scrollSpeed = 1.5; // vitesse en px par frame
let position = 0;

// Fonction d'animation
function scrollLogos() {
    position -= scrollSpeed;
    if (Math.abs(position) >= inner.scrollWidth / 2) {
        position = 0; // réinitialise pour effet infini
    }
    inner.style.transform = `translateX(${position}px)`;
    requestAnimationFrame(scrollLogos);
}

// Démarrer l'animation
scrollLogos();

 const faqItems = document.querySelectorAll(".faq-item");
  faqItems.forEach(item => {
    item.addEventListener("click", () => {
      item.classList.toggle("active");
    });
  });

  