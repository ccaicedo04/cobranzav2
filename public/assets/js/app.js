(function () {
  function onReady(callback) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback, { once: true });
    } else {
      callback();
    }
  }

  function setupConfirmations() {
    document.addEventListener('click', function (event) {
      const trigger = event.target.closest('[data-confirm]');
      if (!trigger) {
        return;
      }
      const tag = trigger.tagName;
      if (tag === 'A' || tag === 'BUTTON') {
        const message = trigger.getAttribute('data-confirm');
        if (message && !window.confirm(message)) {
          event.preventDefault();
          event.stopImmediatePropagation();
        }
      }
    });

    document.addEventListener('submit', function (event) {
      const form = event.target;
      if (!(form instanceof HTMLFormElement)) {
        return;
      }
      let message = form.getAttribute('data-confirm');
      if (!message && event.submitter && event.submitter instanceof HTMLElement) {
        const submitterMessage = event.submitter.getAttribute('data-confirm');
        if (submitterMessage) {
          message = submitterMessage;
        }
      }
      if (message && !window.confirm(message)) {
        event.preventDefault();
        event.stopImmediatePropagation();
      }
    });
  }

  function setupNavDropdowns() {
    const groups = Array.from(document.querySelectorAll('.nav .group'));
    if (!groups.length) {
      return;
    }

    function closeAll(except) {
      groups.forEach(function (group) {
        if (group !== except) {
          group.classList.remove('open');
          const btn = group.querySelector('[data-dropdown-toggle]');
          if (btn) {
            btn.setAttribute('aria-expanded', 'false');
          }
        }
      });
    }

    function openGroup(group) {
      const toggle = group.querySelector('[data-dropdown-toggle]');
      closeAll(group);
      group.classList.add('open');
      if (toggle) {
        toggle.setAttribute('aria-expanded', 'true');
      }
    }

    function closeGroup(group) {
      const toggle = group.querySelector('[data-dropdown-toggle]');
      group.classList.remove('open');
      if (toggle) {
        toggle.setAttribute('aria-expanded', 'false');
      }
    }

    groups.forEach(function (group) {
      const toggle = group.querySelector('[data-dropdown-toggle]');
      if (!toggle) {
        return;
      }

      let hoverTimeout;

      toggle.addEventListener('click', function (event) {
        event.preventDefault();
        if (group.classList.contains('open')) {
          closeGroup(group);
        } else {
          openGroup(group);
        }
      });

      group.addEventListener('mouseenter', function () {
        if (!window.matchMedia('(pointer:fine)').matches) {
          return;
        }
        window.clearTimeout(hoverTimeout);
        openGroup(group);
      });

      group.addEventListener('mouseleave', function () {
        if (!window.matchMedia('(pointer:fine)').matches) {
          return;
        }
        window.clearTimeout(hoverTimeout);
        hoverTimeout = window.setTimeout(function () {
          if (!group.matches(':hover')) {
            closeGroup(group);
          }
        }, 320);
      });

      group.addEventListener('focusin', function (event) {
        if (group.contains(event.target)) {
          openGroup(group);
        }
      });

      group.addEventListener('focusout', function (event) {
        if (!group.contains(event.relatedTarget)) {
          window.clearTimeout(hoverTimeout);
          hoverTimeout = window.setTimeout(function () {
            closeGroup(group);
          }, 200);
        }
      });
    });

    document.addEventListener('click', function (event) {
      if (!event.target.closest('.nav .group')) {
        closeAll(null);
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeAll(null);
      }
    });
  }

  function setupUserMenu() {
    const userBox = document.querySelector('.userbox');
    if (!userBox) {
      return;
    }
    const toggle = userBox.querySelector('.user-toggle');
    const menu = userBox.querySelector('.menu');
    if (!toggle || !menu) {
      return;
    }

    function closeMenu() {
      userBox.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function (event) {
      event.preventDefault();
      const isOpen = userBox.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', function (event) {
      if (!userBox.contains(event.target)) {
        closeMenu();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeMenu();
      }
    });
  }

  function setupContextFiltering() {
    const colegio = document.getElementById('navColegio');
    const sede = document.getElementById('navSede');
    const form = colegio ? colegio.form : null;
    if (!colegio || !sede) {
      return;
    }

    function applyFilter() {
      const selectedColegio = colegio.value;
      const previousValue = sede.value;
      let keepPrevious = false;

      const optgroups = Array.from(sede.querySelectorAll('optgroup'));
      optgroups.forEach(function (group) {
        const matches = !selectedColegio || group.dataset.colegio === selectedColegio;
        group.hidden = !matches;
        group.disabled = !matches;
        Array.from(group.children).forEach(function (option) {
          if (option.value === previousValue && matches) {
            keepPrevious = true;
          }
          if (!matches) {
            option.hidden = true;
            option.disabled = true;
            option.selected = false;
          } else {
            option.hidden = false;
            option.disabled = false;
          }
        });
      });

      if (!keepPrevious) {
        sede.value = '';
      }
    }

    applyFilter();
    colegio.addEventListener('change', applyFilter);

    if (form) {
      form.addEventListener('submit', function () {
        applyFilter();
      });
    }
  }

  onReady(function () {
    setupConfirmations();
    setupNavDropdowns();
    setupUserMenu();
    setupContextFiltering();
  });
})();
