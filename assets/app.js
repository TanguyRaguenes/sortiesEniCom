// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/app.css";

// console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

const burgerBtn = document.getElementById("burgerBtn");
const xmarkBtn = document.getElementById("xmarkBtn");
const navLgScreen = document.getElementById("navLgScreen");
const navSmScreen = document.getElementById("navSmScreen");

burgerBtn.addEventListener("click", () => {
  console.log("click");
  toggleNavSmScreen();
});

xmarkBtn.addEventListener("click", () => {
  console.log("click");
  toggleNavSmScreen();
});

function toggleNavSmScreen() {
  const isHidden = navSmScreen.classList.contains("opacity-0");

  // Bascule des classes pour l'affichage
  navSmScreen.classList.toggle("opacity-0", !isHidden);
  navSmScreen.classList.toggle("pointer-events-none", !isHidden);
  navSmScreen.classList.toggle("opacity-100", isHidden);
  navSmScreen.classList.toggle("pointer-events-auto", isHidden);

  // Bascule des icônes de burger et xmark
  burgerBtn.classList.toggle("hidden", isHidden);
  xmarkBtn.classList.toggle("hidden", !isHidden);
}

setTimeout(() => {
  const flashMessagesContainer = document.getElementById(
    "flashMessagesContainer"
  );

  if (flashMessagesContainer) {
    flashMessagesContainer.style.display = "none";
  }
}, 3000);

// ______________________________________________________________________________________________________________________________________
