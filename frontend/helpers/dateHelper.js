import dayjs from 'dayjs';
import 'dayjs/locale/id';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import isBetween from 'dayjs/plugin/isBetween';

// Inisialisasi Plugin
dayjs.extend(customParseFormat);
dayjs.extend(isBetween);
dayjs.locale('id');

export const getTime = (time, passZero = false) => {
  if (typeof time !== 'number') return false;
  let second = time % 60;
  let totalMinutes = Math.floor(time / 60);
  let minute = totalMinutes % 60;
  let totalHours = Math.floor(totalMinutes / 60);
  let hour = totalHours % 24;
  let totalDays = Math.floor(totalHours / 24);
  let day = totalDays % 30;
  let month = Math.floor(totalDays / 30);

  let res = {};
  let els = { month, day, hour, minute, second };
  let write = false;

  Object.keys(els).forEach((key) => {
    let el = els[key];
    if (el > 0) write = true;
    if (!passZero || write) {
      res[key] = {
        value: el < 10 ? '0' + el : el.toString(),
        label: key,
      };
    }
  });
  return res;
};

export const dateNow = () => dayjs().format('YYYY-MM-DD');

export const timeNow = () => dayjs().format('HH:mm');

export const dateData = (string) => {
  const formats = [
    'YYYY-MM-DD', 'DD-MM-YYYY', 'DD/MM/YYYY', 'MM/DD/YYYY',
    'DD MMMM YYYY', 'dddd, DD MMMM YYYY', 'MMMM DD, YYYY',
    'YYYY/MM/DD', 'DD.MM.YYYY'
  ];
  const date = dayjs(string, formats, true);
  return date.isValid() ? date.format('YYYY-MM-DD') : 'Invalid date';
};

export const formatDate = (date, format = 'YYYY-MM-DD') => dayjs(date).format(format);

export const dayIndo = (date) => dayjs(date).format('dddd');

export const dateIndo = (date) => dayjs(date).format('DD MMMM YYYY');

export const dateIndoRange = (date1, date2) => {
  const t1 = dateIndo(date1).trim();
  const t2 = dateIndo(date2).trim();
  if (!t1 || !t2) return '';
  if (t1 === t2) return t1;

  const d1 = t1.split(' ');
  const d2 = t2.split(' ');

  if (d1[2] === d2[2]) { // Tahun sama
    if (d1[1] === d2[1]) { // Bulan sama
      return `${d1[0]} - ${d2[0]} ${d1[1]} ${d1[2]}`;
    }
    return `${d1[0]} ${d1[1]} - ${d2[0]} ${d2[1]} ${d1[2]}`;
  }
  return `${t1} - ${t2}`;
};

export const dateDayIndoSeparate = (date) => {
  const d = dayjs(date);
  return {
    'long-day': d.format('dddd'),
    'day': d.format('DD'),
    'short-month': d.format('MMM'),
    'month': d.format('MM'),
    'long-month': d.format('MMMM'),
    'year': d.format('YYYY'),
    'time': d.format('HH:mm'),
  };
};

export const dateMonthIndo = (date) => dayjs(date).format('DD MMMM');

export const dateDayIndo = (date) => dayjs(date).format('dddd, DD MMMM YYYY');

export const dateShortIndo = (date) => dayjs(date).format('DD/MM/YY');

export const dateTimeIndo = (datetime) => dayjs(datetime).format('dddd, DD MMMM YYYY HH:mm');

export const timeIndo = (datetime, retry = true) => {
  let time = dayjs(datetime).format('HH:mm');
  if (time === 'Invalid Date' && retry) {
    time = dayjs(dateNow() + ' ' + datetime, 'YYYY-MM-DD HH:mm').format('HH:mm');
  }
  return time;
};

export const monthOnly = (date) => dayjs(date).format('MMMM');

export const monthIndo = (date) => dayjs(date).format('MMMM YYYY');

export const addDay = (date, sum, unit = 'day') => dayjs(date).add(sum, unit).format('YYYY-MM-DD');

export const getStartAndEndOfMonth = (date) => ({
  startOfMonth: dayjs(date).startOf('month').format('YYYY-MM-DD'),
  endOfMonth: dayjs(date).endOf('month').format('YYYY-MM-DD'),
});

export const getStartAndEndOfWeek = (date = new Date()) => {
  const d = dayjs(date);
  const day = d.day();
  const diffToSaturday = day < 6 ? day : 0;
  const startOfWeek = d.subtract(diffToSaturday, 'day').startOf('day');
  const endOfWeek = startOfWeek.add(6, 'day').endOf('day');

  return {
    startOfWeek: startOfWeek.format('YYYY-MM-DD'),
    endOfWeek: endOfWeek.format('YYYY-MM-DD'),
  };
};

export const getDateRanges = (start, end) => {
  const dateArray = [];
  let current = dayjs(start);
  const stop = dayjs(end);

  while (current.isBefore(stop) || current.isSame(stop, 'day')) {
    dateArray.push(current.format('YYYY-MM-DD'));
    current = current.add(1, 'day');
  }
  return dateArray;
};

export const getWeeklyRanges = (lastDateStr, numberOfWeeks) => {
  const result = [];
  const lastDate = dayjs(lastDateStr);

  for (let i = 0; i < numberOfWeeks; i++) {
    const endOfWeek = lastDate.subtract(7 * i, 'day');
    const startOfWeek = endOfWeek.subtract(6, 'day');
    result.push({
      start: startOfWeek.format('YYYY-MM-DD'),
      end: endOfWeek.format('YYYY-MM-DD'),
    });
  }
  return result;
};

export const getMonthlyRanges = (lastDateStr, numberOfMonths) => {
  const result = [];
  let current = dayjs(lastDateStr).startOf('month');

  for (let i = 0; i < numberOfMonths; i++) {
    result.push({
      start: current.format('YYYY-MM-DD'),
      end: current.endOf('month').format('YYYY-MM-DD'),
    });
    current = current.subtract(1, 'month');
  }
  return result;
};

export const setLastDateOfMonth = (val) => (val ? dayjs(val).endOf('month').format('YYYY-MM-DD') : '');

export const monthList = () => {
  const months = [];
  for (let i = 0; i < 12; i++) {
    const monthName = monthOnly('2024-' + (i + 1) + '-01');
    months.push({
      value: (i + 1).toString().padStart(2, '0'),
      label: monthName,
    });
  }
  return months;
};

export const yearList = (count) => {
  const years = [];
  const currentYear = dayjs().year();
  // console.log(currentYear)
  for (let i = 0; i < count; i++) {
    // console.log(i)
    years.push({
      value: currentYear - i,
      label: currentYear - i,
    });
  }
  // console.log('years', count, years)
  return years;
};

export const hitungHariSenin = function (tahun, bulan) {
  if (typeof tahun == 'string') tahun = parseInt(tahun)
  if (typeof bulan == 'string') bulan = parseInt(bulan)
  // Dapatkan hari pertama di bulan tersebut (0 = Minggu, 1 = Senin, dst.)
  const hariPertama = new Date(tahun, bulan - 1, 1).getDay();
  // Dapatkan total hari dalam bulan tersebut
  const totalHari = new Date(tahun, bulan, 0).getDate();

  // Hitung berapa hari jarak ke Senin pertama
  const jarakKeSenin = (1 - hariPertama + 7) % 7;
  const seninPertama = 1 + jarakKeSenin;

  // Jika hari Senin pertama melebihi jumlah hari di bulan itu, return 0
  if (seninPertama > totalHari) return 0;

  // Rumus matematika menghitung sisa Senin
  return 1 + Math.floor((totalHari - seninPertama) / 7);
}