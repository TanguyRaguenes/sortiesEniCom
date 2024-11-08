// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

const burgerBtn = document.getElementById("burgerBtn");
const xmarkBtn = document.getElementById("xmarkBtn");
const navLgScreen = document.getElementById("navLgScreen");
const navSmScreen = document.getElementById("navSmScreen");

burgerBtn.addEventListener("click",()=>{

    console.log("click");
    toggleNavSmScreen()
    
})


xmarkBtn.addEventListener("click",()=>{

    console.log("click");
    toggleNavSmScreen()
    
})


function toggleNavSmScreen(){

    if(navSmScreen.classList.contains("opacity-0")){
        navSmScreen.classList.remove("opacity-0");
        navSmScreen.classList.remove("pointer-events-none");
        navSmScreen.classList.add("opacity-100");
        navSmScreen.classList.add("pointer-events-auto");
        burgerBtn.classList.remove("block")
        burgerBtn.classList.add("hidden")
        xmarkBtn.classList.remove("hidden")
        xmarkBtn.classList.add("block")
    }else{
        navSmScreen.classList.remove("opacity-100");
        navSmScreen.classList.remove("pointer-events-auto");
        navSmScreen.classList.add("opacity-0");
        navSmScreen.classList.add("pointer-events-none");
        burgerBtn.classList.add("block")
        burgerBtn.classList.remove("hidden")
        xmarkBtn.classList.add("hidden")
        xmarkBtn.classList.remove("block")
    }
}


