(function(){
  const cfg=window.agpTotemTimeout||{};
  const timeoutMs=(parseInt(cfg.timeoutSeconds,10)||90)*1000;
  const warningMs=(parseInt(cfg.warningSeconds,10)||10)*1000;
  const warningEl=document.getElementById('agp-timeout-warning');
  let timeoutTimer=null;
  let warningTimer=null;

  function goHome(){
    const homeBtn=document.querySelector('[data-action="home"]');
    if(homeBtn){homeBtn.click();}
    if(warningEl){warningEl.classList.add('is-hidden');}
  }

  function startTimers(){
    clearTimeout(timeoutTimer);clearTimeout(warningTimer);
    warningTimer=setTimeout(function(){
      if(warningEl){warningEl.textContent=cfg.warningText||'Reinicio por inactividad';warningEl.classList.remove('is-hidden');}
    },Math.max(0,timeoutMs-warningMs));
    timeoutTimer=setTimeout(goHome,timeoutMs);
  }

  ['click','touchstart','keydown'].forEach(function(evt){document.addEventListener(evt,startTimers,{passive:true});});
  startTimers();
})();
