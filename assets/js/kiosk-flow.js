(function(){
  const app=document.querySelector('.agp-kiosk-app');
  if(!app){return;}
  const screens=[...document.querySelectorAll('[data-screen]')];
  const progress=[...document.querySelectorAll('.agp-progress-step')];
  const history=['home'];
  let selectedType='';

  function clearFlowState(){
    selectedType='';
    history.splice(0,history.length,'home');
    const selected=document.querySelector('.agp-selected-type');
    if(selected){selected.textContent='';}
  }

  function show(screen){
    screens.forEach(s=>s.classList.toggle('is-hidden',s.dataset.screen!==screen));
    progress.forEach(p=>p.classList.toggle('is-active',p.dataset.step===screen));
    if(screen==='category'){
      const el=document.querySelector('.agp-selected-type');
      if(el){el.textContent=selectedType ? 'Tipo de atención: '+selectedType : '';}
    }
  }

  document.addEventListener('click',function(e){
    const btn=e.target.closest('[data-next],[data-action]');
    if(!btn){return;}

    if(btn.dataset.type){selectedType=btn.dataset.type;}
    if(btn.dataset.action==='home'){
      clearFlowState();
      show('home');
      return;
    }
    if(btn.dataset.action==='back'){
      if(history.length>1){history.pop();show(history[history.length-1]);}
      return;
    }
    const next=btn.dataset.next;
    if(next){history.push(next);show(next);}
  });

  clearFlowState();
  show('home');
})();
