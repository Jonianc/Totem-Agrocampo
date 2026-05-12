(function(){
  const app=document.querySelector('.agp-kiosk-app');
  if(!app){return;}
  const screens=[...document.querySelectorAll('[data-screen]')];
  const progress=[...document.querySelectorAll('.agp-progress-step')];
  const history=['home'];
  let selectedType='';
  let selectedProduct='';
  const initialScreen=app.dataset.initialScreen||'home';

  function clearFlowState(){
    selectedType='';
    selectedProduct='';
    history.splice(0,history.length,'home');
    const selected=document.querySelector('.agp-selected-type');
    if(selected){selected.textContent='';}
    const productField=document.getElementById('agp-selected-product');
    if(productField){productField.value='';}
    const form=document.querySelector('.agp-totem-form');
    if(form){form.reset();}
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
    if(btn.dataset.product){
      selectedProduct=btn.dataset.product;
      const productField=document.getElementById('agp-selected-product');
      if(productField){productField.value=selectedProduct;}
    }
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

  show(initialScreen);
  if(initialScreen!=='success' && initialScreen!=='form'){
    clearFlowState();
    show('home');
  }
})();
