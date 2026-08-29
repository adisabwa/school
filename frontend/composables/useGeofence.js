import { ref } from 'vue';

const DEFAULT_API_KEY =
  (typeof import.meta !== 'undefined' && import.meta.env?.VITE_GOOGLE_MAPS_API_KEY) ||
  (typeof process !== 'undefined' && process.env?.GOOGLE_MAPS_API_KEY);


export function useGeofence(apiKey = DEFAULT_API_KEY) {
  const userCoords = ref(null);
  const loading = ref(false);
  const error = ref(null);
  let mapInstance = null;

  const loadGoogleMapsScript = () => {
    return new Promise((resolve, reject) => {
      if (!apiKey) {
        reject(new Error('Google Maps API key is missing. Check your .env file.'));
        return;
      }

      if (window.google && window.google.maps && window.google.maps.geometry) {
        resolve(window.google);
        return;
      }

      const existingScript = document.getElementById('google-maps-script');
      if (existingScript) {
        existingScript.addEventListener('load', () => resolve(window.google));
        return;
      }

      const script = document.createElement('script');
      script.id = 'google-maps-script';
      script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=geometry`;
      script.async = true;
      script.defer = true;
      script.onload = () => resolve(window.google);
      script.onerror = () => reject(new Error('Failed to load Google Maps SDK.'));
      document.head.appendChild(script);
    });
  };

  const getCurrentLocation = () => {
    return new Promise((resolve, reject) => {
    if (!('geolocation' in navigator)) {
      const errorMsg = 'Geolocation is not supported by your browser.';
      error.value = errorMsg;
      reject(new Error(errorMsg));
      return;
    }

    loading.value = true;
    error.value = null;

    let watchId = null;
    let timerId = null;
    let bestPosition = null;

    // Fungsi pembersihan resource
    const cleanup = () => {
      if (watchId !== null) navigator.geolocation.clearWatch(watchId);
      if (timerId !== null) clearTimeout(timerId);
      loading.value = false;
    };

    // 1. BATAS MAKSIMAL 3 DETIK (3000 ms)
    timerId = setTimeout(() => {
      cleanup();

      if (bestPosition) {
        const coords = {
          lat: bestPosition.coords.latitude,
          lng: bestPosition.coords.longitude,
          accuracy: bestPosition.coords.accuracy,
        };
        userCoords.value = coords;
        console.log(`Batas 3 detik habis. Menggunakan akurasi terbaik: ${coords.accuracy}m`);
        resolve(coords);
      } else {
        const timeoutMsg = 'Gagal mendapatkan sinyal lokasi dalam 3 detik.';
        error.value = timeoutMsg;
        reject(new Error(timeoutMsg));
      }
    }, 3000); // ⏱️ MAKSIMAL 3 DETIK

    // 2. Akselerasi Pembacaan GPS
    watchId = navigator.geolocation.watchPosition(
      (position) => {
        const accuracy = position.coords.accuracy;
        console.log(`Sinyal masuk: ${accuracy}m`);

        // Simpan jika belum ada data atau jika data baru LEBIH PRESISI
        if (!bestPosition || accuracy < bestPosition.coords.accuracy) {
          bestPosition = position;
        }

        // ⚡ EARLY EXIT: Jika akurasi sudah sangat bagus (<= 15 meter), LANGSUNG FINISH!
        if (accuracy <= 15) {
          console.log(`Akurasi presisi (${accuracy}m) didapat cepat! Langsung selesaikan.`);
          cleanup();
          const coords = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
            accuracy: accuracy,
          };
          userCoords.value = coords;
          resolve(coords);
        }
      },
      (err) => {
        if (!bestPosition) {
          cleanup();
          let message = 'Terjadi kesalahan saat mengambil lokasi.';
          switch (err.code) {
            case err.PERMISSION_DENIED:
              message = 'Akses lokasi ditolak oleh pengguna.';
              break;
            case err.POSITION_UNAVAILABLE:
              message = 'Informasi lokasi tidak tersedia.';
              break;
            case err.TIMEOUT:
              message = 'Waktu permintaan lokasi habis.';
              break;
          }
          error.value = message;
          reject(new Error(message));
        }
      },
      {
        enableHighAccuracy: true,
        timeout: 2800, // Timeout internal disesuaikan under 3s
        maximumAge: 0,
      }
    );
  });
  };

  const isInsidePolygon = async (coords, polygonPaths) => {
  try {
    // 1. Pastikan SDK Google Maps & Library Geometry Terload
    await loadGoogleMapsScript();

    if (!google.maps.geometry || !google.maps.geometry.poly) {
      throw new Error("Google Maps Geometry library is not loaded. Make sure 'libraries=geometry' is in script URL.");
    }

    // 2. Format ulang titik pengguna (Pastikan tipe data Float/Number)
    const userPoint = new google.maps.LatLng(
      parseFloat(coords.lat), 
      parseFloat(coords.lng)
    );

    // 3. Format ulang path polygon (Pastikan tipe data Float/Number)
    const formattedPaths = polygonPaths.map(point => ({
      lat: parseFloat(point.lat),
      lng: parseFloat(point.lng)
    }));

    // 4. Inisialisasi Objek Polygon Google Maps
    const polygon = new google.maps.Polygon({ paths: formattedPaths });

    // 5. Cek apakah titik berada di dalam polygon
    const isInside = google.maps.geometry.poly.containsLocation(userPoint, polygon);

    console.log("User Point:", userPoint.toString());
    console.log("Geofence Check Result:", isInside);

    return isInside; // Mengembalikan Promise<boolean> (true/false)
  } catch (err) {
    console.error("Geofence Check Error:", err);
    if (typeof error !== 'undefined') {
      error.value = err.message;
    }
    return false;
  }
};

  /**
   * Renders an interactive map into a target HTML container element
   * @param {HTMLElement} containerRef - HTML element reference (e.g. from Vue template ref)
   * @param {Array} [geofencePolygon] - Optional polygon coordinates to draw on top of the map
   */
  const renderMap = async (containerRef, geofencePolygon = null, multipleCoord = false) => {
    if (!userCoords.value) {
      error.value = 'No coordinates available to display on map.';
      return;
    }

    try {
      await loadGoogleMapsScript();

      const userLatLng = { lat: userCoords.value.lat, lng: userCoords.value.lng };

      // Initialize map instance
      mapInstance = new google.maps.Map(containerRef, {
        center: userLatLng,
        zoom: 17,
        mapTypeId: 'roadmap',
        disableDefaultUI: true,
      });

      // User location marker
      new google.maps.Marker({
        position: userLatLng,
        map: mapInstance,
        title: 'Your Current Location',
        animation: google.maps.Animation.DROP,
      });

      // Render geofence perimeter on map if provided
      console.log('geofencePolygon', geofencePolygon)
      if (geofencePolygon) {
        if (!multipleCoord)
          geofencePolygon = [geofencePolygon]
          geofencePolygon.forEach(polygon => {
            if (polygon.length > 0) {
              const areaPolygon = new google.maps.Polygon({
                paths: polygon,
                strokeColor: '#3B82F6',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#60A5FA',
                fillOpacity: 0.35,
              });

              areaPolygon.setMap(mapInstance);
            }
          })
      }
    } catch (err) {
      error.value = err.message;
    }
  };

  /**
   * Opens current coordinates in external Google Maps app/website
   */
  const openInGoogleMaps = () => {
    // console.log(userCoords)
    if (!userCoords.value) return;
    const { lat, lng } = userCoords.value;
    const url = `https://www.google.com/maps?q=${lat},${lng}`;
    window.open(url, '_blank');
  };

  return {
    userCoords,
    loading,
    error,
    getCurrentLocation,
    isInsidePolygon,
    openInGoogleMaps,
    renderMap,
  };
}