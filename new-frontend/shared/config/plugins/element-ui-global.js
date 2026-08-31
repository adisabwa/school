// import { ElNotification } from 'element-plus'

import { ElDatePicker } from 'element-plus';
import id from 'moment/src/locale/id';

import GetIcon from '@2/shared/components/Icon.vue'
import Loading from '@2/shared/components/Loading.vue'
import File from '@2/shared/components/File.vue'
import Star from '@2/shared/components/Star.vue'
import FloatingScroll from '@2/shared/components/FloatingScroll.vue'
import Form from '@2/shared/components/form/Form.vue'
import ViewTable from '@2/shared/components/form/ViewTable.vue'
import DateWheelPicker from '@2/shared/components/form/DateWheelPicker.vue'
import FloatingSelect from '@2/shared/components/form/FloatingSelect.vue'
import TableData from '@2/shared/components/table/TableData.vue'
import TableFreeze from '@2/shared/components/table/TableFreeze.vue'
import colors from '@2/shared/helpers/tailwindcolors'

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
    app.component('view-table', ViewTable)
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