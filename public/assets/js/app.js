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
    if (!colegio || !sede || !form) {
      return;
    }

    let submitTimeout = null;
    const redirectInput = form.querySelector('#contextRedirect');
    const basePath = (form.dataset.basePath || '').replace(/\/$/, '');

    function buildRedirectValue() {
      try {
        const url = new URL(window.location.href);
        let path = url.pathname;
        if (basePath && path.startsWith(basePath)) {
          path = path.slice(basePath.length);
        }
        path = path.replace(/^\/+/, '');
        if (!path || path === 'public') {
          path = 'index.php';
        }
        if (!path.includes('index.php')) {
          path = 'index.php';
        }
        const query = url.search;
        return query ? path + query : path;
      } catch (error) {
        return 'index.php';
      }
    }

    function scheduleSubmit() {
      window.clearTimeout(submitTimeout);
      submitTimeout = window.setTimeout(function () {
        if (redirectInput) {
          redirectInput.value = buildRedirectValue();
        }
        if (typeof form.requestSubmit === 'function') {
          form.requestSubmit();
        } else {
          form.submit();
        }
      }, 120);
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

    colegio.addEventListener('change', function () {
      applyFilter();
      scheduleSubmit();
    });

    sede.addEventListener('change', function () {
      scheduleSubmit();
    });
  }

  function setupComunicaciones() {
    const shell = document.getElementById('comunicacionesApp');
    if (!shell || typeof window.COMMS_DATA !== 'object' || window.COMMS_DATA === null) {
      return;
    }

    const data = window.COMMS_DATA;
    const form = shell.querySelector('form.comms-form');
    if (!form) {
      return;
    }

    const responsableSelect = form.querySelector('#commsResponsable');
    const estudianteSelect = form.querySelector('#commsEstudiante');
    const canalSelect = form.querySelector('#commsCanal');
    const plantillaSelect = form.querySelector('#commsPlantilla');
    const asuntoInput = form.querySelector('#commsAsunto');
    const mensajeArea = form.querySelector('#commsMensaje');
    const resultadoInput = form.querySelector('#commsResultado');
    const resumeSaldo = form.querySelector('[data-resume-saldo]');
    const resumeDeudas = form.querySelector('[data-resume-deudas]');
    const resumeVencimiento = form.querySelector('[data-resume-vencimiento]');
    const previewDestinatario = shell.querySelector('[data-preview-destinatario]');
    const previewCanal = shell.querySelector('[data-preview-canal]');
    const previewContainer = shell.querySelector('[data-preview]');

    const plantillasMap = {};
    (data.plantillas || []).forEach(function (plantilla) {
      plantillasMap[String(plantilla.id_plantilla)] = plantilla;
    });

    function getResponsableData(id) {
      if (!id) {
        return null;
      }
      return data.responsables && data.responsables[String(id)] ? data.responsables[String(id)] : null;
    }

    function getEstudianteData(responsableId, estudianteId) {
      if (!estudianteId) {
        return null;
      }
      const responsable = getResponsableData(responsableId);
      if (!responsable) {
        return null;
      }

      const estudiantes = responsable.estudiantes || [];
      for (let index = 0; index < estudiantes.length; index += 1) {
        if (Number(estudiantes[index].id) === Number(estudianteId)) {
          return estudiantes[index];
        }
      }
      return null;
    }

    function buildPlaceholders(responsableId, estudianteId) {
      const responsable = getResponsableData(responsableId) || {};
      const estudiante = getEstudianteData(responsableId, estudianteId) || null;
      const sede = responsable.sede_id && data.sedes ? data.sedes[String(responsable.sede_id)] : null;
      const colegio = data.colegio || {};

      const saldoTotal = Number(responsable.totales && responsable.totales.saldo ? responsable.totales.saldo : 0);
      const saldoEstudiante = estudiante ? Number(estudiante.saldo || 0) : 0;
      const saldoFormateado = function (valor) {
        return '$ ' + Number(valor || 0).toLocaleString('es-CO');
      };

      let fechaVencimiento = '';
      if (estudiante && estudiante.fecha_vencimiento) {
        fechaVencimiento = estudiante.fecha_vencimiento;
      } else if (responsable.totales && responsable.totales.proximo_vencimiento) {
        fechaVencimiento = responsable.totales.proximo_vencimiento;
      }

      const telefonoContacto = sede && sede.telefono ? sede.telefono : (colegio.telefono || '');
      const correoContacto = sede && sede.correo ? sede.correo : (colegio.correo || '');

      return {
        responsable_nombre: responsable.nombre || '',
        responsable_documento: responsable.documento || '',
        responsable_correo: responsable.correo || '',
        responsable_telefono: responsable.telefono || '',
        estudiante_nombre: estudiante ? estudiante.nombre || '' : '',
        codigo_estudiante: estudiante ? estudiante.codigo || '' : '',
        grado: estudiante ? estudiante.grado || '' : '',
        curso: estudiante ? estudiante.curso || '' : '',
        concepto: estudiante ? estudiante.conceptos || '' : '',
        saldo_pendiente: saldoFormateado(estudiante ? saldoEstudiante : saldoTotal),
        saldo_total: saldoFormateado(saldoTotal),
        fecha_vencimiento: fechaVencimiento || '',
        colegio_nombre: colegio.nombre || '',
        sede_nombre: (sede && sede.nombre) || responsable.sede_nombre || '',
        telefono_contacto: telefonoContacto,
        correo_contacto: correoContacto,
        direccion_colegio: colegio.direccion || '',
      };
    }

    function applyPlaceholders(template, placeholders) {
      if (!template) {
        return '';
      }
      return template.replace(/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/g, function (match, key) {
        const normalized = String(key || '').toLowerCase();
        if (Object.prototype.hasOwnProperty.call(placeholders, normalized)) {
          return placeholders[normalized];
        }
        return '';
      });
    }

    function renderEmailPreview(content, subject, responsableId) {
      const responsable = getResponsableData(responsableId) || {};
      const sede = responsable.sede_id && data.sedes ? data.sedes[String(responsable.sede_id)] : null;
      const colegio = data.colegio || {};
      const sedeNombre = sede && sede.nombre ? 'Sede ' + sede.nombre : '';
      const telefono = sede && sede.telefono ? sede.telefono : (colegio.telefono || '');
      const correo = sede && sede.correo ? sede.correo : (colegio.correo || '');
      const direccion = sede && sede.direccion ? sede.direccion : (colegio.direccion || '');
      const logo = colegio.logo_url || '';

      return [
        '<div class="preview-mail">',
        '  <div class="preview-mail-header">',
        logo ? '    <img src="' + logo + '" alt="' + (colegio.nombre || 'Colegio') + '">' : '',
        '    <div>',
        '      <strong>' + (colegio.nombre || 'Colegio') + '</strong>',
        sedeNombre ? '      <span>' + sedeNombre + '</span>' : '',
        subject ? '      <span class="subject">' + subject + '</span>' : '',
        '    </div>',
        '  </div>',
        '  <div class="preview-mail-body">' + content + '</div>',
        '  <div class="preview-mail-footer">',
        telefono ? '    <span>Tel: ' + telefono + '</span>' : '',
        correo ? '    <span>Correo: ' + correo + '</span>' : '',
        direccion ? '    <span>' + direccion + '</span>' : '',
        '  </div>',
        '</div>',
      ].join('');
    }

    function renderMessagePreview(content, canal) {
      const body = content.replace(/\n/g, '<br>');
      return '<div class="preview-message preview-' + canal + '">' + body + '</div>';
    }

    function updateResume() {
      const responsableId = Number(responsableSelect.value || 0);
      const responsable = getResponsableData(responsableId);
      if (!responsable) {
        resumeSaldo.textContent = '$ 0';
        resumeDeudas.textContent = '0';
        resumeVencimiento.textContent = '—';
        return;
      }
      resumeSaldo.textContent = responsable.totales && responsable.totales.saldo_formateado ? responsable.totales.saldo_formateado : '$ 0';
      resumeDeudas.textContent = responsable.totales && typeof responsable.totales.deudas_activas !== 'undefined'
        ? String(responsable.totales.deudas_activas)
        : '0';
      resumeVencimiento.textContent = responsable.totales && responsable.totales.proximo_vencimiento ? responsable.totales.proximo_vencimiento : '—';
    }

    function updateEstudiantes() {
      const responsableId = Number(responsableSelect.value || 0);
      let firstVisible = null;
      Array.from(estudianteSelect.options).forEach(function (option) {
        if (!option.value) {
          option.hidden = false;
          return;
        }
        const optionResponsable = Number(option.dataset.responsable || 0);
        const matches = optionResponsable === responsableId;
        option.hidden = !matches;
        if (matches && !firstVisible) {
          firstVisible = option;
        }
      });
      if (estudianteSelect.value && estudianteSelect.selectedOptions.length && estudianteSelect.selectedOptions[0].hidden) {
        estudianteSelect.value = '';
      }
      if (!estudianteSelect.value && firstVisible) {
        estudianteSelect.value = firstVisible.value;
      }
    }

    function updateTemplateOptions() {
      const canal = canalSelect.value;
      let shouldReset = false;
      Array.from(plantillaSelect.options).forEach(function (option) {
        const optionCanal = option.dataset.canal;
        if (!optionCanal) {
          option.hidden = false;
          return;
        }
        const matches = optionCanal === canal;
        option.hidden = !matches;
        if (!matches && option.selected) {
          shouldReset = true;
        }
      });
      if (shouldReset) {
        plantillaSelect.value = '';
      }
    }

    function updatePreview() {
      const responsableId = Number(responsableSelect.value || 0);
      const estudianteId = Number(estudianteSelect.value || 0) || null;
      const placeholders = buildPlaceholders(responsableId, estudianteId);
      const responsable = getResponsableData(responsableId);
      const mensajeBase = mensajeArea.value || '';
      const asuntoBase = asuntoInput.value || '';
      const mensajeRenderizado = applyPlaceholders(mensajeBase, placeholders);
      const asuntoRenderizado = applyPlaceholders(asuntoBase, placeholders);

      previewCanal.textContent = canalSelect.selectedOptions.length ? canalSelect.selectedOptions[0].textContent.trim() : '';
      if (responsable) {
        const contacto = responsable.correo || responsable.telefono || '';
        previewDestinatario.textContent = responsable.nombre + (contacto ? ' — ' + contacto : '');
      } else {
        previewDestinatario.textContent = 'Selecciona un responsable';
      }

      if (!mensajeRenderizado) {
        previewContainer.innerHTML = '<div class="placeholder">Completa la información para previsualizar.</div>';
        return;
      }

      if (canalSelect.value === 'email') {
        previewContainer.innerHTML = renderEmailPreview(mensajeRenderizado, asuntoRenderizado, responsableId);
      } else {
        previewContainer.innerHTML = renderMessagePreview(mensajeRenderizado, canalSelect.value);
      }
    }

    function applyTemplate(resetContent) {
      const plantillaId = plantillaSelect.value;
      const plantilla = plantillasMap[plantillaId];
      if (plantilla) {
        if (resetContent || !asuntoInput.value) {
          asuntoInput.value = plantilla.asunto_default || '';
        }
        mensajeArea.value = plantilla.cuerpo_html || '';
      } else if (resetContent) {
        mensajeArea.value = '';
        if (!resultadoInput.value) {
          resultadoInput.value = '';
        }
      }
      updatePreview();
    }

    function initialize() {
      updateTemplateOptions();
      updateEstudiantes();
      updateResume();
      applyTemplate(true);
    }

    responsableSelect.addEventListener('change', function () {
      updateEstudiantes();
      updateResume();
      updatePreview();
    });

    estudianteSelect.addEventListener('change', updatePreview);

    canalSelect.addEventListener('change', function () {
      updateTemplateOptions();
      updatePreview();
    });

    plantillaSelect.addEventListener('change', function () {
      applyTemplate(true);
    });

    asuntoInput.addEventListener('input', updatePreview);
    mensajeArea.addEventListener('input', updatePreview);

    initialize();
  }

  onReady(function () {
    setupConfirmations();
    setupNavDropdowns();
    setupUserMenu();
    setupContextFiltering();
    setupComunicaciones();
  });
})();
