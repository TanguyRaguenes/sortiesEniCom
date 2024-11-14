
let map = null;
let latitudeInit = 47.22679624955804;
let longitudeInit = -1.6206476928187;

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

const latitudeField = document.getElementById("place_form_latitude");
const longitudeField = document.getElementById("place_form_longitude");

let markers = [];
let newMarker = L.marker([latitudeInit, longitudeInit]).addTo(map);
markers = [newMarker];

map.addEventListener("click", (ev) => {
  const latlng = ev.latlng;
  latitudeField.value = latlng.lat;
  longitudeField.value = latlng.lng;

  markers.forEach((marker) => {
    map.removeLayer(marker);
  });

  newMarker = L.marker([latlng.lat, latlng.lng]).addTo(map);
  markers = [newMarker];
  getAddressFromCoordinates(latlng.lat, latlng.lng);
});

function getAddressFromCoordinates(lat, lon) {
  const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&addressdetails=1`;

  fetch(url)
    .then((response) => response.json())
    .then((data) => {
      if (data && data.address) {
        const address = data.address;
        console.log("Adresse obtenue : ", address);

        const place_form_address =
          document.getElementById("place_form_address");
        place_form_address.value = JSON.stringify(address);
      } else {
        alert("Aucune adresse trouvée.");
      }
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération de l'adresse:", error);
      alert("Erreur lors de la récupération de l'adresse.");
    });
}
