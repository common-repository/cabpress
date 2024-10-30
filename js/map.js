/**
 * @license
 * Copyright 2019 Google LLC. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0
 */
// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script
// src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

// Get PRICE per km
let PRICE_PER_KM = document.currentScript.getAttribute('PRICE_PER_KM');
let cityMap = document.currentScript.getAttribute('CITY_MAP');
let country = document.currentScript.getAttribute('COUNTRY');

function initMap() {
  var address = cityMap+', '+country;
  new google.maps.Geocoder().geocode({'address': address}, function(results, status) {
  if (status == google.maps.GeocoderStatus.OK) {
    locationDefined = true;
    defaultMapLat = results[0].geometry.location.lat();
    defaultMapLng = results[0].geometry.location.lng();

    const map = new google.maps.Map(document.getElementById("map"), {
      mapTypeControl: false,
      center: { lat: defaultMapLat, lng: defaultMapLng },
      zoom: 13,
    });
  
    new AutocompleteDirectionsHandler(map);
  
    }else {
      console.log('Error on Geocoder.geocode: '+ status);
      //alert('Geocoder failed due to: ' + status);
    }
  })
}

class AutocompleteDirectionsHandler {
  map;
  originPlaceId;
  destinationPlaceId;
  travelMode;
  directionsService;
  directionsRenderer;
  distanceService;
  distanceE;
  priceE;
  priceForm;
  
  constructor(map) {
    this.map = map;
    this.originPlaceId = "";
    this.destinationPlaceId = "";
    this.travelMode = google.maps.TravelMode.DRIVING;
    this.directionsService = new google.maps.DirectionsService();
    this.directionsRenderer = new google.maps.DirectionsRenderer();
    this.distanceService = new google.maps.DistanceMatrixService();
    this.directionsRenderer.setMap(map);

    const originInput = document.getElementById("origin-input");
    const destinationInput = document.getElementById("destination-input");
    this.distanceE = document.querySelector(".distance");
    this.distanceInput = document.getElementById('order-distance');
    this.priceE = document.querySelector(".price");
    this.priceForm = document.querySelector(".priceEnd");

    //const modeSelector = document.getElementById("mode-selector");
    // Specify just the place data fields that you need.
    const originAutocomplete = new google.maps.places.Autocomplete(
      originInput,
      { fields: ["place_id"] }
    );

    // Specify just the place data fields that you need.
    const destinationAutocomplete = new google.maps.places.Autocomplete(
      destinationInput,
      { fields: ["place_id"] }
    );
    //console.log(originAutocomplete);
    //console.log(destinationAutocomplete);

    this.setupPlaceChangedListener(originAutocomplete, "ORIG");
    this.setupPlaceChangedListener(destinationAutocomplete, "DEST");
  }

  // Sets a listener on a radio button to change the filter type on Places
  // Autocomplete.
  setupClickListener(id, mode) {
    const radioButton = document.getElementById(id);

    radioButton.addEventListener("click", () => {
      this.travelMode = mode;
      this.route();
    });
  }
  setupPlaceChangedListener(autocomplete, mode) {
    autocomplete.bindTo("bounds", this.map);
    autocomplete.addListener("place_changed", () => {
      const place = autocomplete.getPlace();

      if (!place.place_id) {
        window.alert("Please select an option from the dropdown list.");
        return;
      }

      if (mode === "ORIG") {
        this.originPlaceId = place.place_id;
      } else {
        this.destinationPlaceId = place.place_id;
      }
      this.route();
    });
  }
  route() {
    if (!this.originPlaceId || !this.destinationPlaceId) {
      return;
    }

    const me = this;

    this.directionsService.route(
      {
        origin: { placeId: this.originPlaceId },
        destination: { placeId: this.destinationPlaceId },
        travelMode: this.travelMode,
      },
      (response, status) => {
        if (status === "OK") {
          me.directionsRenderer.setDirections(response);
          this.distanceE.innerHTML = 'Distance estimée: ' + JSON.stringify(response.routes[0].legs[0].distance.text);
          this.distanceInput.value = JSON.stringify(response.routes[0].legs[0].distance.value);
          getFormData();
          //let finalPrice = JSON.stringify(response.routes[0].legs[0].distance.value) / 1000 * PRICE_PER_KM;
          //this.priceE.innerHTML = 'Prix de la course: ' + finalPrice.toFixed(2);
          //this.priceForm.innerHTML = finalPrice.toFixed(2);
        } else {
          window.alert("Directions request failed due to " + status);
        }
      }
    );
  }
  
}
window.initMap = initMap;
