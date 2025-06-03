
import moment from 'moment/src/moment';
import id from 'moment/src/locale/id';

moment.updateLocale('id', id);

let listFunction = {
    dateNow() {
      return moment().format('yyyy-MM-DD');
    },
    dateData(string) {
      const formats = [
        'YYYY-MM-DD',
        'DD-MM-YYYY',
        'DD/MM/YYYY',
        'MM/DD/YYYY',
        'DD MMMM YYYY',
        'dddd, DD MMMM YYYY',
        'MMMM DD, YYYY',
        'YYYY/MM/DD',
        'DD.MM.YYYY'
      ]
    
      const date = moment(string, formats, true)
    
      if (date.isValid()) {
        return date.format('YYYY-MM-DD')
      } else {
        return 'Invalid date'
      }    
    },
    formatDate(date, format){
      return moment(date).format(format);
    },
    dayIndo(date) {
      return moment(date).format('dddd');
    },
    dateIndo(date) {
      return moment(date).format('DD MMMM yyyy');
    },
    dateDayIndoSeparate(date) {
      let data = moment(date).format('dddd DD MMM MM MMMM yyyy HH:mm');
      let keys = ['long-day','day','short-month','month','long-month','year','time'];
      data = data.split(' ');
      let result = {};
      for (var i = 0; i < data.length; i++) {
        data[i] = data[i].trim()
        result[keys[i]] = data[i];
      }
      return result
    },
    dateMonthIndo(date) {
      return moment(date).format('DD MMMM');
    },
    dateDayIndo(date) {
      return moment(date).format('dddd, DD MMMM YYYY');
    },
    dateShortIndo(date) {
      return moment(date).format('DD/MM/YY');
    },
    dateTimeIndo(datetime) {
      return moment(datetime).format('dddd, DD MMMM YYYY HH:mm');
    },
    timeIndo(datetime) {
      return moment(datetime).format('HH:mm');
    },
    monthOnly(date) {
      return moment(date).format('MMMM');
    },
    monthIndo(date) {
      return moment(date).format('MMMM YYYY');
    },
    addDay(date, sum, unit = 'days'){
      return moment(date).add(sum, unit).format('yyyy-MM-DD');
    },
    getWeeklyRanges(lastDateStr, numberOfWeeks) {
      const result = [];
      const lastDate = new Date(lastDateStr);
  
      for (let i = 0; i < numberOfWeeks; i++) {
          // Clone the date to avoid modifying lastDate
          const endOfWeek = new Date(lastDate);
          endOfWeek.setDate(lastDate.getDate() - (7 * i));
  
          const startOfWeek = new Date(endOfWeek);
          startOfWeek.setDate(endOfWeek.getDate() - 6);
  
          result.push({
              start: startOfWeek.toISOString().slice(0, 10),
              end: endOfWeek.toISOString().slice(0, 10)
          });
      }
  
      return result;
    },
    getMonthlyRanges(lastDateStr, numberOfMonths) {
      const result = [];
      let current = new Date(lastDateStr);
  
      // Normalize to first of the month
      current.setDate(1);
  
      for (let i = 0; i < numberOfMonths; i++) {
          const startOfMonth = new Date(current);
          
          // Get last day of current month
          const endOfMonth = new Date(current.getFullYear(), current.getMonth() + 1, 0);
  
          result.push({
              start: startOfMonth.toISOString().slice(0, 10),
              end: endOfMonth.toISOString().slice(0, 10)
          });
  
          // Move to first day of next month
          current.setMonth(current.getMonth() - 1);
      }
  
      return result;
    },
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