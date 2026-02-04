
(function(){
  if(!window.Chart) return;
  const baseOptions = {
    responsive:true,
    plugins:{legend:{display:false}},
    scales:{x:{grid:{display:false}},y:{grid:{color:'rgba(148,163,184,.2)'}}}
  };
  const ch1 = document.getElementById('ch1');
  if(ch1){
    new Chart(ch1,{
      type:'doughnut',
      data:{
        labels:['María','Juan','Ana','Ricardo','Luis'],
        datasets:[{data:[45,38,22,18,15],backgroundColor:['#0f766e','#2563eb','#f59e0b','#22c55e','#e11d48']}]
      },
      options:{plugins:{legend:{position:'bottom'}}}
    });
  }
  const ch2 = document.getElementById('ch2');
  if(ch2){
    new Chart(ch2,{
      type:'bar',
      data:{
        labels:['2025-03','2025-04','2025-05','2025-06','2025-07','2025-08'],
        datasets:[{label:'Cartera',data:[30,35,28,40,32,36],backgroundColor:'#2563eb',borderRadius:10}]
      },
      options:baseOptions
    });
  }
  const ch3 = document.getElementById('ch3');
  if(ch3){
    new Chart(ch3,{
      type:'line',
      data:{
        labels:['2025-03','2025-04','2025-05','2025-06','2025-07','2025-08'],
        datasets:[{label:'Recaudo',data:[15,18,12,22,16,20],borderColor:'#0f766e',backgroundColor:'rgba(15,118,110,.15)',fill:true,tension:.4}]
      },
      options:baseOptions
    });
  }
})();
