// import { ElNotification } from 'element-plus'

import { ElDatePicker } from 'element-plus';
import id from 'moment/src/locale/id';

import GetIcon from '@/components/Icon.vue'
import Loading from '@/components/Loading.vue'
import File from '@/components/File.vue'
import Star from '@/components/Star.vue'
import FloatingScroll from '@/components/FloatingScroll.vue'
import Form from '@/components/form/Form.vue'
import DateWheelPicker from '@/components/form/DateWheelPicker.vue'
import FloatingSelect from '@/components/form/FloatingSelect.vue'
import TableData from '@/components/table/TableData.vue'
import TableFreeze from '@/components/table/TableFreeze.vue'
import colors from '@/helpers/tailwindcolors'

function getColor(code) {
  return colors[code]
}

export default {
  install: (app) => {
    // inject a globally available $translate() method
    app.use(ElDatePicker, { id });
    app.component('icons', GetIcon)
    app.component('loading', Loading)
    app.component('file', File)
    app.component('star', Star)
    app.component('date-wheel-picker', DateWheelPicker)
    app.component('floating-select', FloatingSelect)
    app.component('form-comp', Form)
    app.component('table-data', TableData)
    app.component('table-freeze', TableFreeze)
    app.component('floating-scroll', FloatingScroll)
    app.config.globalProperties.$notify = ElNotification
    app.config.globalProperties.$msgbox = ElMessageBox
    app.config.globalProperties.$alert = ElMessageBox.alert
    app.config.globalProperties.$confirm = ElMessageBox.confirm
    app.config.globalProperties.$propmt = ElMessageBox.prompt
    app.config.globalProperties.getColor = getColor
  }
}