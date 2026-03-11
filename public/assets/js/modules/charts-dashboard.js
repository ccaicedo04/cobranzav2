
(function(){
  if(!window.Chart) return;
  const ch1 = document.getElementById('ch1'); if(ch1) new Chart(ch1,{type:'doughnut',data:{labels:['María','Juan','Ana','Ricardo','Luis'],datasets:[{data:[45,38,22,18,15]}]}});
  const ch2 = document.getElementById('ch2'); if(ch2) new Chart(ch2,{type:'bar',data:{labels:['2025-03','2025-04','2025-05','2025-06','2025-07','2025-08'],datasets:[{label:'Cartera',data:[30,35,28,40,32,36]}]}});
  const ch3 = document.getElementById('ch3'); if(ch3) new Chart(ch3,{type:'line',data:{labels:['2025-03','2025-04','2025-05','2025-06','2025-07','2025-08'],datasets:[{label:'Recaudo',data:[15,18,12,22,16,20]}]}});
})();
