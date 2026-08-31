import { type } from "jquery";
import { isArray } from "lodash";
import { ref, watch } from 'vue';

let listFunction = {
    toQueryString(obj, prefix) {
      console.log(obj)
      const str = [];
      for (let key in obj) {
        if (obj.hasOwnProperty(key)) {
          const k = prefix ? prefix + "[" + key + "]" : key;
          const v = obj[key];
          str.push(
            (v !== null && typeof v === "object")
              ? listFunction.toQueryString(v, k)
              : encodeURIComponent(k) + "=" + encodeURIComponent(v)
          );
        }
      }
      return str.join("&");
    },
    fillObjectValue(src, data, exception = []) {
      if (!listFunction.isArrayOrObject(data))
        return;
      if (data === null || data == undefined)
        return;
      // console.log('source', src)
      // console.log('data', data)
      Object.keys(data).forEach(key => {
        if (!exception.includes(key)) {
          // key = key + '.coba'
          if (listFunction.getObjectValueByPath(src, key) !== undefined) {
            // src[key] = data[key];
            // console.log(key, data[key])
            let res = listFunction.setObjectValueByPath(src, key, data[key])
            // console.log('res', res, src)
          }
        }
      });
      // console.log(src)
      return src;
    },
    fillAndAddObjectValue(src, data) {
      if (!listFunction.isArrayOrObject(data))
        return;
      if (data === null || data == undefined)
        return;
      let adds = []
      // console.log(src)
      Object.keys(data).forEach(key => {
        let res = listFunction.setObjectValueByPath(src, key, data[key])
      });
      return src;
    },
    isArrayOrObject(val){
      return typeof val == 'object' || Array.isArray(val);
    },
    resetObjectValue(src, exception = []) {
      if (!listFunction.isArrayOrObject(src))
        return;
      if (src === null || src == undefined)
        return;
      Object.keys(src).forEach(key => {
        if (!exception.includes(key)) {
          if (listFunction.isArrayOrObject(src[key])) {
            listFunction.resetObjectValue(src[key], exception)
          } else
            src[key] = null; 
        }
      });
      // console.log(src)
      return src;
    },
    traverse(obj, callback, path = '') {
      Object.entries(obj).forEach(([key, value]) => {
        const fullPath = path ? `${path}.${key}` : key;
    
        if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
          listFunction.traverse(value, callback, fullPath); // recurse
        } else {
          callback(fullPath, value); // base case
        }
      });
    },
    flattenObject(obj, parentKey = '', result = {}) {
      for (const key in obj) {
        if (!obj.hasOwnProperty(key)) continue;
    
        const newKey = parentKey ? `${parentKey}.${key}` : key;
    
        if (typeof obj[key] === 'object' && obj[key] !== null && !Array.isArray(obj[key])) {
          listFunction.flattenObject(obj[key], newKey, result);
        } else {
          result[newKey] = obj[key];
        }
      }
      return result;
    },
    findPathbyValue(obj, target, path = []) {
      for (let key in obj) {
          if (obj.hasOwnProperty(key)) {
              let currentPath = [...path, key];
              
              // Check if the current value matches the target
              if (obj[key] === target) {
                  return currentPath.join('.');
              }
              
              // Recursively search nested objects or arrays
              if (typeof obj[key] === 'object' && obj[key] !== null) {
                  let result = listFunction.findPathbyValue(obj[key], target, currentPath);
                  if (result) return result;
              }
          }
      }
      return null;
    },
    getObjectValueByPath(obj, path) {
      path = path.split('.')
      return path.reduce((acc, key) => acc?.[key], obj);
    },
    setObjectValueByPath(obj, path, value) {
      const keys = path.split('.'); // Split the path into individual keys
      let current = obj;
      // console.log('setObjectValueByPath', keys, value)
      // Iterate over the keys to find the target location
      keys.forEach((key, index) => {
        // console.log(current, keys, key)
        if (index === keys.length - 1) {
          // If we're at the last key, set the value
          current[key] = value;
        } else {
          // If the key doesn't exist, create it (if it's not an array)
          if (current[key] === undefined) {
            current[key] = isNaN(keys[index + 1]) ? {} : [];
          }
          current = current[key];
        }
      });
    },
    coalesce(array) {
      let check = false;
      let value = null;
      array.forEach(a => {
        // console.log(a);
        if (!check && a !== null && a !== undefined) {
          value = a;
          check = true;
        }
      });
      return value;
    },
    setCookie(name, value, days) {
      let expires = "";
      if (days) {
          const date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          expires = "; expires=" + date.toUTCString();
      }
      value = JSON.stringify(value)
      // console.log(value)
      document.cookie = name + "=" + (value || "") + expires + "; path=/";
    },
    getCookie(name) {
      const nameEQ = name + "=";
      const ca = document.cookie.split(';');
      // console.log(ca)
      let value = null
      for (let i = 0; i < ca.length; i++) {
          let c = ca[i];
          while (c.charAt(0) === ' ') c = c.substring(1, c.length);
          if (c.indexOf(nameEQ) === 0) {
            value = c.substring(nameEQ.length, c.length);
            if (value != '')
              value = JSON.parse(value)
          }
      }
      return value;;
    },
    deleteCookie(name) {
      document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    },
    convertNullToEmptyString(obj) {
      if (Array.isArray(obj)) {
        return obj.map(item => listFunction.convertNullToEmptyString(item));
      } else if (obj !== null && typeof obj === 'object') {
        const result = {};
        for (const key in obj) {
          result[key] = listFunction.convertNullToEmptyString(obj[key]);
        }
        return result;
      } else {
        return obj === null ? '' : obj;
      }
    },
    saveToStorage(index, value){
      let old_data = listFunction.getDataFromStorage(index) ?? []
      console.log([...new Set([...[value], ...old_data])])
      localStorage.setItem(index,JSON.stringify([...new Set([...[value], ...old_data])]))
      // console.log('save',index,listFunction.getDataFromStorage(index))
    },
    removeFromStorage(index, value){
      let old_data = listFunction.getDataFromStorage(index) ?? []
      console.log('old_data', old_data)
      let new_data = old_data.filter(d => d !== value)
      localStorage.setItem(index,JSON.stringify(new_data))
      // console.log('remove',listFunction.getDataFromStorage(index))
    },
    resetStorage(index){
      localStorage.removeItem(index)
      // console.log('reset',listFunction.getDataFromStorage(index))
    },
    getDataFromStorage(index){
      let data = localStorage.getItem(index)
      // console.log('get-local', index, data)
      if (data)
        return JSON.parse(data)
      else
        return ''
    },
    useLocalStorage(index, defaultValue) {
      const storedValue = localStorage.getItem(index);
      const data = ref(storedValue ? JSON.parse(storedValue) : defaultValue);
    
      // Watch for changes and update localStorage
      watch(data, (newVal) => {
        localStorage.setItem(index, JSON.stringify(newVal));
      }, { deep: true });
    
      return data;
    },
    getMostFrequent(arr) {
      if (arr.length === 0) return null;
      const hashmap = arr.reduce((acc, val) => {
        acc[val] = (acc[val] || 0) + 1;
        return acc;
      }, {});

      return Object.keys(hashmap).reduce((a, b) => hashmap[a] > hashmap[b] ? a : b);
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
    }
  }