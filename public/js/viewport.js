// Samakan tinggi halaman dengan area layar yang benar-benar terlihat
function setTinggiLayar() {
  var tinggi = window.visualViewport
    ? window.visualViewport.height
    : window.innerHeight;
  document.body.style.setProperty("--tinggi-layar", tinggi + "px");
}

setTinggiLayar();
window.addEventListener("resize", setTinggiLayar);
window.addEventListener("orientationchange", setTinggiLayar);
document.addEventListener("fullscreenchange", setTinggiLayar);
if (window.visualViewport) {
  window.visualViewport.addEventListener("resize", setTinggiLayar);
}