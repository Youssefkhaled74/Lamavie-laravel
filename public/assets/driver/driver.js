(function(){
  // Sidebar toggle (mobile)
  const toggle = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('driverSidebar');
  if(toggle && sidebar){
    toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  }

  // Language toggle (UI only)
  const key = 'lamavie_driver_locale';
  const btn = document.getElementById('langToggle');

  function applyLocale(loc){
    document.body.classList.remove('locale-en','locale-ar');
    document.body.classList.add(loc === 'ar' ? 'locale-ar' : 'locale-en');
    document.documentElement.setAttribute('dir', loc === 'ar' ? 'rtl' : 'ltr');
    document.documentElement.setAttribute('lang', loc === 'ar' ? 'ar' : 'en');
    try{ localStorage.setItem(key, loc); }catch(e){}
    if(btn) btn.textContent = loc === 'ar' ? 'AR / EN' : 'EN / AR';
  }

  let current = 'en';
  try{ current = localStorage.getItem(key) || 'en'; }catch(e){}
  applyLocale(current);

  if(btn){
    btn.addEventListener('click', () => {
      current = current === 'ar' ? 'en' : 'ar';
      applyLocale(current);
    });
  }

  // Toast helper
  window.driverToast = function(message, type='success'){
    const stack = document.getElementById('toastStack');
    if(!stack) return alert(message);
    // avoid noisy duplicates (common when multiple listeners exist)
    const now = Date.now();
    const last = window.__driverToastLast || { msg:'', at:0, type:'' };
    if(last.msg === String(message) && last.type === type && (now - last.at) < 800) return;
    window.__driverToastLast = { msg:String(message), at:now, type };
    const el = document.createElement('div');
    el.className = 'toastx ' + (type === 'error' ? 'error' : 'success');
    el.textContent = message;
    stack.appendChild(el);
    setTimeout(() => el.remove(), 3200);
  };

  // Ajax forms: class="ajax-action" (expects JSON {success,message,booking})
  window.bindAjaxActions = function(){
    document.querySelectorAll('form.ajax-action').forEach(form => {
      if(form.dataset.ajaxBound === '1') return;
      form.dataset.ajaxBound = '1';

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const action = form.action;
        const data = new FormData(form);
        const submitButtons = Array.from(form.querySelectorAll('button[type="submit"], button:not([type])'));
        submitButtons.forEach(b => { b.disabled = true; b.dataset._oldHtml = b.innerHTML; b.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + b.innerHTML; });
        form.setAttribute('aria-busy', 'true');

        try{
          const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
          const res = await fetch(action, {
            method: 'POST',
            headers: {
              'X-Requested-With':'XMLHttpRequest',
              'Accept': 'application/json',
              ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
            },
            body: data,
            credentials: 'same-origin'
          });

          const contentType = (res.headers.get('content-type') || '').toLowerCase();

          // Laravel controllers sometimes redirect (HTML) even if the action succeeded.
          // Treat non-JSON successful responses as success and refresh/navigate.
          if(!contentType.includes('application/json')){
            if(res.redirected && res.url){
              window.location.href = res.url;
              return;
            }

            if(res.ok){
              driverToast('Updated', 'success');
              window.location.reload();
              return;
            }

            if(res.status === 401 || res.status === 419){
              driverToast('Session expired. Refresh the page.', 'error');
              return;
            }

            driverToast(`Request failed (${res.status})`, 'error');
            return;
          }

          let json = null;
          try{
            json = await res.json();
          }catch(parseErr){
            if(res.ok){
              driverToast('Updated', 'success');
              window.location.reload();
              return;
            }
            driverToast('Unexpected server response', 'error');
            return;
          }

          const isSuccess = (json?.success === undefined) ? res.ok : !!json?.success;
          if(isSuccess){
            driverToast(json?.message || 'Updated', 'success');
            if(typeof window.onDriverAjaxSuccess === 'function'){
              window.onDriverAjaxSuccess(json);
            }
          }else{
            driverToast(json?.message || `Action failed (${res.status})`, 'error');
          }
        }catch(err){
          driverToast('Network error. Check your connection.', 'error');
        }finally{
          form.removeAttribute('aria-busy');
          submitButtons.forEach(b => {
            b.disabled = false;
            if(b.dataset._oldHtml){
              b.innerHTML = b.dataset._oldHtml;
              delete b.dataset._oldHtml;
            }
          });
        }
      });
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    window.bindAjaxActions();
  });
})();
