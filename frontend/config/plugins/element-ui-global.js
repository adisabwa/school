// import { ElNotification } from 'element-plus'

import { ElDatePicker } from 'element-plus';
import id from 'moment/src/locale/id';

import GetIcon from '@/components/Icon.vue'
import Loading from '@/components/Loading.vue'
import File from '@/components/File.vue'
import Form from '@/components/Form.vue'
import Star from '@/components/Star.vue'
import DateWheelPicker from '@/components/form-components/DateWheelPicker.vue'
import FloatingSelect from '@/components/form-components/FloatingSelect.vue'

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
    app.config.globalProperties.$notify = ElNotification
    app.config.globalProperties.$msgbox = ElMessageBox
    app.config.globalProperties.$alert = ElMessageBox.alert
    app.config.globalProperties.$confirm = ElMessageBox.confirm
    app.config.globalProperties.$propmt = ElMessageBox.prompt
  }
}