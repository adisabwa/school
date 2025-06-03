import { type } from "jquery";
import { isArray } from "lodash";
import { ref, watch } from 'vue';

let listFunction = {
    fillObjectValue(src, data) {
      if (!this.isArrayOrObject(data))
        return;
      if (data === null || data == undefined)
        return;
      // console.log('source', src)
      // console.log('data', data)
      Object.keys(data).forEach(key => {
        // key = key + '.coba'
        if (this.getObjectValueByPath(src, key) !== undefined) {
          // src[key] = data[key];
          // console.log(key, data[key])
          let res = this.setObjectValueByPath(src, key, data[key])
          // console.log('res', res, src)
        }
      });
      // console.log(src)
      return src;
    },
    fillAndAddObjectValue(src, data) {
      if (!this.isArrayOrObject(data))
        return;
      if (data === null || data == undefined)
        return;
      let adds = []
      // console.log(src)
      Object.keys(data).forEach(key => {
        let res = this.setObjectValueByPath(src, key, data[key])
      });
      return src;
    },
    isArrayOrObject(val){
      return typeof val == 'object' || Array.isArray(val);
    },
    resetObjectValue(src, exception = []) {
      if (!this.isArrayOrObject(src))
        return;
      if (src === null || src == undefined)
        return;
      Object.keys(src).forEach(key => {
        if (!exception.includes(key)) {
          if (this.isArrayOrObject(src[key])) {
            this.resetObjectValue(src[key], exception)
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
        return obj.map(item => this.convertNullToEmptyString(item));
      } else if (obj !== null && typeof obj === 'object') {
        const result = {};
        for (const key in obj) {
          result[key] = this.convertNullToEmptyString(obj[key]);
        }
        return result;
      } else {
        return obj === null ? '' : obj;
      }
    },
    saveToStorage(index, value){
      let old_data = this.getDataFormStorage(index) ?? []
      localStorage.setItem(index,JSON.stringify([...new Set([...old_data, ...[value]])]))
      console.log('save',this.getDataFormStorage(index))
    },
    removeFromStorage(index, value){
      let old_data = this.getDataFormStorage(index) ?? []
      let new_data = old_data.filter(d => d !== value)
      localStorage.setItem(index,JSON.stringify(new_data))
      console.log('save',this.getDataFormStorage(index))
    },
    resetStorage(index){
      return localStorage.setItem(index, null)
    },
    getDataFormStorage(index){
      let data = localStorage.getItem(index)
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