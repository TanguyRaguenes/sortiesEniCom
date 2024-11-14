let map = null;
let latitudeInit = 47.22679624955804;
let longitudeInit = -1.6206476928187;

const data = document.querySelector(".data");
const placesData = JSON.parse(data.getAttribute("data-places"));
console.log(placesData);

// placesData.forEach((place) => {
//   console.log(place);
//   if (place.name == "Test") {
//     console.log(place.name);
//     console.log(place.latitude);
//     console.log(place.longitude);
//   }
// });

function generateMap(latitude, longitude) {
  if (map == null) {
    map = L.map("map").setView([latitude, longitude], 15);

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 19,
      attribution:
        '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);
  }
}

generateMap(latitudeInit, longitudeInit);

let markers = [];
let newMarker = L.marker([latitudeInit, longitudeInit]).addTo(map);
markers = [newMarker];

const tripFormPlace = document.getElementById("trip_form_place");
const latitudeContainer = document.getElementById("latitude");
const longitudeContainer = document.getElementById("longitude");
const addressContainer = document.getElementById("address");

tripFormPlace.addEventListener("change", () => {
  console.log("change");

  markers.forEach((marker) => {
    map.removeLayer(marker);
  });

  const indice = tripFormPlace.value - 1;
  console.log(placesData[indice]);
  console.log(placesData[indice].name);
  console.log(placesData[indice].latitude);
  console.log(placesData[indice].longitude);
  latitudeContainer.innerText = placesData[indice].latitude;
  longitudeContainer.innerText = placesData[indice].longitude;
  addressContainer.innerText = placesData[indice].address;

  newMarker = L.marker([
    placesData[indice].latitude,
    placesData[indice].longitude,
  ]).addTo(map);

  markers = [newMarker];

  map.setView([placesData[indice].latitude, placesData[indice].longitude], 15);
});
