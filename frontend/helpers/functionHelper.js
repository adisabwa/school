// src/helpers/listHelper.js
export const isEmpty = (str) => {
  return (!str || 0 === str.length || str === undefined || str === '0000-00-00' || str === 'null');
};

export const ucFirst = (str) => str ? str[0].toUpperCase() + str.slice(1) : str;

export const capitalizeEachWord = (str) => {
  if (typeof str !== 'string') return '';
  return str.split(' ').map(word => {
    if (word.length === 0) return '';
    return word.charAt(0).toUpperCase() + word.slice(1);
  }).join(' ');
};

export const getFileType = (url) => {
  const cleanUrl = url.split('#')[0];
  return cleanUrl.split('.').pop().toLowerCase();
};

export const objectToQueryParams = (obj, prefix = '') => {
  const query = Object.entries(obj).flatMap(([key, value]) => {
    const paramKey = prefix ? `${prefix}[${encodeURIComponent(key)}]` : encodeURIComponent(key);
    if (value === null || value === undefined) return [];
    if (typeof value === 'object' && !Array.isArray(value)) return objectToQueryParams(value, paramKey);
    if (Array.isArray(value)) {
      return value.flatMap((val, i) => {
        return typeof val === 'object' 
          ? objectToQueryParams(val, `${paramKey}[${i}]`) 
          : `${paramKey}[]=${encodeURIComponent(val)}`;
      });
    }
    return `${paramKey}=${encodeURIComponent(value)}`;
  });
  return query.join('&');
};

export const isSimilar = (s1, s2, caseSensitive = false) => {
  if (!caseSensitive) { s1 = s1.toLowerCase(); s2 = s2.toLowerCase(); }
  if (s1 == s2) return 1.0;
  let len1 = s1.length, len2 = s2.length;
  if (len1 == 0 || len2 == 0) return 0.0;

  let max_dist = Math.floor(Math.max(len1, len2) / 2) - 1;
  let match = 0;
  let hash_s1 = new Array(len1).fill(0);
  let hash_s2 = new Array(len2).fill(0);

  for (let i = 0; i < len1; i++) {
    for (let j = Math.max(0, i - max_dist); j < Math.min(len2, i + max_dist + 1); j++) {
      if (s1[i] == s2[j] && hash_s2[j] == 0) {
        hash_s1[i] = 1; hash_s2[j] = 1; match++; break;
      }
    }
  }
  if (match == 0) return 0.0;
  let t = 0, point = 0;
  for (let i = 0; i < len1; i++) {
    if (hash_s1[i] == 1) {
      while (hash_s2[point] == 0) point++;
      if (s1[i] != s2[point++]) t++;
    }
  }
  t /= 2;
  let jaro = (match/len1 + match/len2 + (match-t)/match) / 3.0;
  if (jaro > 0.7) {
    let prefix = 0;
    for (let i = 0; i < Math.min(len1, len2); i++) {
      if (s1[i] == s2[i]) prefix++; else break;
    }
    jaro += 0.1 * Math.min(4, prefix) * (1 - jaro);
  }
  return jaro.toFixed(6);
};

export const getValueFromOption = (label, options, sim_rate = 0.7) => {
  const opts = typeof options === 'object' ? Object.values(options) : options;
  let similarities = opts.map(el => {
    let text2 = el?.match ?? el?.label ?? '';
    return isSimilar(label.trim(), text2.trim());
  });
  let maxSim = Math.max(...similarities);
  if (maxSim > sim_rate) {
    return opts[similarities.findIndex(d => d == maxSim)].value;
  }
  return false;
};

// 1. Gather all files in the directories (* matches any filename)
const helperModules = import.meta.glob('@/helpers/*.js', { eager: true });
const composableModules = import.meta.glob('@/composables/*.js', { eager: true });

/**
 * Scans a folder's modules and extracts all exported functions
 */
function extractFunctions(modules, isComposableFolder = false) {
  const functionRegistry = {};

  for (const path in modules) {
    const fileExports = modules[path];

    for (const exportName in fileExports) {
      const exportedItem = fileExports[exportName];

      // If it's a helper folder, grab all pure utility functions
      if (!isComposableFolder && typeof exportedItem === 'function') {
        functionRegistry[exportName] = exportedItem;
      } 
      
      // If it's a composable folder, look for the 'use...' factory functions
      if (isComposableFolder && exportName.startsWith('use') && typeof exportedItem === 'function') {
        functionRegistry[exportName] = exportedItem;
      }
    }
  }
  return functionRegistry;
}
const allHelpers = extractFunctions(helperModules, false);
const allComposables = extractFunctions(composableModules, true);

export const runFunction = ({func, data, options = [], defaultData = ''}) => {
  // console.log('run-func', func)
  if (typeof options == 'object')
    options = Object.values(options)
  // console.log(data, options)
  try {
    // console.log('try', func)
    if (allHelpers[func]) {
      return allHelpers[func](data);
    }

    // Scenario B: It's a stateful composable factory (e.g., 'useCounter')
    if (allComposables[func]) {
      // Note: This must be called within a Vue component's setup context!
      return allComposables[func](data);
    }
    throw new Error('Function not found');
  }
  catch (error){
    // console.log('catch', error)
    if (isEmpty(options))
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
  }
  // console.log(data, defaultData)
  if (defaultData)
    return defaultData
  else
    return data
}