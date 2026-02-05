(function(){
  function renderCharts() {
    if (!window.Chart) {
      return setTimeout(renderCharts, 100);
    }

    const responsables = (typeof topResponsables !== 'undefined') ? topResponsables : [];
    const cartera = (typeof carteraMeses !== 'undefined') ? carteraMeses : [];
    const recaudo = (typeof recaudoMeses !== 'undefined') ? recaudoMeses : [];

    const ch1 = document.getElementById('ch1');
    if (ch1 && responsables.length) {
      new Chart(ch1, {
        type: 'doughnut',
        data: {
          labels: responsables.map(r => r.nombre),
          datasets: [{
            data: responsables.map(r => r.total),
            backgroundColor: ['#16a34a','#f59e0b','#ef4444','#2563eb','#0ea5e9']
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });
    }

    const ch2 = document.getElementById('ch2');
    if (ch2) {
      new Chart(ch2, {
        type: 'bar',
        data: {
          labels: cartera.map(row => row.periodo),
          datasets: [{
            label: 'Cartera',
            data: cartera.map(row => Number(row.total)),
            backgroundColor: '#2563eb'
          }]
        }
      });
    }

    const ch3 = document.getElementById('ch3');
    if (ch3) {
      new Chart(ch3, {
        type: 'line',
        data: {
          labels: recaudo.map(row => row.periodo),
          datasets: [{
            label: 'Recaudo',
            data: recaudo.map(row => Number(row.total)),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,.18)',
            tension: 0.3,
            fill: true
          }]
        }
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderCharts);
  } else {
    renderCharts();
  }
})();
