import { isArray } from "lodash";

let listFunction = {
  isEmpty(str) {
    return (!str || 0 === str.length || str === undefined || str === '0000-00-00' || str === 'null');
  },
  ucFirst: str => str ? str[0].toUpperCase() + str.slice(1) : str,
  capitalizeEachWord(str) {
    if (typeof str !== 'string') {
        return ''; // Or throw an error, depending on desired behavior
    }
    return str.split(' ').map(word => {
        if (word.length === 0) return '';
        return word.charAt(0).toUpperCase() + word.slice(1);
    }).join(' ');
  },
  openLink(link){
    window.open(link,'_blank');
  },
  openPost(url, params = {}) {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = url;
    form.target = "_blank";

    // Add CSRF token if needed (CI4)
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
          // jika value adalah array → buat multiple input
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
    form.remove();
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
  },
  objectToQueryParams(obj, prefix = '') {
    const query = Object.entries(obj).flatMap(([key, value]) => {
      const paramKey = prefix ? `${prefix}[${encodeURIComponent(key)}]` : encodeURIComponent(key);

      if (value === null || value === undefined) {
        return [];
      } else if (typeof value === 'object' && !Array.isArray(value)) {
        return objectToQueryParams(value, paramKey);
      } else if (Array.isArray(value)) {
        return value.flatMap((val, i) => {
          if (typeof val === 'object') {
            return objectToQueryParams(val, `${paramKey}[${i}]`);
          } else {
            return `${paramKey}[]=${encodeURIComponent(val)}`;
          }
        });
      } else {
        return `${paramKey}=${encodeURIComponent(value)}`;
      }
    });

    return query.join('&');
  },
  getValueFromOption(label, options, sim_rate = 0.7){
    if (typeof options == 'object') {
      options = Object.values(options)
    }
    // console.log(options)
    //get similar value
    let sim = []
    for (let i = 0; i < options.length; i++) {
      const element = options[i];
      let text2 = element?.match ?? element?.label ?? ''
      let text1 = label
      // console.log(element)
      sim[i] = this.isSimilar(text1.trim().toLowerCase(), text2.trim().toLowerCase())
      // console.log(label, text, sim[i])
      // let sim = 0    
    }

    let _sim = Math.max(...sim)
    // console.log(label, _sim)
    if (_sim > sim_rate) {
      let ind = sim.findIndex(d => d == _sim)
      // console.log(ind)
      let element = options[ind]
      // console.log(element)
      return element.value   
    }
    return false
  },
  isSimilar(s1, s2, caseSensitive = false){
    if (!caseSensitive) {
      s1 = s1.toLowerCase()
      s2 = s2.toLowerCase()
    }
    // If the strings are equal
    if (s1 == s2)
      return 1.0;

    // Length of two strings
    let len1 = s1.length, len2 = s2.length;

    if (len1 == 0 || len2 == 0)
        return 0.0;

    // Maximum distance upto which matching
    // is allowed
    let max_length = Math.max(len1, len2)
    let max_dist = Math.floor(max_length / 2) - 1;

    // Count of matches
    let match = 0;

    // Hash for matches
    let hash_s1 = new Array(s1.length);
    hash_s1.fill(0);
    let hash_s2 = new Array(s2.length);
    hash_s2.fill(0);

    // Traverse through the first string
    for (let i = 0; i < len1; i++)
    {
        // Check if there is any matches
        for (let j = Math.max(0, i - max_dist);
            j < Math.min(len2, i + max_dist + 1); j++)
            // If there is a match
            if (s1[i] == s2[j] &&
                hash_s2[j] == 0)
            {
                hash_s1[i] = 1;
                hash_s2[j] = 1;
                match++;
                break;
            }
    }

    // If there is no match
    if (match == 0)
        return 0.0;

    // Number of transpositions
    let t = 0;

    let point = 0;

    // Count number of occurrences
    // where two characters match but
    // there is a third matched character
    // in between the indices
    for (let i = 0; i < len1; i++)
        if (hash_s1[i] == 1)
        {

            // Find the next matched character
            // in second string
            while (hash_s2[point] == 0)
                point++;

            if (s1[i] != s2[point++])
                t++;
        }
    t /= 2;

    // Return the Jaro Similarity
    let jaro_dist = ((match) / (len1)
            + (match) / (len2)
            + (match - t) / (match))
        / 3.0;

    // If the jaro Similarity is above a threshold
    if (jaro_dist > 0.7)
      {
          // Find the length of common prefix
          let prefix = 0;
   
          for (let i = 0; i < Math.min(s1.length,s2.length); i++)
          {
               
              // If the characters match
              if (s1[i] == s2[i])
                  prefix++;
   
              // Else break
              else
                  break;
          }
   
          // Maximum of 4 characters are allowed in prefix
          prefix = Math.min(4, prefix);
   
          // Calculate jaro winkler Similarity
          jaro_dist += 0.1 * prefix * (1 - jaro_dist);
      }
      return jaro_dist.toFixed(6);
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
    app.config.globalProperties.runFunction = ({func, data, options = [], defaultData = ''}) => {
      let listFunction = app.config.globalProperties
      if (typeof options == 'object')
        options = Object.values(options)
      // console.log(data, options, listFunction.isEmpty(options))
      if (listFunction.isEmpty(func)) {
        if (listFunction.isEmpty(options))
          return data
        else {
          for (let index = 0; index < options.length; index++) {
            const el = options[index];
            if (Array.isArray(el.options)) {
              for (let j = 0; j < el.options.length; j++) {
                const element = el.options[j];
                // console.log(element.value, data)
                if (element.value == data)
                  return element.label
              }
            } else if (el.value == data)
              return el.label
          }
        }
      } else { 
        return ( typeof func == 'string' ? listFunction[func](data) : func(data) )
      }
      // console.log(data, defaultData)
      if (defaultData)
        return defaultData
      else
        return data
    }
  }
}