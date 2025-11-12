(function(){
  const ready = () => {
    const body = document.body;
    const placeholders = {
      responsable: body.dataset.responsable || '',
      periodo: body.dataset.periodo || '',
      valor: body.dataset.valor || '',
      'fecha limite': body.dataset.fechaLimite || body.dataset.fecha_limite || '',
      fecha_limite: body.dataset.fechaLimite || body.dataset.fecha_limite || '',
      estudiante: body.dataset.estudiante || ''
    };

    const normalizeTemplate = (text) => {
      if (!text) return '';
      return text.replace(/\{\{([^}]+)\}\}/g, (_, key) => {
        const normalized = key.trim().toLowerCase();
        return Object.prototype.hasOwnProperty.call(placeholders, normalized) && placeholders[normalized]
          ? placeholders[normalized]
          : `{{${key}}}`;
      });
    };

    const messageInput = document.getElementById('messageInput');
    const templateButtons = document.querySelectorAll('.template-chip[data-message]');
    templateButtons.forEach((button) => {
      button.addEventListener('click', () => {
        const template = button.getAttribute('data-message');
        if (!template || !messageInput) return;
        const parsed = normalizeTemplate(template);
        if (!messageInput.value) {
          messageInput.value = parsed;
        } else {
          messageInput.value = `${messageInput.value}\n\n${parsed}`.trim();
        }
        messageInput.focus();
      });
    });

    document.querySelectorAll('.notification').forEach((notification) => {
      notification.addEventListener('click', () => {
        const target = notification.getAttribute('data-target');
        if (target) {
          const element = document.getElementById(target.replace('#', '')) || document.querySelector(target);
          if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
        notification.classList.remove('is-unread');
      });
    });

    document.querySelectorAll('[data-scroll]').forEach((button) => {
      button.addEventListener('click', () => {
        const target = button.getAttribute('data-scroll');
        if (target) {
          const element = document.querySelector(target);
          if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      });
    });

    const openTemplates = document.getElementById('openTemplates');
    if (openTemplates) {
      openTemplates.addEventListener('click', () => {
        const templates = document.getElementById('templatesLibrary');
        if (templates) {
          templates.scrollIntoView({ behavior: 'smooth', block: 'start' });
          templates.classList.add('message--highlight');
          setTimeout(() => templates.classList.remove('message--highlight'), 1600);
        }
      });
    }

    const chatThread = document.getElementById('whatsappThread');
    const typingIndicator = document.getElementById('typingIndicator');
    const channelSelect = document.getElementById('channelSelect');
    const scheduleAt = document.getElementById('scheduleAt');
    const attachmentInput = document.getElementById('attachmentInput');
    const selectedFiles = document.getElementById('selectedFiles');

    const renderFilePreview = () => {
      if (!selectedFiles) return;
      selectedFiles.innerHTML = '';
      if (!attachmentInput || !attachmentInput.files) return;
      Array.from(attachmentInput.files).forEach((file, index) => {
        const pill = document.createElement('span');
        pill.className = 'file-pill';
        pill.textContent = file.name;
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = '×';
        removeBtn.addEventListener('click', () => {
          const data = new DataTransfer();
          Array.from(attachmentInput.files).forEach((current, idx) => {
            if (idx !== index) {
              data.items.add(current);
            }
          });
          attachmentInput.files = data.files;
          renderFilePreview();
        });
        pill.appendChild(removeBtn);
        selectedFiles.appendChild(pill);
      });
    };

    if (attachmentInput) {
      attachmentInput.addEventListener('change', renderFilePreview);
    }

    const formatTime = (date) => date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });

    const appendMessage = (direction, text, timestamp, files = [], options = {}) => {
      if (!chatThread) return;
      const message = document.createElement('div');
      message.className = `message message-${direction === 'out' ? 'out' : 'in'}`;
      const bubble = document.createElement('div');
      bubble.className = 'message-bubble';

      const header = document.createElement('header');
      header.textContent = direction === 'out' ? 'Laura Espinosa • Gestora' : (body.dataset.responsable || 'Contacto');
      bubble.appendChild(header);

      if (text) {
        text.split(/\n+/).forEach((line) => {
          const p = document.createElement('p');
          p.textContent = line;
          bubble.appendChild(p);
        });
      }

      const blobUrls = [];
      if (files.length) {
        files.forEach((file) => {
          const link = document.createElement('a');
          link.className = 'message-attachment';
          link.textContent = `📎 ${file.name}`;
          const blobUrl = URL.createObjectURL(file);
          link.href = blobUrl;
          link.download = file.name;
          bubble.appendChild(link);
          blobUrls.push(blobUrl);
        });
      }

      if (options.scheduledNote) {
        const note = document.createElement('div');
        note.className = 'scheduled-note';
        note.textContent = options.scheduledNote;
        bubble.appendChild(note);
      }

      const footer = document.createElement('footer');
      const timeSpan = document.createElement('span');
      timeSpan.textContent = formatTime(timestamp);
      footer.appendChild(timeSpan);

      if (direction === 'out') {
        const status = document.createElement('span');
        status.className = 'status';
        status.textContent = options.statusLabel || 'Enviado ✓✓';
        footer.appendChild(status);
      }

      bubble.appendChild(footer);
      message.appendChild(bubble);

      if (typingIndicator && typingIndicator.parentElement === chatThread) {
        chatThread.insertBefore(message, typingIndicator);
      } else {
        chatThread.appendChild(message);
      }

      chatThread.scrollTop = chatThread.scrollHeight;
      return { element: message, urls: blobUrls };
    };

    const inboundSamples = [
      '¡Gracias! Quedo atenta al soporte para mi correo.',
      '¿Puedes enviarme el estado de cuenta actualizado por favor?',
      'Perfecto, si necesitan algo adicional me escriben.',
      'El pago se realizó con tarjeta, adjunto soporte en un momento.'
    ];

    const chatComposer = document.getElementById('chatComposer');
    if (chatComposer) {
      chatComposer.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!messageInput) return;
        const channel = channelSelect ? channelSelect.value : 'whatsapp';
        const scheduleValue = scheduleAt && scheduleAt.value ? new Date(scheduleAt.value) : null;
        const now = new Date();
        const files = attachmentInput && attachmentInput.files ? Array.from(attachmentInput.files) : [];
        const text = messageInput.value.trim();
        if (!text && files.length === 0) {
          return;
        }

        let scheduledNote = '';
        let statusLabel = 'Enviado ✓✓';
        if (scheduleValue && scheduleValue > now) {
          const formatted = scheduleValue.toLocaleString('es-CO', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' });
          scheduledNote = `Programado para ${formatted} vía ${channel.toUpperCase()}`;
          statusLabel = 'Programado ⏰';
        }

        const result = appendMessage('out', text, now, files, {
          scheduledNote,
          statusLabel
        });

        if (result && result.urls) {
          result.urls.forEach((url) => setTimeout(() => URL.revokeObjectURL(url), 2000));
        }

        if (result && result.element) {
          result.element.classList.add('message--highlight');
          setTimeout(() => result.element.classList.remove('message--highlight'), 1600);
        }

        if (attachmentInput) {
          attachmentInput.value = '';
        }
        if (scheduleAt) {
          scheduleAt.value = '';
        }
        renderFilePreview();
        chatComposer.reset();
        if (channelSelect) {
          channelSelect.value = channel;
        }
        if (messageInput) {
          messageInput.focus();
        }

        if (typingIndicator) {
          typingIndicator.hidden = false;
        }

        setTimeout(() => {
          if (typingIndicator) {
            typingIndicator.hidden = true;
          }
          const inboundText = inboundSamples[Math.floor(Math.random() * inboundSamples.length)];
          const incoming = appendMessage('in', inboundText, new Date());
          if (incoming && incoming.element) {
            incoming.element.classList.add('message--highlight');
            setTimeout(() => incoming.element.classList.remove('message--highlight'), 2000);
          }
          const notif = document.querySelector('.notification[data-target="whatsappSection"]');
          if (notif) {
            notif.classList.add('is-unread');
          }
        }, 2200);
      });
    }

    const callForm = document.getElementById('callForm');
    const callLog = document.getElementById('callLog');
    if (callForm && callLog) {
      callForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const dateValue = document.getElementById('callDate').value;
        const timeValue = document.getElementById('callTime').value;
        const durationValue = document.getElementById('callDuration').value;
        const notesValue = document.getElementById('callNotes').value.trim();
        if (!dateValue || !timeValue) {
          return;
        }
        const [year, month, day] = dateValue.split('-');
        const formattedDate = `${day}/${month}/${year} • ${timeValue}`;
        const eventBlock = document.createElement('div');
        eventBlock.className = 'call-event message--highlight';
        const strong = document.createElement('strong');
        strong.textContent = formattedDate;
        const span = document.createElement('span');
        span.textContent = `Duración: ${durationValue || '0'} min • Registró Laura Espinosa`;
        const paragraph = document.createElement('p');
        paragraph.textContent = notesValue || 'Sin observaciones adicionales.';
        eventBlock.appendChild(strong);
        eventBlock.appendChild(span);
        eventBlock.appendChild(paragraph);
        callLog.prepend(eventBlock);
        setTimeout(() => eventBlock.classList.remove('message--highlight'), 1800);
        callForm.reset();
      });
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ready);
  } else {
    ready();
  }
})();
