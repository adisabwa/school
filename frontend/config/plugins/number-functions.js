

const angka = [
  '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
  'sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas',
  'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'
];
const units = ['', 'ribu', 'juta', 'miliar', 'triliun'];

function convertHundreds(num) {

  if (num < 20) {
    return angka[num];
  } else if (num < 100) {
    const puluhan = Math.floor(num / 10);
    const satuan = num % 10;
    return angka[puluhan + 18] + (satuan > 0 ? ' ' + angka[satuan] : '');
  } else {
    const ratusan = Math.floor(num / 100);
    const sisa = num % 100;
    return angka[ratusan] + ' ratus' + (sisa > 0 ? ' ' + convertHundreds(sisa) : '');
  }
}

let listFunction = {
  toIDR: function(number) {
    if (number == null || number == undefined) return 'Rp. 0,00';
    number = Math.round(number);
    return "Rp " + number.toString().replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1\.") + ",00";
  },
  setCurrency: function(number) {
    number = listFunction.toNumber(number)
    return number.toString().replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1\.");
  },
  toNumber: function(number){
    if (number == null || number == undefined) return 0;
    console.log(number, typeof number)
    if (typeof number == 'string') 
      number = number.replace(/[^0-9]+/g, '');

    if (number == '') return 0
    console.log(number)
    number = parseInt(number)
    console.log(number)
    number = Math.round(number)

    return number
  },
  range(size, startAt = 0) {
    return [...Array(size).keys()].map(i => i + startAt);
  },
  numberToWords(num, pass = 0) {
    if (num === 0) {
      return 'nol';
    }
    let words = '';
    let unitIndex = 0;
    while (num > 0) {
      const part = num % 1000; // Ambil bagian ribuan
      if (num < 1000)
        words = num + ' ' + units[unitIndex] + ' ' + words;
      else if (part > 0) {
        words = convertHundreds(part) + ' ' + units[unitIndex] + ' ' + words;
      }
      num = Math.floor(num / 1000);
      unitIndex++;
    }

    // Hilangkan spasi ekstra di akhir
    return words.trim();
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