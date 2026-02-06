// js/app.js - minimal JS helpers (no localStorage usage; server-side handles data)
(function(){
  // keep a minimal namespace for potential future client behavior
  window.App = {
    showMessage: function(msg) {
      var el = document.getElementById('loginMsg');
      if (el) el.textContent = msg;
    }
  };
})();

