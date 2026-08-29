// src/composables/useBrowserActions.js
export const useBrowserActions = () => {
  const openLink = (link) => {
    window.open(link, '_blank');
  };

  const openPost = (url, params = {}, target = '') => {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = url;
    form.target = target;

    const csrf = document.querySelector("meta[name='csrf-token']");
    if (csrf) {
      const tokenField = document.createElement("input");
      tokenField.type = "hidden";
      tokenField.name = csrf.getAttribute("data-name");
      tokenField.value = csrf.getAttribute("content");
      form.appendChild(tokenField);
    }

    for (let key in params) {
      if (Array.isArray(params[key])) {
        params[key].forEach(v => {
          const input = document.createElement("input");
          input.type = "hidden";
          input.name = key + "[]";
          input.value = v;
          form.appendChild(input);
        });
      } else {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = key;
        input.value = params[key];
        form.appendChild(input);
      }
    }
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
  };

  const copyText = (link) => {
    const textArea = document.createElement("textarea");
    textArea.value = link;
    document.body.appendChild(textArea);
    textArea.select();
    try {
      document.execCommand('copy');
      alert('Link berhasil di copy: ' + link);
    } catch (err) {
      alert('Unable to copy to clipboard');
    }
    document.body.removeChild(textArea);
  };

  return { openLink, openPost, copyText };
}