document.addEventListener('DOMContentLoaded', () => {
// --- NAVIGATION & BURGER ---
    const burger = document.getElementById('burger');
    const nav = document.getElementById('nav');
    const header = document.querySelector('.main-header');
    const navLinks = document.querySelectorAll('.main-nav a');

    // Fonction pour fermer le menu
    const closeMenu = () => {
        nav.classList.remove('active');
        burger.classList.remove('toggle');
        document.body.style.overflow = ''; // Réactive le scroll
    };

    // Toggle menu
    burger.addEventListener('click', (e) => {
        e.stopPropagation(); // Empêche la fermeture immédiate via le clic document
        nav.classList.toggle('active');
        burger.classList.toggle('toggle');
        
        // Optionnel : Bloquer le scroll quand le menu est ouvert
        if (nav.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    });

    // Fermer si on clique sur un lien (très important pour les sites "Single Page")
    navLinks.forEach(link => {
        link.addEventListener('click', closeMenu);
    });

    // Fermer si on clique à l'extérieur du menu
    document.addEventListener('click', (event) => {
        const isClickInsideMenu = nav.contains(event.target);
        const isClickOnBurger = burger.contains(event.target);

        if (!isClickInsideMenu && !isClickOnBurger && nav.classList.contains('active')) {
            closeMenu();
        }
    });
    // Effet au scroll sur le header
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // --- CALCULATEUR AVEC ANIMATION ---
    const inputs = document.querySelectorAll('#price, #weight');
    const EUR_TO_XAF = 665;

    function animateValue(element, start, end, duration, isXAF = false) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = progress * (end - start) + start;
            
            if (isXAF) {
                element.textContent = Math.round(value).toLocaleString() + " XAF";
            } else {
                element.textContent = value.toFixed(2) + " €";
            }
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    function calculate() {
        const price = parseFloat(document.getElementById("price").value) || 0;
        const weight = parseFloat(document.getElementById("weight").value) || 0;

        const commission = price * 0.15;
        const shipping = weight * 10.5;
        const totalEUR = price + commission + shipping;
        const totalXAF = totalEUR * EUR_TO_XAF;

        // Mise à jour avec animation fluide
        animateValue(document.getElementById("buyPrice"), 0, price, 300);
        animateValue(document.getElementById("commission"), 0, commission, 300);
        animateValue(document.getElementById("shipping"), 0, shipping, 300);
        animateValue(document.getElementById("totalEUR"), 0, totalEUR, 300);
        animateValue(document.getElementById("totalFCFA"), 0, totalXAF, 500, true);
        animateValue(document.getElementById("accompte"), 0, totalXAF * 0.8, 500, true);
        animateValue(document.getElementById("reste"), 0, totalXAF * 0.2, 500, true);
    }

    inputs.forEach(input => input.addEventListener('input', calculate));

    // --- SLIDER PARTENAIRES (Infini & Fluide) ---
    const track = document.getElementById("partnersTrack");
    if (track) {
        // On clone le contenu pour le défilement infini
        track.innerHTML += track.innerHTML;
    }
});

const overlay = document.getElementById('newsletterOverlay');
const closeBtn = document.getElementById('closeModal');
const form = document.getElementById('newsletterForm');

// Fermer au clic sur la croix
closeBtn.addEventListener('click', () => {
    overlay.style.opacity = '0';
    setTimeout(() => { overlay.style.visibility = 'hidden'; }, 500);
});

// Fermer après avoir validé le formulaire
form.addEventListener('submit', (e) => {
    e.preventDefault();
    alert("Merci ! Bienvenue chez Holy Shop Congo.");
    overlay.style.opacity = '0';
    setTimeout(() => { overlay.style.visibility = 'hidden'; }, 500);
});