
let listFunction = {
  isEmpty(str) {
    return (!str || 0 === str.length || str === undefined || str === '0000-00-00');
  },
  ucFirst: str => str ? str[0].toUpperCase() + str.slice(1) : str,
  openLink(link){
    window.open(link,'_blank');
  },
  copyText(link) {
    console.log(link)
    const textArea = document.createElement("textarea");
    textArea.value = link
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
      document.execCommand('copy');
      alert('Link berhasil di copy: ' + link)
    } catch (err) {
      alert('Unable to copy to clipboard', err);
    }
    document.body.removeChild(textArea);
  },
  getFileType(url) {
      // Remove query parameters and hash
    const cleanUrl = url.split('#')[0];
    // Get the last part after the last dot
    const extension = cleanUrl.split('.').pop().toLowerCase();
    return extension
  }
}

export { listFunction };

export default {
  install: (app) => {
    let keys = Object.keys(listFunction)
    for (var i = 0; i < keys.length; i++) {
      let ind = keys[i]
      app.config.globalProperties[ind] = listFunction[ind]
    }
    app.config.globalProperties.runFunction = (_func, data, options = []) => {
      let listFunction = app.config.globalProperties
      // console.log(listFunction, _func)
      if (listFunction.isEmpty(_func)) {
        if (listFunction.isEmpty(options))
          return data
        else {
          for (let index = 0; index < options.length; index++) {
            const el = options[index];
            if (el.value == data)
              return el.label
          }
        }
      } else { 
        return ( typeof _func == 'string' ? listFunction[_func](data) : _func(data) )
      }
      return data
    }
  }
}