/* ============================================================
   Form validation + submission (branding enquiry / contact)
   ============================================================ */
(function(){
  const T=window.THREADS;
  const OWNER='customerservice@threadsembroideryprint.co.uk';

  function firstError(err){
    const errors = err?.data?.errors;
    if (errors && typeof errors === 'object') {
      const first = Object.values(errors)[0];
      if (Array.isArray(first) && first[0]) return first[0];
    }
    return err?.data?.message || err?.message || 'Something went wrong.';
  }

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

  function showSuccess(card, name, reference, portalUrl){
    const first=(name||'there').split(' ')[0];
    card.innerHTML=`<div class="text-center" style="padding:1.5rem 0">
      <div style="width:72px;height:72px;border-radius:50%;background:var(--brand-soft);color:var(--brand);display:grid;place-items:center;font-size:2.2rem;margin:0 auto 1.2rem"><i class="bi bi-check-lg"></i></div>
      <h3 class="h-3">Thanks, ${first} — enquiry received.</h3>
      ${reference ? `<p class="muted mt-2">Reference: <b style="color:var(--text)">${reference}</b></p>` : ''}
      <p class="muted mt-2" style="max-width:44ch;margin-inline:auto">Our team will review your details and reply within one working day.</p>
      <div class="hero-cta mt-3" style="justify-content:center;display:flex;gap:12px;flex-wrap:wrap">
        ${portalUrl ? `<a class="btn" href="${portalUrl}"><i class="bi bi-person-circle"></i> View in Portal</a>` : ''}
        <a class="btn ${portalUrl?'btn--ghost':''}" href="/"><i class="bi bi-house"></i> Back to Home</a>
      </div>
    </div>`;
    window.scrollTo({top:card.getBoundingClientRect().top+window.scrollY-120,behavior:'smooth'});
  }

  function wireFileInput(form){
    const uz=form.querySelector('.upload-zone');
    const fi=form.querySelector('input[type=file]');
    const fl=form.querySelector('[data-file-list]');
    if(!uz||!fi) return { getFiles: ()=>[] };

    let selected=[];

    const syncInput=()=>{
      const dt=new DataTransfer();
      selected.forEach(f=>dt.items.add(f));
      fi.files=dt.files;
      if(!fl) return;
      fl.innerHTML=selected.map((f,i)=>`
        <div class="pill" style="margin:.3rem .3rem 0 0;display:inline-flex;align-items:center;gap:.35rem">
          <i class="bi bi-paperclip"></i> ${f.name}
          <button type="button" data-remove-file="${i}" aria-label="Remove" style="border:0;background:transparent;color:inherit;cursor:pointer;padding:0;line-height:1"><i class="bi bi-x"></i></button>
        </div>`).join('');
      fl.querySelectorAll('[data-remove-file]').forEach(btn=>{
        btn.onclick=()=>{
          selected.splice(+btn.dataset.removeFile,1);
          syncInput();
        };
      });
    };

    const addFiles=(list)=>{
      Array.from(list||[]).forEach(file=>{
        if(selected.length>=10) return;
        if(file.size>10*1024*1024){ T.toast(`${file.name}: max 10MB.`); return; }
        if(!/\.(png|jpe?g|svg|pdf)$/i.test(file.name)){ T.toast(`${file.name}: unsupported type.`); return; }
        const exists=selected.some(f=>f.name===file.name && f.size===file.size);
        if(!exists) selected.push(file);
      });
      syncInput();
    };

    uz.onclick=()=>fi.click();
    uz.ondragover=e=>{e.preventDefault();uz.classList.add('drag');};
    uz.ondragleave=()=>uz.classList.remove('drag');
    uz.ondrop=e=>{e.preventDefault();uz.classList.remove('drag');addFiles(e.dataTransfer?.files);};
    fi.onchange=()=>{ addFiles(fi.files); };

    return { getFiles: ()=>selected };
  }

  function wireBrandingForm(form){
    const filesApi=wireFileInput(form);

    form.querySelectorAll('.field-group').forEach(fg=>{
      const input=fg.querySelector('.input,.textarea,.select');
      if(input){
        input.addEventListener('blur',()=>validateField(fg));
        input.addEventListener('input',()=>{ if(fg.classList.contains('invalid')) validateField(fg); });
      }
    });

    form.addEventListener('submit', async e=>{
      e.preventDefault();
      let firstBad=null;
      form.querySelectorAll('.field-group').forEach(fg=>{
        if(!validateField(fg) && !firstBad) firstBad=fg;
      });
      if(firstBad){
        firstBad.scrollIntoView({block:'center',behavior:'smooth'});
        firstBad.querySelector('.input,.textarea,.select')?.focus();
        T.toast('Please check the highlighted fields.');
        return;
      }

      const btn=form.querySelector('[type=submit]');
      if(btn){ btn.disabled=true; btn.innerHTML='<i class="bi bi-hourglass-split"></i> Sending…'; }

      const fd=new FormData(form);
      // Ensure file list from our managed selection (handles drag/drop)
      fd.delete('files[]');
      filesApi.getFiles().forEach(file=>fd.append('files[]', file));

      try{
        const res=await T.api.send('/api/branding-enquiries', { method:'POST', formData:fd });
        T.toast(res.message || 'Enquiry submitted.', true);
        const card=form.closest('.form-card')||form;
        showSuccess(card, form.name?.value, res.reference, res.portal_url);
      }catch(err){
        T.toast(firstError(err));
        if(btn){ btn.disabled=false; btn.innerHTML='<i class="bi bi-send"></i> Send Enquiry'; }
      }
    });
  }

  function wireContactForm(form){
    wireFileInput(form);
    form.querySelectorAll('.field-group').forEach(fg=>{
      const input=fg.querySelector('.input,.textarea,.select');
      if(input){
        input.addEventListener('blur',()=>validateField(fg));
        input.addEventListener('input',()=>{ if(fg.classList.contains('invalid')) validateField(fg); });
      }
    });

    form.addEventListener('submit', e=>{
      e.preventDefault();
      let firstBad=null;
      form.querySelectorAll('.field-group').forEach(fg=>{ if(!validateField(fg)&&!firstBad) firstBad=fg; });
      if(firstBad){
        firstBad.scrollIntoView({block:'center',behavior:'smooth'});
        firstBad.querySelector('.input,.textarea,.select')?.focus();
        T.toast('Please check the highlighted fields.');
        return;
      }

      const data={};
      new FormData(form).forEach((v,k)=>{ data[k]=data[k]?data[k]+', '+v:v; });
      const card=form.closest('.form-card')||form;
      const name=(data.name||'there').split(' ')[0];
      const body=encodeURIComponent(Object.entries(data).map(([k,v])=>`${k}: ${v}`).join('\n'));
      const subject=encodeURIComponent('Website enquiry — '+(data.name||''));
      card.innerHTML=`<div class="text-center" style="padding:1.5rem 0">
        <div style="width:72px;height:72px;border-radius:50%;background:var(--brand-soft);color:var(--brand);display:grid;place-items:center;font-size:2.2rem;margin:0 auto 1.2rem"><i class="bi bi-check-lg"></i></div>
        <h3 class="h-3">Thanks, ${name} — message ready.</h3>
        <p class="muted mt-2" style="max-width:44ch;margin-inline:auto">Use the button below to send via your email client, or call us directly.</p>
        <div class="hero-cta mt-3" style="justify-content:center;display:flex;gap:12px;flex-wrap:wrap">
          <a class="btn" href="mailto:${OWNER}?subject=${subject}&body=${body}"><i class="bi bi-envelope"></i> Send via your email</a>
          <a class="btn btn--ghost" href="/">Back to Home</a>
        </div>
      </div>`;
      T.toast('Message prepared.',true);
    });
  }

  T.wireForms=function(){
    document.querySelectorAll('form[data-enquiry]').forEach(form=>{
      const type=form.getAttribute('data-enquiry')||'contact';
      if(type==='branding') wireBrandingForm(form);
      else wireContactForm(form);
    });
  };
})();
