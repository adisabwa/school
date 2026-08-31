<template>
  <div class="draggable-table-data-wrapper">
    <floating-scroll :key="floatKey"
      v-fixed-to-position="'90vh'"
      target="#table-base-wrapper"
      offset-y="90vh"
      width="95%"/>
    <table-data
      ref="tableDataRef"
      v-bind="$props"
      @update:checkedId="$emit('update:checkedId', $event)"
      @update:datasValue="handleDatasValueUpdate"
      @resetField="$emit('resetField')"
      @update:formValue="$emit('update:formValue', $event)"
      @changedFormValue="$emit('changedFormValue', $event)"
      @updateData="$emit('updateData', $event)"
      @onFormSaved="$emit('onFormSaved', $event)"
    >
      <template v-for="(_, slotName) in $slots" #[slotName]="slotProps">
        <slot :name="slotName" v-bind="slotProps ?? {}"></slot>
      </template>
    </table-data>
  </div>
</template>

<script>
import { nextTick } from 'vue';
import Sortable from 'sortablejs';
import TableData from './TableData.vue';

export default {
  name: "DraggableTableData",
  
  components: {
    TableData
  },

  props: {
    type: { type: String, default: '' },
    component: { type: String, default: '' },
    showCreate: { type: Boolean, default: true },
    showSearch: { type: Boolean, default: true },
    showDownload: { type: Boolean, default: false },
    showDropdown: { type: Boolean, default: true },
    showOrderText: { type: Boolean, default: true },
    showIndex: { type: Boolean, default: true },
    fields: { type: [Array, Object], default: () => ({}) },
    passColumns: { type: Array, default: () => [] },
    showColumns: { type: Array, default: () => [] },
    passColumnsInput: { type: Array, default: () => [] },
    showColumnsInput: { type: Array, default: () => [] },
    href: { type: String, default: '' },
    hrefStore: { type: String, default: null },
    hrefGet: { type: String, default: null },
    params: { type: Object, default: () => ({}) },
    checked: { type: Boolean, default: true },
    sortable: { type: Boolean, default: true },
    showUpload: { type: Boolean, default: true },
    showUploadNormal: { type: Boolean, default: false },
    title: { type: String, default: '' },
    checkedId: { type: Array, default: () => [] },
    headerVerticalAlign: { type: String, default: 'middle' },
    verticalAlign: { type: String, default: 'middle' },
    defaultValue: { type: [Array, Object], default: () => [] },
    formValue: { type: [Array, Object], default: () => [] },
    datasValue: { type: [Array, Object], default: () => [] },
    dropdownItemProps: { type: Object, default: () => ({}) },
    labelWidth: { type: [String, Number], default: '150px' },
    addValues: { type: [Object, Array], default: () => ({}) },
    labelClass: { type: String, default: '' },
    dataClass: { type: String, default: '' },
    eachLabelClass: { type: [Object, Array], default: () => ({}) },
    eachDataClass: { type: [Object, Array], default: () => ({}) },
    
    rowDraggable: { type: Boolean, default: true },
    useHandle: { type: [Boolean, String], default: false },
  },

  emits: [
    'update:checkedId',
    'update:datasValue', 
    'resetField',
    'update:formValue',
    'changedFormValue',
    'updateData', 
    'onFormSaved',
    'on-drag-end'
  ],

  data() {
    return {
      sortableInstance: null,
      localDatas: []
    };
  },

  watch: {
    // FIX 1: Push external updates from parent down into the child's hidden internal data array
    datasValue: {
      handler(newVal) {
        this.localDatas = [...newVal];
        
        const tableDataComponent = this.$refs.tableDataRef;
        if (tableDataComponent && tableDataComponent.datas) {
          // Check to prevent infinite reactive feedback loop
          if (JSON.stringify(tableDataComponent.datas) !== JSON.stringify(newVal)) {
            tableDataComponent.datas = [...newVal]; 
          }
        }
        
        if (this.rowDraggable) {
          this.initRowDrop();
        }
      },
      deep: true
    }
  },

  methods: {
    handleDatasValueUpdate(newVal) {
      this.localDatas = [...newVal];
      this.$emit('update:datasValue', newVal);
      
      if (this.rowDraggable) {
        this.initRowDrop();
      }
    },

    initRowDrop() {
      nextTick(() => {
        if (this.sortableInstance) {
          this.sortableInstance.destroy();
        }

        const el = this.$el.querySelector('.el-table__body-wrapper tbody');
        if (!el) return;

        const vm = this;
        this.sortableInstance = Sortable.create(el, {
          handle: this.useHandle ? (this.useHandle === true ? '.handle' : this.useHandle) : false, // handle's class
          ghostClass: 'sortable-ghost',
          animation: 150,
          onEnd(evt) {
            const { newIndex, oldIndex, from } = evt;
            if (newIndex === oldIndex) return;

            // FIX 2: DOM ROLLBACK TRICK
            // Instantly undo SortableJS's raw physical DOM manipulation.
            // This resets the HTML rows back to match Vue's current Virtual DOM.
            if (oldIndex < newIndex) {
              from.insertBefore(evt.item, from.children[oldIndex]);
            } else {
              from.insertBefore(evt.item, from.children[oldIndex + 1] || null);
            }

            // FIX 3: Direct internal state mutation
            const tableDataComponent = vm.$refs.tableDataRef;
            if (tableDataComponent && tableDataComponent.datas) {
              // Adjust indices for pagination alignment
              const offset = tableDataComponent.paging.offset || 0;
              const realOldIndex = oldIndex + offset;
              const realNewIndex = newIndex + offset;

              // Mutate the actual inner array so Vue cleanly repaints the rows and fixes index numbers
              const targetRow = tableDataComponent.datas.splice(realOldIndex, 1)[0];
              tableDataComponent.datas.splice(realNewIndex, 0, targetRow);

              // Push the freshly ordered dataset out to the parent
              const currentOrderedList = [...tableDataComponent.datas];
              vm.$emit('update:datasValue', currentOrderedList);
              vm.$emit('on-drag-end', currentOrderedList);
            }
          }
        });
      });
    },

    getData() {
      if (this.$refs.tableDataRef && typeof this.$refs.tableDataRef.getData === 'function') {
        this.$refs.tableDataRef.getData();
      }
    },
    handleActionClick(obj) {
      if (this.$refs.tableDataRef && typeof this.$refs.tableDataRef.handleActionClick === 'function') {
        this.$refs.tableDataRef.handleActionClick(obj);
      }
    }
  },

  beforeUnmount() {
    if (this.sortableInstance) {
      this.sortableInstance.destroy();
    }
  }
}
</script>

<style scoped>
:deep(.sortable-ghost) {
  opacity: 0.6;
  background-color: #ecf5ff !important;
  border: 1px dashed #409eff;
}
:deep(.el-table__row) {
  cursor: move;
}
</style>