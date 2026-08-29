const angka = [
  '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
  'sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas',
  'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'
];

const units = ['', 'ribu', 'juta', 'miliar', 'triliun'];

/**
 * Internal logic untuk konversi ratusan
 */
const convertHundreds = (num) => {
  if (num === 0) return '';

  if (num < 20) {
    return angka[num];
  } else if (num < 100) {
    const puluhan = Math.floor(num / 10);
    const satuan = num % 10;
    return angka[puluhan + 18] + (satuan > 0 ? ' ' + angka[satuan] : '');
  } else {
    const ratusan = Math.floor(num / 100);
    const sisa = num % 100;
    const prefix = ratusan === 1 ? 'seratus' : angka[ratusan] + ' ratus';
    return prefix + (sisa > 0 ? ' ' + convertHundreds(sisa) : '');
  }
};

/**
 * Exports
 */

export const toNumber = (number, decimal = ',') => {
  if (number == null || number === undefined) return 0;
  console.log('number', number)
  if (typeof number === 'string') {
    number = number.replace(new RegExp(`[^0-9${decimal}-]+`, 'g'), ''); // Mendukung minus dan titik desimal
  }
  console.log('number', number)
  if (number === '' || isNaN(number)) return 0;
  console.log(parseFloat(number))
  return Math.round(parseFloat(number));
};

export const toIDR = (number) => {
  if (number == null || number === undefined) return 'Rp 0,00';
  const val = Math.round(number);
  return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ",00";
};

export const setCurrency = (number) => {
  const val = toNumber(number,',');
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

export const range = (size, startAt = 0) => {
  console.log(size, startAt)
  return [...Array(size).keys()].map(i => i + startAt);
};

export const numberToWords = (num) => {
  if (num === 0) return 'nol';
  
  let words = '';
  let unitIndex = 0;
  let n = Math.abs(num);

  while (n > 0) {
    const part = n % 1000;
    if (part > 0) {
      let partWords = convertHundreds(part);
      
      // Khusus untuk "seribu" bukan "satu ribu"
      if (unitIndex === 1 && part === 1) {
        words = 'seribu ' + words;
      } else {
        words = partWords + ' ' + units[unitIndex] + ' ' + words;
      }
    }
    n = Math.floor(n / 1000);
    unitIndex++;
  }

  return (num < 0 ? 'minus ' : '') + words.trim();
};

export const checkMinMax = (val, min, max) => {
  const v = parseFloat(val);
  const mn = parseFloat(min);
  const mx = parseFloat(max);
  if (v < mn) return mn;
  if (v > mx) return mx;
  return v;
};

export const rounding = (val, decimalPlaces = 0) => {
  const multiplier = Math.pow(10, decimalPlaces);
  return Math.round(parseFloat(val) * multiplier) / multiplier;
};