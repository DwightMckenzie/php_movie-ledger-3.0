/**
 * Site-wide behaviour.
 * Any form with a data-confirm attribute asks the user before submitting.
 */
(() => {
  'use strict';

  document.addEventListener('submit', (event) => {
    const message = event.target.dataset?.confirm;
    if (message && !window.confirm(message)) {
      event.preventDefault();
    }
  });
})();
