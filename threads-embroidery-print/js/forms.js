/* ============================================================
   Form validation + demo submission (enquiry / contact)
   Emails owner on launch; here it validates + confirms + offers
   a mailto fallback so nothing is lost.
   ============================================================ */
(function(){
  const T=window.THREADS;
  const OWNER='customerservice@threadsembroideryprint.co.uk';

  function validateField(fg){
    const input=fg.querySelector('.input,.textarea,.select');
    if(!input) return true;
    let ok=true, val=(input.value||'').trim();
    if(input.required && !val) ok=false;
    if(ok && input.type==='email' && val && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(val)) ok=false;
    if(ok && input.type==='tel' && val && !/[0-9]{6,}/.test(val.replace(/\s/g,''))) ok=false;
    fg.classList.toggle('invalid',!ok);
    return ok;
  }

  function wireForm(form){
    if(!form) return;
    // inline validation on blur
    form.querySelectorAll('.field-group').forEach(fg=>{
      const input=fg.querySelector('.input,.textarea,.select');
      if(input){ input.addEventListener('blur',()=>validateField(fg));
        input.addEventListener('input',()=>{ if(fg.classList.contains('invalid')) validateField(fg); }); }
    });
    // file upload
    const uz=form.querySelector('.upload-zone'), fi=form.querySelector('input[type=file]'), fl=form.querySelector('[data-file-list]');
    if(uz&&fi){
      uz.onclick=()=>fi.click();
      uz.ondragover=e=>{e.preventDefault();uz.classList.add('drag');};
      uz.ondragleave=()=>uz.classList.remove('drag');
      uz.ondrop=e=>{e.preventDefault();uz.classList.remove('drag');fi.files=e.dataTransfer.files;showFiles();};
      fi.onchange=showFiles;
      function showFiles(){ if(!fl)return; fl.innerHTML=[...fi.files].map(f=>`<div class="pill" style="margin:.3rem .3rem 0 0"><i class="bi bi-paperclip"></i> ${f.name}</div>`).join(''); }
    }
    form.addEventListener('submit',e=>{
      e.preventDefault();
      let firstBad=null;
      form.querySelectorAll('.field-group').forEach(fg=>{ if(!validateField(fg)&&!firstBad) firstBad=fg; });
      if(firstBad){ firstBad.scrollIntoView({block:'center',behavior:'smooth'}); firstBad.querySelector('.input,.textarea,.select').focus(); T.toast('Please check the highlighted fields.'); return; }
      // build data
      const data={}; new FormData(form).forEach((v,k)=>{ data[k]=data[k]?data[k]+', '+v:v; });
      // success state
      const card=form.closest('.form-card')||form;
      const name=(data.name||'there').split(' ')[0];
      const body=encodeURIComponent(Object.entries(data).map(([k,v])=>`${k}: ${v}`).join('\n'));
      const subject=encodeURIComponent((data.enquiry_type?('Enquiry: '+data.enquiry_type):'Website enquiry')+' — '+(data.name||''));
      card.innerHTML=`<div class="text-center" style="padding:1.5rem 0">
        <div style="width:72px;height:72px;border-radius:50%;background:var(--brand-soft);color:var(--brand);display:grid;place-items:center;font-size:2.2rem;margin:0 auto 1.2rem"><i class="bi bi-check-lg"></i></div>
        <h3 class="h-3">Thanks, ${name} — enquiry received.</h3>
        <p class="muted mt-2" style="max-width:44ch;margin-inline:auto">Our team will review your details and reply within one working day. A copy has been prepared for <b style="color:var(--text)">${OWNER}</b>.</p>
        <div class="hero-cta mt-3" style="justify-content:center;display:flex;gap:12px;flex-wrap:wrap">
          <a class="btn" href="mailto:${OWNER}?subject=${subject}&body=${body}"><i class="bi bi-envelope"></i> Send via your email</a>
          <a class="btn btn--ghost" href="index.php">Back to Home</a>
        </div>
        <p class="help mt-3"><i class="bi bi-info-circle"></i> Live email delivery activates when the site goes live on your domain.</p>
      </div>`;
      T.toast('Enquiry submitted.',true);
      window.scrollTo({top:card.getBoundingClientRect().top+window.scrollY-120,behavior:'smooth'});
    });
  }

  T.wireForms=function(){ document.querySelectorAll('form[data-enquiry]').forEach(wireForm); };
})();
