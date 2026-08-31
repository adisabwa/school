// import { ElNotification } from 'element-plus'
import { defineAsyncComponent } from 'vue'

import GetIcon from '@/components/Icon.vue'
import Loading from '@/components/Loading.vue'
import File from '@/components/File.vue'
import Star from '@/components/Star.vue'
import FloatingScroll from '@/components/FloatingScroll.vue'
import DateWheelPicker from '@/components/form/DateWheelPicker.vue'
import FloatingSelect from '@/components/form/FloatingSelect.vue'

export default {
  install: (app) => {
    // inject a globally available $translate() method
    app.component('form-comp', defineAsyncComponent(() =>
      import('@/components/form/Form.vue')
    ))
    app.component('document-editor', defineAsyncComponent(() =>
      import('@/components/form/DocumentEditor.vue')
    ))
    app.component('view-table', defineAsyncComponent(() =>
      import('@/components/form/ViewTable.vue')
    ))
    app.component('table-data', defineAsyncComponent(() =>
      import('@/components/table/TableData.vue')
    ))
    app.component('table-freeze', defineAsyncComponent(() =>
      import('@/components/table/TableFreeze.vue')
    ))
    app.component('draggable-table-data', defineAsyncComponent(() =>
      import('@/components/table/DraggableTableData.vue')
    ))
  
    app.component('icons', GetIcon)
    app.component('loading', Loading)
    app.component('file', File)
    app.component('star', Star)
    app.component('date-wheel-picker', DateWheelPicker)
    app.component('floating-select', FloatingSelect)
    app.component('floating-scroll', FloatingScroll)
    app.config.globalProperties.$notify = ElNotification
    app.config.globalProperties.$msgbox = ElMessageBox
    app.config.globalProperties.$alert = ElMessageBox.alert
    app.config.globalProperties.$confirm = ElMessageBox.confirm
    app.config.globalProperties.$propmt = ElMessageBox.prompt
  }
}