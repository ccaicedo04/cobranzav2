
(function(){
  // dropdown exclusivas
  const closeAll = ()=> document.querySelectorAll('.dropdown').forEach(d=>d.style.display='none');
  document.querySelectorAll('.nav .group').forEach(g=>{
    const btn = g.querySelector('button'); const dd = g.querySelector('.dropdown');
    btn.addEventListener('click', e=>{ e.preventDefault(); e.stopPropagation(); const vis = dd.style.display==='block'; closeAll(); dd.style.display = vis?'none':'block'; });
  });
  document.addEventListener('click', closeAll);

  // user menu
  const ub = document.getElementById('userBtn'); const um = document.getElementById('userMenu');
  if(ub){ ub.addEventListener('click', e=>{ e.preventDefault(); e.stopPropagation(); um.style.display = um.style.display==='block'?'none':'block'; }); document.addEventListener('click', ()=> um.style.display='none'); }

  // Router (hash -> iframe) usando rutas relativas
  const iframe = document.getElementById('appFrame');
  function routeTo(path){ if(!path) path='screens/dashboard.php'; if(path.startsWith('/')) path = path.replace(/^\/+/, ''); iframe.src = path; location.hash = 'route=' + path; }
  function readHash(){ const h = new URLSearchParams(location.hash.replace(/^#/,'')).get('route'); return h || 'screens/dashboard.php'; }
  document.querySelectorAll('[data-route]').forEach(a=> a.addEventListener('click', e=>{ e.preventDefault(); routeTo(a.getAttribute('data-route')); }));
  window.addEventListener('hashchange', ()=> routeTo(readHash()));
  routeTo(readHash());

  // Confirm logout
  const logout = document.getElementById('logoutLink');
  if(logout){ logout.addEventListener('click', (e)=>{ if(!confirm('¿Seguro que deseas cerrar sesión?')) e.preventDefault(); }); }
})();
