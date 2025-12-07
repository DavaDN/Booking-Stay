import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

// Auto-refresh CSRF token setiap 30 menit
setInterval(() => {
    fetch("/csrf-token")
        .then((response) => response.json())
        .then((data) => {
            document
                .querySelector('meta[name="csrf-token"]')
                .setAttribute("content", data.token);
        })
        .catch((error) => console.log("CSRF refresh error:", error));
}, 30 * 60 * 1000);

// Update CSRF token di axios interceptor
if (window.axios) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
}
