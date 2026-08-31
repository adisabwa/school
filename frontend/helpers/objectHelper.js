// src/helpers/objectHelper.js
export const isArrayOrObject = (val) => typeof val === 'object' || Array.isArray(val);

export const findPathbyValue = (obj, target, path = []) => {
  for (let key in obj) {
    if (obj.hasOwnProperty(key)) {
      let currentPath = [...path, key];
      
      // Jika value cocok
      if (obj[key] === target) {
        return currentPath.join('.');
      }
      
      // Rekursif untuk nested object/array
      if (typeof obj[key] === 'object' && obj[key] !== null) {
        let result = findPathbyValue(obj[key], target, currentPath);
        if (result) return result;
      }
    }
  }
  return null;
};

// Fungsi tambahan untuk sinkronisasi data yang tidak ada di file sebelumnya
export const fillAndAddObjectValue = (src, data) => {
  if (!isArrayOrObject(data) || data === null) return;
  Object.keys(data).forEach(key => {
    setObjectValueByPath(src, key, data[key]);
  });
  return src;
};

export const toQueryString = (obj, prefix) => {
  const str = [];
  for (let key in obj) {
    if (obj.hasOwnProperty(key)) {
      const k = prefix ? prefix + "[" + key + "]" : key;
      const v = obj[key];
      str.push(
        (v !== null && typeof v === "object")
          ? toQueryString(v, k)
          : encodeURIComponent(k) + "=" + encodeURIComponent(v)
      );
    }
  }
  return str.join("&");
};

export const getObjectValueByPath = (obj, path) => {
  const keys = path.split('.');
  return keys.reduce((acc, key) => acc?.[key], obj);
};

export const setObjectValueByPath = (obj, path, value) => {
  // console.log(obj, path, value)
  const keys = path.split('.');
  let current = obj;
  keys.forEach((key, index) => {
    if (index === keys.length - 1) {
      current[key] = value;
    } else {
      if (current[key] === undefined) {
        current[key] = isNaN(keys[index + 1]) ? {} : [];
      }
      current = current[key];
    }
  });
};

export const fillObjectValue = (src, data, exception = []) => {
  if (!isArrayOrObject(data) || data === null) return;
  Object.keys(data).forEach(key => {
    if (!exception.includes(key)) {
      if (getObjectValueByPath(src, key) !== undefined) {
        setObjectValueByPath(src, key, data[key]);
      }
    }
  });
  return src;
};

export const resetObjectValue = (src, exception = []) => {
  if (!isArrayOrObject(src) || src === null) return;
  Object.keys(src).forEach(key => {
    if (!exception.includes(key)) {
      if (isArrayOrObject(src[key])) {
        resetObjectValue(src[key], exception);
      } else {
        src[key] = null;
      }
    }
  });
  return src;
};

export const flattenObject = (obj, parentKey = '', result = {}) => {
  for (const key in obj) {
    if (!obj.hasOwnProperty(key)) continue;
    const newKey = parentKey ? `${parentKey}.${key}` : key;
    if (typeof obj[key] === 'object' && obj[key] !== null && !Array.isArray(obj[key])) {
      flattenObject(obj[key], newKey, result);
    } else {
      result[newKey] = obj[key];
    }
  }
  return result;
};

export const coalesce = (array) => {
  return array.find(a => a !== null && a !== undefined) ?? null;
};

export const convertNullToEmptyString = (obj) => {
  if (Array.isArray(obj)) {
    return obj.map(item => convertNullToEmptyString(item));
  } else if (obj !== null && typeof obj === 'object') {
    const result = {};
    for (const key in obj) {
      result[key] = convertNullToEmptyString(obj[key]);
    }
    return result;
  }
  return obj === null ? '' : obj;
};

export const getMostFrequent = (arr) => {
  if (arr.length === 0) return null;
  const hashmap = arr.reduce((acc, val) => {
    acc[val] = (acc[val] || 0) + 1;
    return acc;
  }, {});
  return Object.keys(hashmap).reduce((a, b) => hashmap[a] > hashmap[b] ? a : b);
};