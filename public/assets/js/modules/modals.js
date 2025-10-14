
(function(){
  document.addEventListener('click',function(e){
    const t = e.target;
    if(t.matches('[data-open]')){
      e.preventDefault();
      const id = t.getAttribute('data-open');
      const m = parent.document.getElementById(id) || document.getElementById(id);
      if(m){ m.style.display='flex'; }
    }
    if(t.matches('[data-close]') || t.classList.contains('modal-backdrop')){
      const m = t.closest('.modal-backdrop') || document.querySelector('.modal-backdrop'); if(m){ m.style.display='none'; }
    }
  });
})();
